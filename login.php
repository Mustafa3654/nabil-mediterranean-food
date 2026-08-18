<?php
include "includes/connection.php";
include "includes/auth.php";
start_secure_session();

// Redirect to dashboard if already logged in
if (is_admin_logged_in()) {
    header("Location: admin/dashboard");
    exit;
}

// -------------------------
// Local helpers
// -------------------------
function fetchUserByUsername(mysqli $conn, string $username): ?array
{
    $stmt = $conn->prepare("SELECT user_id, username, userpassword, isAdmin FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = ($result && $result->num_rows === 1) ? $result->fetch_assoc() : null;
    $stmt->close();
    return $user;
}

function isLegacyPlainPassword(string $storedPassword): bool
{
    return password_get_info($storedPassword)['algo'] === 0;
}

function upgradeLegacyPasswordHash(mysqli $conn, int $userId, string $plainPassword): void
{
    $newHash = password_hash($plainPassword, PASSWORD_DEFAULT);
    if ($newHash === false) {
        return;
    }

    $updateStmt = $conn->prepare("UPDATE users SET userpassword = ? WHERE user_id = ?");
    $updateStmt->bind_param("si", $newHash, $userId);
    $updateStmt->execute();
    $updateStmt->close();
}

// -------------------------
// Form handling
// -------------------------
$error_message = "";

if (isset($_POST['submit'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error_message = "Invalid session token. Please refresh and try again.";
    } else {
        $username = trim($_POST["username"] ?? '');
        $password = $_POST["password"] ?? '';

        if ($username === '' || $password === '') {
            $error_message = "Wrong Username or Password. Please try again.";
        } else {
            $user = fetchUserByUsername($conn, $username);

            if ($user) {
                $storedPassword = (string)($user['userpassword'] ?? '');
                // Keep legacy plain-text compatibility while upgrading users to hashes.
                $isValidPassword = password_verify($password, $storedPassword)
                    || hash_equals($storedPassword, $password);

                if ($isValidPassword) {
                    if (isLegacyPlainPassword($storedPassword)) {
                        upgradeLegacyPasswordHash($conn, (int)$user['user_id'], $password);
                    }

                    session_regenerate_id(true);
                    $_SESSION["user_name"] = $user['username'];
                    $_SESSION["isAdmin"] = ((int)$user['isAdmin'] === 1);


                    header("Location: admin/dashboard");
                    exit;
                }
            }

            $error_message = "Wrong Username or Password. Please try again.";
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
    <meta name="robots" content="noindex, nofollow">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/login.css">
    <script src="assets/js/login.js" defer></script>
</head>
<body>
    <div class="dashboard-container">
        <h1>Login</h1>
        <?php if($error_message !== ""): ?>
            <div id="login-error-box" style='text-align:center; margin-bottom:20px; color: #e11d48; font-weight: 600;'>
                <p><?php echo $error_message; ?></p>
            </div>
        <?php endif; ?>
        <form action="login" method="POST">
            <?php echo csrf_input(); ?>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter Username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter Password" required>
            <button type="submit" name="submit" value="submit" class="submit">Login</button>
        </form> 
        <br>
        <div class="button-grid"> 
        <a href="index" class="back-button backk">Back</a>
        </div>
    </div>
</body>
</html>


