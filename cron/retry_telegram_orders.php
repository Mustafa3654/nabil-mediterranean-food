<?php
/**
 * Retry pending order notifications. Run from Hostinger's PHP CLI scheduler.
 * Never expose this script as an HTTP endpoint.
 * A timeout after Telegram accepted a message can lead to a duplicate on retry;
 * Telegram's sendMessage API does not provide idempotency keys.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/includes/connection.php';
define('TELEGRAM_ORDER_WORKER', true);
require_once dirname(__DIR__) . '/save_order.php';

$limit = 25;
$stmt = $conn->prepare('SELECT id FROM orders WHERE telegram_notified_at IS NULL AND telegram_attempts < 10 AND (telegram_next_attempt_at IS NULL OR telegram_next_attempt_at <= NOW()) ORDER BY id ASC LIMIT ?');
$stmt->bind_param('i', $limit);
$stmt->execute();
$result = $stmt->get_result();
$orderIds = [];
while ($row = $result->fetch_assoc()) {
    $orderIds[] = (int)$row['id'];
}
$stmt->close();

foreach ($orderIds as $orderId) {
    $delivery = sendOrderTelegramNotification($conn, $orderId);
    if (!$delivery['ok']) {
        $attemptStmt = $conn->prepare('SELECT telegram_attempts FROM orders WHERE id = ?');
        $attemptStmt->bind_param('i', $orderId);
        $attemptStmt->execute();
        $attempts = (int)($attemptStmt->get_result()->fetch_assoc()['telegram_attempts'] ?? 0);
        $attemptStmt->close();

        if ($attempts >= 10) {
            $status = 'failed';
            $error = 'Maximum automatic Telegram retry attempts reached';
            $failedStmt = $conn->prepare('UPDATE orders SET telegram_status = ?, telegram_last_error = ?, telegram_next_attempt_at = NULL WHERE id = ?');
            $failedStmt->bind_param('ssi', $status, $error, $orderId);
            $failedStmt->execute();
            $failedStmt->close();
        } else {
            // Retry after 1, 2, 4, 8, 16... minutes, capped at 6 hours.
            $delaySeconds = min(21600, 60 * (2 ** min(8, max(0, $attempts - 1))));
            $nextAttempt = date('Y-m-d H:i:s', time() + $delaySeconds);
            $retryStmt = $conn->prepare('UPDATE orders SET telegram_next_attempt_at = ? WHERE id = ?');
            $retryStmt->bind_param('si', $nextAttempt, $orderId);
            $retryStmt->execute();
            $retryStmt->close();
        }
    }
}

echo 'Processed ' . count($orderIds) . " pending Telegram order notification(s).\n";
