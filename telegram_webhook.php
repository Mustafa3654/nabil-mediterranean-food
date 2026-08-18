<?php
/**
 * Telegram Webhook — your website assistant.
 *
 * Every message is handled by the AI assistant (DeepSeek), which can:
 *   - Update order status via natural language: "15 ready", "cancel order 12"
 *   - Answer questions: "list pending orders", "how many cancelled today"
 *   - Manage the menu: add items, add categories, change prices
 *   DESTRUCTIVE actions (deleting an item or category) never happen
 *   immediately — the bot asks you to reply YES to confirm first.
 *
 * When an order is marked "Ready for Pickup", the bot automatically attaches
 * a one-tap "Text customer" button that opens the phone's Messages app with
 * a pre-filled "your order is ready" text to that customer's number.
 *
 * Security:
 *  1. Telegram sends a secret header we set during setWebhook — requests
 *     without the correct header are rejected.
 *  2. We only act on messages from the chat_id configured in your
 *     Settings > Telegram tab.
 *  3. The AI can only act through the specific tools defined below — it
 *     cannot run arbitrary SQL or touch anything outside them.
 */

require_once __DIR__ . '/includes/connection.php';
header('Content-Type: application/json');

// ---- 1. Verify the secret token Telegram sends in the header ----
define('WEBHOOK_SECRET', '22672360caac9abaf8b34b255ce7bccf84ef88df4f19340c');

$sentSecret = $_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] ?? '';
if (!hash_equals(WEBHOOK_SECRET, $sentSecret)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'forbidden']);
    exit;
}

// ---- 2. Parse the incoming Telegram update ----
$raw = file_get_contents('php://input');
$update = json_decode($raw, true);

$message = $update['message'] ?? $update['edited_message'] ?? null;
if (!$message || empty($message['text'])) {
    echo json_encode(['ok' => true]);
    exit;
}

$text = trim($message['text']);
$incomingChatId = (string)($message['chat']['id'] ?? '');

// ---- 3. Load settings ----
$settingsRow = $conn->query("SELECT * FROM settings LIMIT 1")->fetch_assoc();
$botToken = $settingsRow['bot_token'] ?? '';
$authorizedChatId = (string)($settingsRow['chat_id'] ?? '');

if (!$botToken) {
    echo json_encode(['ok' => true]);
    exit;
}

function sendTelegramReply($chatId, $botToken, $text, $parseMode = 'HTML', $buttonText = null, $buttonUrl = null)
{
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $data = ['chat_id' => $chatId, 'text' => $text];
    if ($parseMode) {
        $data['parse_mode'] = $parseMode;
    }
    if ($buttonText && $buttonUrl) {
        $data['reply_markup'] = json_encode([
            'inline_keyboard' => [[ ['text' => $buttonText, 'url' => $buttonUrl] ]]
        ]);
    }
    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($data),
            'timeout' => 10,
            'ignore_errors' => true, // still read the response body on 4xx/5xx
        ]
    ];
    $result = @file_get_contents($url, false, stream_context_create($options));
    $ok = $result ? (json_decode($result, true)['ok'] ?? false) : false;

    // Safety net: if sending with a button failed for any reason (e.g. Telegram
    // rejects the button URL), retry as plain text so the core message still
    // reaches the owner instead of failing completely silently.
    if (!$ok && $buttonText && $buttonUrl) {
        error_log("Telegram sendMessage with button failed, retrying without button: " . ($result ?: 'no response'));
        unset($data['reply_markup']);
        $options['http']['content'] = http_build_query($data);
        @file_get_contents($url, false, stream_context_create($options));
    }
}

if (!$authorizedChatId || $incomingChatId !== $authorizedChatId) {
    echo json_encode(['ok' => true]);
    exit;
}

// ---- 3.5. Check for a pending confirmation (delete item/category) ----
// (Handled further below, after the AI helper is defined, so the outcome
// message is composed by the AI rather than hardcoded here.)
$conn->query("DELETE FROM bot_pending_actions WHERE created_at < (NOW() - INTERVAL 10 MINUTE)");

$pendingStmt = $conn->prepare("SELECT * FROM bot_pending_actions WHERE chat_id = ? ORDER BY created_at DESC LIMIT 1");
$pendingStmt->bind_param("s", $incomingChatId);
$pendingStmt->execute();
$pending = $pendingStmt->get_result()->fetch_assoc();
$pendingStmt->close();

$deepseekKey = $settingsRow['deepseek_api_key'] ?? '';
$deepseekModel = $settingsRow['deepseek_model'] ?? 'deepseek-v4-flash';

if (!$deepseekKey) {
    // Genuine exception: there is no AI to ask, so this one line cannot be
    // AI-composed. Every other reply in this file comes from the AI.
    sendTelegramReply($incomingChatId, $botToken,
        "⚠️ The AI assistant isn't configured yet — add a DeepSeek API key in Settings > Telegram.");
    echo json_encode(['ok' => true]);
    exit;
}

if ($pending) {
    $lower = mb_strtolower(trim($text), 'UTF-8');
    $yesWords = ['yes', 'y', 'confirm', 'confirmed', 'ok', 'okay', 'yep', 'yeah', 'ايوا', 'اي', 'أكد', 'نعم', 'تمام'];
    $noWords  = ['no', 'n', 'cancel', 'nvm', 'stop', 'لا', 'لأ', 'كنسل'];

    if (in_array($lower, $yesWords) || in_array($lower, $noWords)) {
        $situation = null;

        if (in_array($lower, $yesWords)) {
            $payload = json_decode($pending['payload'], true);

            if ($pending['action_type'] === 'delete_item') {
                $stmt = $conn->prepare("DELETE FROM items WHERE item_id = ?");
                $stmt->bind_param("i", $payload['item_id']);
                $stmt->execute();
                $stmt->close();
                $situation = "The owner confirmed deleting menu item \"{$payload['item_name']}\" (ID {$payload['item_id']}). It has been deleted. Confirm this briefly.";
            } elseif ($pending['action_type'] === 'delete_category') {
                $stmt = $conn->prepare("DELETE FROM categories WHERE cat_id = ?");
                $stmt->bind_param("i", $payload['cat_id']);
                $stmt->execute();
                $stmt->close();
                $situation = "The owner confirmed deleting category \"{$payload['cat_name']}\" (ID {$payload['cat_id']}). It has been deleted (items in it were not deleted). Confirm this briefly.";
            }
        } else {
            $situation = "The owner declined this pending action: \"{$pending['summary']}\". Nothing was deleted or changed. Confirm briefly that it was cancelled.";
        }

        $delStmt = $conn->prepare("DELETE FROM bot_pending_actions WHERE id = ?");
        $delStmt->bind_param("i", $pending['id']);
        $delStmt->execute();
        $delStmt->close();

        $aiReply = composeAiReply($deepseekKey, $deepseekModel, $situation);
        sendTelegramReply($incomingChatId, $botToken, $aiReply ?? "Done.", null);
        echo json_encode(['ok' => true]);
        exit;
    }
    // Any other message: leave the pending confirmation in place (it expires
    // after 10 minutes) and keep processing normally below.
}

/**
 * Lightweight AI call (no tools) used to phrase a short reply describing
 * an outcome that already happened in code (e.g. a confirmed deletion).
 * Returns null on failure so the caller can fall back gracefully.
 */
function composeAiReply($apiKey, $model, $situation)
{
    $systemPrompt = "You are the website assistant for a restaurant, replying to the owner over Telegram. "
        . "Something already happened in the system — phrase a short, natural confirmation message about it, "
        . "like a text message. No headers, no markdown. Match the owner's language/style if given context suggests it.";

    $payload = [
        'model' => $model,
        'messages' => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $situation],
        ],
    ];
    if (in_array($model, ['deepseek-v4-pro', 'deepseek-v4-flash'])) {
        $payload['thinking'] = ['type' => 'enabled'];
    }

    $ch = curl_init('https://api.deepseek.com/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $apiKey],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 20,
    ]);
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err || !$response) { error_log("DeepSeek composeAiReply error: $err"); return null; }
    $data = json_decode($response, true);
    return $data['choices'][0]['message']['content'] ?? null;
}

/**
 * AI assistant (DeepSeek, OpenAI-compatible function calling).
 * Can read orders/items/categories, add items/categories, update prices,
 * and update order status immediately. Deleting an item or category never
 * happens directly — it creates a pending confirmation instead.
 */
function handleAiAssistant($text, $conn, $apiKey, $model, $chatId, $botToken)
{
    $statusLabels = [
        'pending'   => 'Pending',
        'sent'      => 'Ready for Pickup',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    // Set by the update_order_status tool when an order becomes "Ready for
    // Pickup", so we can attach the one-tap SMS button to the final reply.
    $smsButton = null;

    $tools = [
        [ 'type' => 'function', 'function' => [
            'name' => 'list_orders',
            'description' => 'List recent orders, optionally filtered by status and/or date range.',
            'parameters' => [ 'type' => 'object', 'properties' => [
                'status' => ['type' => 'string', 'enum' => ['pending', 'sent', 'completed', 'cancelled'], 'description' => 'sent = Ready for Pickup'],
                'date_from' => ['type' => 'string', 'description' => 'YYYY-MM-DD, optional'],
                'date_to' => ['type' => 'string', 'description' => 'YYYY-MM-DD, optional'],
                'limit' => ['type' => 'integer', 'description' => 'Max rows, default 20, max 50'],
            ]],
        ]],
        [ 'type' => 'function', 'function' => [
            'name' => 'count_orders',
            'description' => 'Count orders, optionally filtered by status and/or date range.',
            'parameters' => [ 'type' => 'object', 'properties' => [
                'status' => ['type' => 'string', 'enum' => ['pending', 'sent', 'completed', 'cancelled']],
                'date_from' => ['type' => 'string', 'description' => 'YYYY-MM-DD, optional'],
                'date_to' => ['type' => 'string', 'description' => 'YYYY-MM-DD, optional'],
            ]],
        ]],
        [ 'type' => 'function', 'function' => [
            'name' => 'update_order_status',
            'description' => 'Change the status of one order.',
            'parameters' => [ 'type' => 'object', 'properties' => [
                'order_id' => ['type' => 'integer'],
                'status' => ['type' => 'string', 'enum' => ['pending', 'sent', 'completed', 'cancelled']],
            ], 'required' => ['order_id', 'status']],
        ]],
        [ 'type' => 'function', 'function' => [
            'name' => 'list_categories',
            'description' => 'List all menu categories.',
            'parameters' => [ 'type' => 'object', 'properties' => new stdClass() ],
        ]],
        [ 'type' => 'function', 'function' => [
            'name' => 'list_items',
            'description' => 'List menu items, optionally filtered by category name.',
            'parameters' => [ 'type' => 'object', 'properties' => [
                'category' => ['type' => 'string', 'description' => 'Category name, optional'],
            ]],
        ]],
        [ 'type' => 'function', 'function' => [
            'name' => 'add_category',
            'description' => 'Add a new menu category.',
            'parameters' => [ 'type' => 'object', 'properties' => [
                'name' => ['type' => 'string'],
            ], 'required' => ['name']],
        ]],
        [ 'type' => 'function', 'function' => [
            'name' => 'add_item',
            'description' => 'Add a new menu item to an existing category.',
            'parameters' => [ 'type' => 'object', 'properties' => [
                'name' => ['type' => 'string'],
                'category' => ['type' => 'string', 'description' => 'Must match an existing category name — call list_categories first if unsure'],
                'price' => ['type' => 'number'],
                'price_suffix' => ['type' => 'string', 'description' => 'e.g. "/lb", "LG" — optional'],
                'ingredients' => ['type' => 'string', 'description' => 'optional'],
            ], 'required' => ['name', 'category', 'price']],
        ]],
        [ 'type' => 'function', 'function' => [
            'name' => 'update_item_price',
            'description' => 'Change the price of an existing item. Identify it by item_id if known, otherwise by name.',
            'parameters' => [ 'type' => 'object', 'properties' => [
                'item_id' => ['type' => 'integer'],
                'item_name' => ['type' => 'string'],
                'new_price' => ['type' => 'number'],
            ], 'required' => ['new_price']],
        ]],
        [ 'type' => 'function', 'function' => [
            'name' => 'request_delete_item',
            'description' => 'Request deletion of a menu item. This does NOT delete immediately — it asks the owner to confirm first. Identify by item_id if known, otherwise by name.',
            'parameters' => [ 'type' => 'object', 'properties' => [
                'item_id' => ['type' => 'integer'],
                'item_name' => ['type' => 'string'],
            ]],
        ]],
        [ 'type' => 'function', 'function' => [
            'name' => 'request_delete_category',
            'description' => 'Request deletion of a category. This does NOT delete immediately — it asks the owner to confirm first. Items in the category are not deleted, only the category itself.',
            'parameters' => [ 'type' => 'object', 'properties' => [
                'cat_id' => ['type' => 'integer'],
                'cat_name' => ['type' => 'string'],
            ]],
        ]],
    ];

    $runTool = function ($name, $args) use ($conn, $statusLabels, $chatId, &$smsButton) {
        // ---- Orders: read ----
        if ($name === 'list_orders' || $name === 'count_orders') {
            $where = "1=1"; $params = []; $types = "";
            if (!empty($args['status'])) { $where .= " AND status = ?"; $params[] = $args['status']; $types .= "s"; }
            if (!empty($args['date_from'])) { $where .= " AND DATE(created_at) >= ?"; $params[] = $args['date_from']; $types .= "s"; }
            if (!empty($args['date_to'])) { $where .= " AND DATE(created_at) <= ?"; $params[] = $args['date_to']; $types .= "s"; }

            if ($name === 'count_orders') {
                $stmt = $conn->prepare("SELECT COUNT(*) AS c FROM orders WHERE $where");
                if ($types) $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $count = $stmt->get_result()->fetch_assoc()['c'];
                $stmt->close();
                return json_encode(['count' => (int)$count]);
            }
            $limit = min(50, max(1, (int)($args['limit'] ?? 20)));
            $stmt = $conn->prepare("SELECT id, customer_name, whatsapp_number, total_usd, status, created_at FROM orders WHERE $where ORDER BY created_at DESC LIMIT $limit");
            if ($types) $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $res = $stmt->get_result();
            $out = [];
            while ($row = $res->fetch_assoc()) {
                $out[] = ['id' => (int)$row['id'], 'customer_name' => $row['customer_name'], 'phone' => $row['whatsapp_number'],
                    'total_usd' => (float)$row['total_usd'], 'status' => $statusLabels[$row['status']] ?? $row['status'], 'created_at' => $row['created_at']];
            }
            $stmt->close();
            return json_encode($out);
        }

        // ---- Orders: update status (immediate, reversible) ----
        if ($name === 'update_order_status') {
            $orderId = (int)($args['order_id'] ?? 0);
            $status = $args['status'] ?? '';
            if (!$orderId || !in_array($status, ['pending', 'sent', 'completed', 'cancelled'])) {
                return json_encode(['error' => 'missing or invalid order_id/status']);
            }
            $check = $conn->prepare("SELECT id, customer_name, whatsapp_number FROM orders WHERE id = ?");
            $check->bind_param("i", $orderId);
            $check->execute();
            $order = $check->get_result()->fetch_assoc();
            $check->close();
            if (!$order) return json_encode(['error' => "order #$orderId not found"]);

            if ($status === 'completed') {
                $upd = $conn->prepare("UPDATE orders SET status = 'completed', completed_at = NOW() WHERE id = ?");
                $upd->bind_param("i", $orderId);
            } else {
                $upd = $conn->prepare("UPDATE orders SET status = ?, completed_at = NULL WHERE id = ?");
                $upd->bind_param("si", $status, $orderId);
            }
            $upd->execute();
            $upd->close();

            // When an order becomes Ready for Pickup, prepare a one-tap SMS
            // button for the final reply. Telegram only allows http(s)/tg://
            // links on buttons, so we route through a small https redirect
            // page that hands off to the phone's native sms: link.
            if ($status === 'sent' && !empty($order['whatsapp_number'])) {
                $digits = preg_replace('/\D/', '', $order['whatsapp_number']);
                if ($digits) {
                    $smsBody = "Hi " . $order['customer_name'] . ", your order #{$orderId} is ready for pickup!";
                    $siteBase = "https://" . $_SERVER['HTTP_HOST'];
                    $smsButton = [
                        'text' => "📱 Text customer: order ready",
                        'url' => $siteBase . "/sms_redirect.php?to=" . urlencode($digits) . "&msg=" . urlencode($smsBody),
                    ];
                }
            }

            return json_encode(['success' => true, 'order_id' => $orderId, 'customer_name' => $order['customer_name'], 'new_status' => $statusLabels[$status]]);
        }

        // ---- Categories: read ----
        if ($name === 'list_categories') {
            $res = $conn->query("SELECT cat_id, cat_name FROM categories ORDER BY `Order` ASC");
            $out = [];
            while ($row = $res->fetch_assoc()) $out[] = ['cat_id' => (int)$row['cat_id'], 'cat_name' => $row['cat_name']];
            return json_encode($out);
        }

        // ---- Categories: add (immediate, reversible via delete) ----
        if ($name === 'add_category') {
            $catName = trim($args['name'] ?? '');
            if ($catName === '') return json_encode(['error' => 'name required']);
            $exists = $conn->prepare("SELECT cat_id FROM categories WHERE LOWER(cat_name) = LOWER(?)");
            $exists->bind_param("s", $catName);
            $exists->execute();
            if ($exists->get_result()->fetch_assoc()) { $exists->close(); return json_encode(['error' => 'a category with that name already exists']); }
            $exists->close();
            $stmt = $conn->prepare("INSERT INTO categories (cat_name, cat_picture, cat_icon, cat_footer, cat_footer_bottom, `Order`) VALUES (?, '', '', '', '', 0)");
            $stmt->bind_param("s", $catName);
            $stmt->execute();
            $newId = $conn->insert_id;
            $stmt->close();
            return json_encode(['success' => true, 'cat_id' => $newId, 'cat_name' => $catName]);
        }

        // ---- Categories: request delete (needs confirmation) ----
        if ($name === 'request_delete_category') {
            $cat = null;
            if (!empty($args['cat_id'])) {
                $stmt = $conn->prepare("SELECT cat_id, cat_name FROM categories WHERE cat_id = ?");
                $stmt->bind_param("i", $args['cat_id']);
            } elseif (!empty($args['cat_name'])) {
                $needle = $args['cat_name'];
                $stmt = $conn->prepare("SELECT cat_id, cat_name FROM categories WHERE cat_name LIKE ?");
                $like = "%$needle%";
                $stmt->bind_param("s", $like);
            } else {
                return json_encode(['error' => 'cat_id or cat_name required']);
            }
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows === 0) { $stmt->close(); return json_encode(['error' => 'category not found']); }
            if ($res->num_rows > 1) {
                $matches = [];
                while ($r = $res->fetch_assoc()) $matches[] = $r;
                $stmt->close();
                return json_encode(['error' => 'multiple categories matched, be more specific', 'matches' => $matches]);
            }
            $cat = $res->fetch_assoc();
            $stmt->close();

            $summary = "Delete category \"{$cat['cat_name']}\"? Items in it will stay on the menu but become uncategorized. Reply YES to confirm or NO to cancel.";
            $payload = json_encode(['cat_id' => (int)$cat['cat_id'], 'cat_name' => $cat['cat_name']]);

            $del = $conn->prepare("DELETE FROM bot_pending_actions WHERE chat_id = ?");
            $del->bind_param("s", $chatId);
            $del->execute(); $del->close();

            $ins = $conn->prepare("INSERT INTO bot_pending_actions (chat_id, action_type, payload, summary) VALUES (?, 'delete_category', ?, ?)");
            $ins->bind_param("sss", $chatId, $payload, $summary);
            $ins->execute(); $ins->close();

            return json_encode(['status' => 'awaiting_confirmation', 'message' => $summary]);
        }

        // ---- Items: read ----
        if ($name === 'list_items') {
            if (!empty($args['category'])) {
                $stmt = $conn->prepare("SELECT item_id, item_name, item_category, item_priceusd, price_suffix FROM items WHERE item_category = ? ORDER BY `Order` ASC");
                $stmt->bind_param("s", $args['category']);
            } else {
                $stmt = $conn->prepare("SELECT item_id, item_name, item_category, item_priceusd, price_suffix FROM items ORDER BY item_category, `Order` ASC");
            }
            $stmt->execute();
            $res = $stmt->get_result();
            $out = [];
            while ($row = $res->fetch_assoc()) {
                $out[] = ['item_id' => (int)$row['item_id'], 'name' => $row['item_name'], 'category' => $row['item_category'],
                    'price' => (float)$row['item_priceusd'], 'price_suffix' => $row['price_suffix']];
            }
            $stmt->close();
            return json_encode($out);
        }

        // ---- Items: add (immediate, reversible via delete) ----
        if ($name === 'add_item') {
            $itemName = trim($args['name'] ?? '');
            $category = trim($args['category'] ?? '');
            $price = (float)($args['price'] ?? 0);
            $suffix = trim($args['price_suffix'] ?? '');
            $ingredients = trim($args['ingredients'] ?? '');
            if ($itemName === '' || $category === '') return json_encode(['error' => 'name and category required']);

            $catCheck = $conn->prepare("SELECT cat_name FROM categories WHERE LOWER(cat_name) = LOWER(?)");
            $catCheck->bind_param("s", $category);
            $catCheck->execute();
            $catRow = $catCheck->get_result()->fetch_assoc();
            $catCheck->close();
            if (!$catRow) {
                $allCats = [];
                $r = $conn->query("SELECT cat_name FROM categories ORDER BY `Order` ASC");
                while ($row = $r->fetch_assoc()) $allCats[] = $row['cat_name'];
                return json_encode(['error' => "category '$category' does not exist", 'available_categories' => $allCats]);
            }

            $stmt = $conn->prepare("INSERT INTO items (item_name, item_category, Ingredients, item_priceusd, price_suffix, item_pic, `Order`) VALUES (?, ?, ?, ?, ?, '', 0)");
            $stmt->bind_param("sssds", $itemName, $catRow['cat_name'], $ingredients, $price, $suffix);
            $stmt->execute();
            $newId = $conn->insert_id;
            $stmt->close();
            return json_encode(['success' => true, 'item_id' => $newId, 'name' => $itemName, 'category' => $catRow['cat_name'], 'price' => $price]);
        }

        // ---- Items: update price (immediate, reversible) ----
        if ($name === 'update_item_price') {
            $item = null;
            if (!empty($args['item_id'])) {
                $stmt = $conn->prepare("SELECT item_id, item_name FROM items WHERE item_id = ?");
                $stmt->bind_param("i", $args['item_id']);
            } elseif (!empty($args['item_name'])) {
                $like = "%{$args['item_name']}%";
                $stmt = $conn->prepare("SELECT item_id, item_name FROM items WHERE item_name LIKE ?");
                $stmt->bind_param("s", $like);
            } else {
                return json_encode(['error' => 'item_id or item_name required']);
            }
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows === 0) { $stmt->close(); return json_encode(['error' => 'item not found']); }
            if ($res->num_rows > 1) {
                $matches = [];
                while ($r = $res->fetch_assoc()) $matches[] = $r;
                $stmt->close();
                return json_encode(['error' => 'multiple items matched, be more specific or use item_id', 'matches' => $matches]);
            }
            $item = $res->fetch_assoc();
            $stmt->close();

            $newPrice = (float)($args['new_price'] ?? -1);
            if ($newPrice < 0) return json_encode(['error' => 'new_price required']);

            $upd = $conn->prepare("UPDATE items SET item_priceusd = ? WHERE item_id = ?");
            $upd->bind_param("di", $newPrice, $item['item_id']);
            $upd->execute();
            $upd->close();
            return json_encode(['success' => true, 'item_id' => (int)$item['item_id'], 'name' => $item['item_name'], 'new_price' => $newPrice]);
        }

        // ---- Items: request delete (needs confirmation) ----
        if ($name === 'request_delete_item') {
            $item = null;
            if (!empty($args['item_id'])) {
                $stmt = $conn->prepare("SELECT item_id, item_name FROM items WHERE item_id = ?");
                $stmt->bind_param("i", $args['item_id']);
            } elseif (!empty($args['item_name'])) {
                $like = "%{$args['item_name']}%";
                $stmt = $conn->prepare("SELECT item_id, item_name FROM items WHERE item_name LIKE ?");
                $stmt->bind_param("s", $like);
            } else {
                return json_encode(['error' => 'item_id or item_name required']);
            }
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows === 0) { $stmt->close(); return json_encode(['error' => 'item not found']); }
            if ($res->num_rows > 1) {
                $matches = [];
                while ($r = $res->fetch_assoc()) $matches[] = $r;
                $stmt->close();
                return json_encode(['error' => 'multiple items matched, be more specific or use item_id', 'matches' => $matches]);
            }
            $item = $res->fetch_assoc();
            $stmt->close();

            $summary = "Delete item \"{$item['item_name']}\" (ID {$item['item_id']}) from the menu? Reply YES to confirm or NO to cancel.";
            $payload = json_encode(['item_id' => (int)$item['item_id'], 'item_name' => $item['item_name']]);

            $del = $conn->prepare("DELETE FROM bot_pending_actions WHERE chat_id = ?");
            $del->bind_param("s", $chatId);
            $del->execute(); $del->close();

            $ins = $conn->prepare("INSERT INTO bot_pending_actions (chat_id, action_type, payload, summary) VALUES (?, 'delete_item', ?, ?)");
            $ins->bind_param("sss", $chatId, $payload, $summary);
            $ins->execute(); $ins->close();

            return json_encode(['status' => 'awaiting_confirmation', 'message' => $summary]);
        }

        return json_encode(['error' => 'unknown tool']);
    };

    $systemPrompt = "You are the website assistant for a restaurant, replying to the owner over Telegram. "
        . "Today's date is " . date('Y-m-d') . ". "
        . "You can read and manage orders, menu items, and categories using ONLY the provided tools. "
        . "Never claim to have done something you didn't call a tool for. Never invent item IDs, prices, or order numbers — look them up first. "
        . "IMPORTANT: request_delete_item and request_delete_category do NOT delete anything immediately — they only queue a confirmation. "
        . "When a tool returns status 'awaiting_confirmation', your entire reply must be exactly that tool's 'message' field, nothing more. "
        . "For add_item, if the category doesn't exist, tell the owner the available categories instead of guessing one. "
        . "When you mark an order as Ready for Pickup, a 'Text customer' button is attached to your reply automatically — do not mention sending a text yourself. "
        . "Reply briefly like a text message — no headers, no markdown tables, short bullet list only if listing several things. "
        . "Reply in the same language/style the owner used.";

    $messages = [
        ['role' => 'system', 'content' => $systemPrompt],
        ['role' => 'user', 'content' => $text],
    ];

    $finalText = null;
    for ($i = 0; $i < 6; $i++) {
        $payload = [
            'model' => $model,
            'messages' => $messages,
            'tools' => $tools,
        ];
        if (in_array($model, ['deepseek-v4-pro', 'deepseek-v4-flash'])) {
            $payload['thinking'] = ['type' => 'enabled'];
        }

        $ch = curl_init('https://api.deepseek.com/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $apiKey],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 30,
        ]);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err || !$response) { error_log("DeepSeek API error: $err"); break; }

        $data = json_decode($response, true);
        $choice = $data['choices'][0] ?? null;
        if (!$choice) { error_log("DeepSeek API unexpected response: $response"); break; }

        $msg = $choice['message'];
        $toolCalls = $msg['tool_calls'] ?? [];

        if (empty($toolCalls)) { $finalText = $msg['content'] ?? null; break; }

        $messages[] = $msg;
        foreach ($toolCalls as $call) {
            $fnName = $call['function']['name'];
            $fnArgs = json_decode($call['function']['arguments'] ?? '{}', true) ?: [];
            $result = $runTool($fnName, $fnArgs);
            $messages[] = ['role' => 'tool', 'tool_call_id' => $call['id'], 'content' => $result];
        }
    }

    if ($finalText) {
        if ($smsButton) {
            sendTelegramReply($chatId, $botToken, $finalText, null, $smsButton['text'], $smsButton['url']);
        } else {
            sendTelegramReply($chatId, $botToken, $finalText, null);
        }
    } else {
        sendTelegramReply($chatId, $botToken, "⚠️ Couldn't reach the AI assistant. Please try again.", null);
    }
}

// ---- 4. Everything else goes to the AI assistant ----
// ($deepseekKey/$deepseekModel were already loaded and validated above.)
handleAiAssistant($text, $conn, $deepseekKey, $deepseekModel, $incomingChatId, $botToken);

echo json_encode(['ok' => true]);