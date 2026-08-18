<?php
require_once __DIR__ . '/includes/connection.php';
start_secure_session();
header('Content-Type: application/json');

/* -------------------------
   Telegram helper function
-------------------------- */
function sendTelegramMessage($chat_id, $bot_token, $text)
{
    $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";

    $data = [
        'chat_id'    => $chat_id,
        'text'       => $text,
        'parse_mode' => 'HTML'
    ];

    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($data),
            'timeout' => 10
        ]
    ];

    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);

    if ($result === false) {
        error_log("Telegram API Error: request failed");
        return false;
    }

    $response = json_decode($result, true);
    if (!isset($response['ok']) || !$response['ok']) {
        error_log("Telegram API Error: " . ($response['description'] ?? 'Unknown error'));
        return false;
    }

    return true;
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
    echo json_encode(['success' => true, 'order_id' => $order_id]);

    // Notify via Telegram (if configured), after responding to the client.
    $settings = get_settings();
    $telegram_chat_id = $settings['chat_id'] ?? '';
    $telegram_bot_token = $settings['bot_token'] ?? '';

    if ($telegram_chat_id && $telegram_bot_token) {
        $lines = [];
        $lines[] = "🛎️ <b>New Order #{$order_id}</b>";
        $lines[] = "<b>Name:</b> " . htmlspecialchars($customer_name);
        $lines[] = "<b>Phone:</b> " . htmlspecialchars($customer_phone);
        $lines[] = "<b>Requested time:</b> " . ($requested_time ? htmlspecialchars($requested_time) : 'ASAP');

        if (!empty($items) && is_array($items)) {
            $lines[] = "";
            $lines[] = "<b>Items:</b>";
            foreach ($items as $item) {
                $qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;
                $iname = htmlspecialchars($item['name'] ?? 'Item');
                $lines[] = "• {$qty} x {$iname}";
            }
        }

        if ($total_usd > 0) {
            $lines[] = "";
            $lines[] = "<b>Total:</b> $" . number_format($total_usd, 2);
        }

        if (!empty($notes)) {
            $lines[] = "";
            $lines[] = "<b>Notes:</b> " . htmlspecialchars($notes);
        }

        $telegram_text = implode("\n", $lines);
        sendTelegramMessage($telegram_chat_id, $telegram_bot_token, $telegram_text);
    }
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $conn->error]);
}
$stmt->close();