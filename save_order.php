<?php
require_once __DIR__ . '/includes/connection.php';
start_secure_session();
header('Content-Type: application/json');

/* -------------------------
   Telegram helper function
-------------------------- */
function sendTelegramMessage($chat_id, $bot_token, $text, $replyMarkup = null)
{
    $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";

    $data = [
        'chat_id'    => $chat_id,
        'text'       => $text,
        'parse_mode' => 'HTML'
    ];
    if ($replyMarkup !== null) {
        $data['reply_markup'] = json_encode($replyMarkup, JSON_UNESCAPED_UNICODE);
    }

    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($data),
            'timeout' => 15,
            'ignore_errors' => true,
        ]
    ];

    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);

    if ($result === false) {
        $lastError = error_get_last();
        $error = $lastError['message'] ?? 'Telegram request failed';
        error_log("Telegram API Error: " . $error);
        return ['ok' => false, 'description' => $error];
    }

    $response = json_decode($result, true);
    if (!isset($response['ok']) || !$response['ok']) {
        $description = $response['description'] ?? 'Unknown Telegram API error';
        error_log("Telegram API Error: " . $description);
        return ['ok' => false, 'description' => $description];
    }

    return ['ok' => true, 'description' => ''];
}

function buildOrderTelegramMessage(array $order, bool $includeSmsButton = true): array
{
    $orderId = (int)$order['id'];
    $customerName = (string)($order['customer_name'] ?? 'Customer');
    $customerPhone = (string)($order['whatsapp_number'] ?? '');
    $requestedTime = (string)($order['requested_time'] ?? '');
    $totalUsd = (float)($order['total_usd'] ?? 0);
    $notes = (string)($order['notes'] ?? '');
    $items = $order['items'] ?? [];
    if (is_string($items)) {
        $items = json_decode($items, true) ?: [];
    }

    $escape = static function ($value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    };
    $lines = [
        "🛎️ <b>New Order #{$orderId}</b>",
        '<b>Name:</b> ' . $escape($customerName),
        '<b>Phone:</b> ' . $escape($customerPhone),
        '<b>Requested time:</b> ' . ($requestedTime !== '' ? $escape($requestedTime) : 'ASAP'),
    ];

    if (is_array($items) && $items) {
        $lines[] = '';
        $lines[] = '<b>Items:</b>';
        foreach ($items as $item) {
            $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 1;
            $lines[] = '• ' . $quantity . ' x ' . $escape($item['name'] ?? 'Item');
        }
    }
    $lines[] = '';
    $lines[] = '<b>Total:</b> $' . number_format($totalUsd, 2);
    if ($notes !== '') {
        $lines[] = '';
        $lines[] = '<b>Notes:</b> ' . $escape($notes);
    }

    $replyMarkup = null;
    $phoneDigits = preg_replace('/\D+/', '', $customerPhone);
    if ($includeSmsButton && $phoneDigits !== '') {
        $smsBody = "Hi {$customerName}, we received your order #{$orderId}. We’ll be in touch shortly to confirm.";
        $smsUrl = 'https://www.nabilmediterraneanfood.com/sms_redirect.php?' . http_build_query([
            'to' => $phoneDigits,
            'msg' => $smsBody,
        ]);
        $replyMarkup = ['inline_keyboard' => [[['text' => '📩 Confirm with customer', 'url' => $smsUrl]]]];
    }

    return [implode("\n", $lines), $replyMarkup];
}

function sendOrderTelegramNotification(mysqli $conn, int $orderId): array
{
    $orderStmt = $conn->prepare('SELECT id, customer_name, whatsapp_number, total_usd, notes, requested_time, items FROM orders WHERE id = ?');
    $orderStmt->bind_param('i', $orderId);
    $orderStmt->execute();
    $order = $orderStmt->get_result()->fetch_assoc();
    $orderStmt->close();
    if (!$order) {
        return ['ok' => false, 'description' => 'Order not found'];
    }

    $settingsResult = $conn->query('SELECT chat_id, bot_token FROM settings LIMIT 1');
    $settings = $settingsResult ? $settingsResult->fetch_assoc() : null;
    $chatId = trim((string)($settings['chat_id'] ?? ''));
    $botToken = trim((string)($settings['bot_token'] ?? ''));
    if ($chatId === '' || $botToken === '') {
        $result = ['ok' => false, 'description' => 'Telegram chat ID or bot token is not configured'];
    } else {
        [$message, $replyMarkup] = buildOrderTelegramMessage($order);
        $result = sendTelegramMessage($chatId, $botToken, $message, $replyMarkup);
    }

    $errorText = (string)$result['description'];
    $error = $result['ok'] ? null : (function_exists('mb_substr') ? mb_substr($errorText, 0, 1000) : substr($errorText, 0, 1000));
    $status = $result['ok'] ? 'sent' : 'pending';
    $update = $conn->prepare('UPDATE orders SET telegram_status = ?, telegram_notified_at = ?, telegram_last_error = ?, telegram_attempts = telegram_attempts + 1, telegram_last_attempt_at = NOW(), telegram_next_attempt_at = ? WHERE id = ?');
    $notifiedAt = $result['ok'] ? date('Y-m-d H:i:s') : null;
    $nextAttempt = $result['ok'] ? null : date('Y-m-d H:i:s', time() + 60);
    $update->bind_param('ssssi', $status, $notifiedAt, $error, $nextAttempt, $orderId);
    $update->execute();
    $update->close();

    return $result;
}

if (defined('TELEGRAM_ORDER_WORKER')) {
    return;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'POST required']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
    exit;
}

$customer_name = trim($input['customer_name'] ?? '');
$customer_phone = trim($input['customer_phone'] ?? '');
$notes = isset($input['notes']) ? trim($input['notes']) : null;
if ($notes === '') {
    $notes = null;
}
$requested_time = isset($input['requested_time']) ? trim($input['requested_time']) : '';
// Expecting HH:MM (24h) from the <input type="time">; anything else is treated as ASAP.
if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $requested_time)) {
    $requested_time = null;
}
$items = $input['items'] ?? [];
$total_usd = (float)($input['total_usd'] ?? 0);

if (empty($customer_name) || empty($customer_phone) || empty($items)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

// Store a clean copy of the items (name, quantity, price) as JSON for the dashboard
$items_clean = array_map(function ($item) {
    return [
        'name'     => $item['name'] ?? 'Item',
        'quantity' => isset($item['quantity']) ? (int)$item['quantity'] : 1,
        'price'    => isset($item['priceUsd']) ? (float)$item['priceUsd'] : null,
    ];
}, $items);
$items_json = json_encode($items_clean, JSON_UNESCAPED_UNICODE);

$stmt = $conn->prepare("INSERT INTO orders (customer_name, whatsapp_number, total_usd, notes, requested_time, items, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
$stmt->bind_param("ssdsss", $customer_name, $customer_phone, $total_usd, $notes, $requested_time, $items_json);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id;
    $delivery = sendOrderTelegramNotification($conn, (int)$order_id);
    echo json_encode(['success' => true, 'order_id' => $order_id]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $conn->error]);
}
$stmt->close();
