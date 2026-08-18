<?php
/**
 * Tiny redirect used by the Telegram bot's "Text customer" button.
 * Telegram only allows https:// (or tg://) links on inline buttons, so this
 * page exists purely as an https:// stepping stone that immediately hands
 * off to the phone's native sms: link, which the browser follows.
 *
 * Usage: sms_redirect.php?to=<digits>&msg=<url-encoded text>
 */

$to = preg_replace('/\D/', '', $_GET['to'] ?? '');
$msg = $_GET['msg'] ?? '';

if (!$to) {
    http_response_code(400);
    echo "Missing phone number.";
    exit;
}

$smsUrl = "sms:" . $to . "?body=" . rawurlencode($msg);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="refresh" content="0;url=<?php echo htmlspecialchars($smsUrl); ?>">
<title>Opening Messages…</title>
<style>
    body { font-family: -apple-system, sans-serif; text-align: center; padding: 60px 20px; color: #333; }
    a { color: #42522B; font-weight: 600; }
</style>
</head>
<body>
    <p>Opening your Messages app…</p>
    <p>If nothing happens, <a href="<?php echo htmlspecialchars($smsUrl); ?>">tap here</a>.</p>
    <script>window.location.href = <?php echo json_encode($smsUrl); ?>;</script>
</body>
</html>