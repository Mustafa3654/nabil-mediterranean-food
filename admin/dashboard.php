<?php
include "../includes/connection.php";
include "../includes/auth.php";
start_secure_session();
require_admin('../login');
check_session_timeout(30);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css?v=1.1">
</head>
<body>
    <div class="dashboard-container"> 
        <h1>Dashboard</h1>
        <div class="button-grid">
            <a href="addItem" class="dashboard-button">Add Item</a>
            <a href="addCategory" class="dashboard-button">Add Category</a>
            <a href="viewItems" class="dashboard-button">View Items</a>
            <a href="viewCategories" class="dashboard-button">View Categories</a>
            <a href="viewOrders" class="dashboard-button">View Orders</a>
            <a href="viewContacts" class="dashboard-button">View Contacts</a>
            <a href="editSettings" class="dashboard-button" style="grid-column: 1 / -1; background: #42522B;">Global Settings</a>

            <a href="<?php echo $BASE_URL; ?>index" class="dashboard-button" style="background:#6c757d;">Back to Site</a>
            <a href="<?php echo $BASE_URL; ?>logout" class="dashboard-button logout" style="background:#e11d48;">Logout</a>
        </div>
    </div>
</body>
</html>



