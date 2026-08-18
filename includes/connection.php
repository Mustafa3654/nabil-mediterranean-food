<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Database configuration
$dbHost     = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName     = 'nabil_menu';

// Dynamically determine the base URL based on the folder location relative to Document Root.
// This allows you to rename the project folder to anything (e.g. 'menu') without breaking links.
$docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
$projectRoot = str_replace('\\', '/', realpath(dirname(__DIR__)));

// Standardize casing for comparison (especially on Windows)
$docRootLower = strtolower($docRoot);
$projectRootLower = strtolower($projectRoot);

if (strpos($projectRootLower, $docRootLower) === 0) {
    $relativePath = substr($projectRoot, strlen($docRoot));
    $BASE_URL = '/' . trim($relativePath, '/') . '/';
    if ($BASE_URL === '//') {
        $BASE_URL = '/';
    }
} else {
    // Safe fallback using the immediate directory name
    $BASE_URL = '/' . basename($projectRoot) . '/';
}
// Set connection timeout to 5 seconds
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
    $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage() . "<br>Make sure MySQL is running in XAMPP.");
}

// Lightweight schema guard for incremental features.
// Keeps older databases working without manual ALTERs.
try {
    $col = $conn->query("SHOW COLUMNS FROM settings LIKE 'show_cart'");
    if (!$col || $col->num_rows === 0) {
        $conn->query("ALTER TABLE settings ADD COLUMN show_cart TINYINT(1) NOT NULL DEFAULT 1");
    }
} catch (Throwable $e) {
    // Non-fatal: pages can still render with defaults if the column can't be added.
}

try {
    $col = $conn->query("SHOW COLUMNS FROM orders LIKE 'notes'");
    if (!$col || $col->num_rows === 0) {
        $conn->query("ALTER TABLE orders ADD COLUMN notes TEXT DEFAULT NULL");
    }
} catch (Throwable $e) {
    // Non-fatal
}

// Include core helpers
include_once __DIR__ . '/auth.php';
?>


