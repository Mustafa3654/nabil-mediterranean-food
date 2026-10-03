<?php
/**
 * Minimal Telegram webhook endpoint.
 *
 * Website orders are sent directly to the configured Telegram chat by
 * save_order.php. This endpoint remains available for the bot's webhook
 * registration and safely acknowledges updates without processing commands.
 */

header('Content-Type: application/json; charset=utf-8');

$expectedSecret = getenv('TELEGRAM_WEBHOOK_SECRET') ?: '';
$sentSecret = $_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] ?? '';

if ($expectedSecret !== '' && !hash_equals($expectedSecret, $sentSecret)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'forbidden']);
    exit;
}

// This bot is only used to receive website order notifications. Ignore
// Telegram updates so they cannot trigger commands or modify site data.
echo json_encode(['ok' => true]);
