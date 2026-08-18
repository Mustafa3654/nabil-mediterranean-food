<?php
include "includes/connection.php";

start_secure_session();

/* -------------------------
   Fetch restaurant settings (cached)
-------------------------- */
$settings = get_settings();

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
    $result = file_get_contents($url, false, $context);

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

/* -------------------------
   Handle form submission
-------------------------- */
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $message = '
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            Invalid request token. Please refresh and try again.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>';
    } else {

        $name    = trim($_POST['name'] ?? '');
        $phonenumber = trim($_POST['phonenumber'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $body    = trim($_POST['message'] ?? '');

        if ($name && $phonenumber && $subject && $body) {

            $sql = "INSERT INTO contact_submissions (name, phonenumber, subject, message)
                    VALUES (?, ?, ?, ?)";

            if ($stmt = $conn->prepare($sql)) {

                $stmt->bind_param("ssss", $name, $phonenumber, $subject, $body);

                if ($stmt->execute()) {



                    $message = '
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        Thank you for your message! We will get back to you soon.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>';

                    $telegram_chat_id  = $settings['chat_id'] ?? '';
                    $telegram_bot_token = $settings['bot_token'] ?? '';
                    $notify_enabled = !isset($settings['notify_contact_telegram']) || (string)$settings['notify_contact_telegram'] === '1';

                    if ($telegram_chat_id && $telegram_bot_token && $notify_enabled) {

                        $telegram_text =
                              htmlspecialchars($name) . ":\n"
                            . "<b>Phone Number:</b> " . htmlspecialchars($phonenumber) . "\n"
                            . "<b>Subject:</b> " . htmlspecialchars($subject) . "\n"
                            . "<b>Message:</b>\n" . htmlspecialchars($body);

                        sendTelegramMessage(
                            $telegram_chat_id,
                            $telegram_bot_token,
                            $telegram_text
                        );
                    }

                } else {
                    $message = '
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Something went wrong. Please try again later.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>';
                }

                $stmt->close();

            } else {
                $message = '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Database error. Please try again later.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
            }

        } else {
            $message = '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                Please fill in all fields.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
        }
    }
}

$csrfToken = ensure_csrf_token();
?>

<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="assets/css/contact.css">

<section class="hero-section contact-hero-section" style="background-image: url('<?php echo htmlspecialchars($settings['contact_bg'] ?? ''); ?>');">
    <div class="hero-content">
        <h1 class="hero-title reveal-text">Contact Us</h1>
        <p class="hero-subtitle reveal-text">We'd love to hear from you</p>
    </div>
</section>

<section class="contact-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <?php echo $message; ?>

                <div class="contact-card">
                    <div class="row g-0">

                        <div class="col-md-5">
                            <div class="contact-info-section">
                                <h3>Get in Touch</h3>

                                <div class="info-item">
                                    <div class="info-icon"><i class="fas fa-phone"></i></div>
                                    <div class="info-text">
                                        <h5>Phone</h5>
                                        <p><?php echo htmlspecialchars($settings['restaurant_phone'] ?? ''); ?></p>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon"><i class="fas fa-envelope"></i></div>
                                    <div class="info-text">
                                        <h5>Email</h5>
                                        <p><?php echo htmlspecialchars($settings['restaurant_email'] ?? ''); ?></p>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                                    <div class="info-text">
                                        <h5>Location</h5>
                                        <p><?php echo htmlspecialchars($settings['restaurant_address'] ?? 'Lebanon'); ?></p>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-7">
                            <div class="contact-form-section">
                                <form method="POST">
                                    <?php echo csrf_input(); ?>
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" name="phonenumber" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Subject</label>
                                        <input type="text" name="subject" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Message</label>
                                        <textarea name="message" class="form-control" rows="4" required></textarea>
                                    </div>

                                    <button type="submit" class="btn-submit">Send Message</button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>