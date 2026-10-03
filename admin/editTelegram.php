<?php
include "../includes/connection.php";
include "../includes/auth.php";
start_secure_session();
require_admin();
check_session_timeout(30);

// -------------------------
// Load current settings (cached)
// -------------------------
$settings = get_settings();
$message = "";

// -------------------------
// Handle form submission
// -------------------------
if (isset($_POST['update_telegram'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $message = "<div class='alert alert-danger'>Invalid request token. Please refresh and try again.</div>";
    } else {
        $chat_id = trim($_POST['chat_id'] ?? '');
        $bot_token = trim($_POST['bot_token'] ?? '');
        // Persist settings
        if ($settings) {
            $stmt = $conn->prepare("UPDATE settings SET chat_id = ?, bot_token = ? WHERE id = ?");
            $stmt->bind_param("ssi", $chat_id, $bot_token, $settings['id']);
            
            if ($stmt->execute()) {
                invalidate_settings_cache();
                $message = "<div class='alert alert-success'>Telegram Settings updated successfully!</div>";
                $settings = get_settings();
            } else {
                $message = "<div class='alert alert-danger'>Error updating settings: " . htmlspecialchars($stmt->error) . "</div>";
            }
            $stmt->close();
        } else {
            $message = "<div class='alert alert-danger'>Settings configuration not found. Please save general settings first.</div>";
        }
    }
}

$csrfToken = ensure_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Telegram Settings</title>
    <link rel="stylesheet" href="../assets/css/admin_form.css">
    <link rel="stylesheet" href="../assets/css/admin-shared.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head> 
<body>
    <div class="form-container">
        <i class="fab fa-telegram tg-icon"></i>
        <h1>Telegram Config</h1>
        <?php echo $message; ?>
        <form action="editTelegram" method="POST">
            <?php echo csrf_input(); ?>

            <div class="form-group">
                <label for="chat_id">Telegram Chat ID</label>
                <input type="text" name="chat_id" value="<?php echo htmlspecialchars($settings['chat_id'] ?? ''); ?>" placeholder="e.g. -100123456789">
            </div>

            <div class="form-group">
                <label for="bot_token">Telegram Bot Token</label>
                <input type="text" name="bot_token" value="<?php echo htmlspecialchars($settings['bot_token'] ?? ''); ?>" placeholder="e.g. 123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11">
            </div>

            <button type="submit" name="update_telegram" class="submit-btn">Save Telegram Settings</button>
            <a href="dashboard" class="back-link"><button type="button">BACK</button></a>
        </form>
    </div>
</body>
</html>
