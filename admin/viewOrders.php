<?php
include "../includes/connection.php";
include "../includes/auth.php";
start_secure_session();
require_admin('../login');
check_session_timeout(30);
$csrfToken = ensure_csrf_token();

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$dateFrom = isset($_GET['date_from']) ? trim($_GET['date_from']) : date('Y-m-d');
$dateTo = isset($_GET['date_to']) ? trim($_GET['date_to']) : date('Y-m-d', strtotime('+1 month'));
$statusFilter = isset($_GET['status_filter']) ? trim($_GET['status_filter']) : '';

$where = "";
$params = [];
$types = "";

if ($search !== '') {
    $where .= " AND (customer_name LIKE ? OR whatsapp_number LIKE ? OR notes LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "sss";
}

if ($statusFilter !== '' && in_array($statusFilter, ['pending', 'sent', 'completed', 'cancelled'])) {
    $where .= " AND status = ?";
    $params[] = $statusFilter;
    $types .= "s";
}

$where .= " AND DATE(created_at) >= ? AND DATE(created_at) <= ?";
$params[] = $dateFrom;
$params[] = $dateTo;
$types .= "ss";

$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM orders WHERE 1=1" . $where);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalOrders = (int)$countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();
$totalPages = max(1, (int)ceil($totalOrders / $perPage));
if ($page > $totalPages) $page = $totalPages;
$offset = ($page - 1) * $perPage;

$sql = "SELECT * FROM orders WHERE 1=1" . $where . " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;
$types .= "ii";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
$stmt->close();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['new_status'];
    $allowed = ['pending', 'sent', 'completed', 'cancelled'];
    if (in_array($new_status, $allowed)) {
        if ($new_status === 'completed') {
            $upd = $conn->prepare("UPDATE orders SET status = ?, completed_at = NOW() WHERE id = ?");
        } else {
            $upd = $conn->prepare("UPDATE orders SET status = ?, completed_at = NULL WHERE id = ?");
        }
        $upd->bind_param("si", $new_status, $order_id);
        $upd->execute();
        $upd->close();
        header("Location: viewOrders?updated=1");
        exit;
    }
}

// Display labels for status values (DB keeps 'sent' for backward compatibility,
// but it's shown to you as "Done")
$statusLabels = [
    'pending'   => 'Pending',
    'sent'      => 'Ready for Pickup',
    'completed' => 'Completed',
    'cancelled' => 'Cancelled',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <link rel="stylesheet" href="../assets/css/view.css?v=1.2" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="../assets/css/admin-shared.css?v=1.2">
</head>
<body>
    <div class="dashboard-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1>Orders</h1>
            <a href="dashboard" class="back-btn"><i class="fas fa-arrow-left"></i> BACK</a>
        </div>

        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success">Order status updated.</div>
        <?php endif; ?>

        <div class="controls">
            <form method="GET" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap; width:100%;">
                <div class="search-box" style="flex:1; min-width:180px;">
                    <input type="text" name="search" placeholder="Search by name or phone..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <label style="font-size:13px; color:#42522B; font-weight:600;">Status:
                    <select name="status_filter" style="margin-left:4px; padding:8px; border:1px solid #CBB58B; border-radius:5px; font-size:13px;">
                        <option value="">All</option>
                        <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="sent" <?php echo $statusFilter === 'sent' ? 'selected' : ''; ?>>Ready for Pickup</option>
                        <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </label>
                <label style="font-size:13px; color:#42522B; font-weight:600;">From:
                    <input type="date" name="date_from" value="<?php echo $dateFrom; ?>" style="margin-left:4px; padding:8px; border:1px solid #CBB58B; border-radius:5px; font-size:13px;">
                </label>
                <label style="font-size:13px; color:#42522B; font-weight:600;">To:
                    <input type="date" name="date_to" value="<?php echo $dateTo; ?>" style="margin-left:4px; padding:8px; border:1px solid #CBB58B; border-radius:5px; font-size:13px;">
                </label>
                <button type="submit" style="padding:8px 16px; background:#42522B; color:white; border:none; border-radius:5px; cursor:pointer; font-weight:600;">Filter</button>
            </form>
        </div>

        <?php if (count($rows) > 0): ?>
            <div class="orders-list">
                <?php foreach ($rows as $r):
                    $total = $r['total_usd'] > 0 ? '$' . number_format($r['total_usd'], 2) : '-';
                    $itemsList = [];
                    if (!empty($r['items'])) {
                        $decoded = json_decode($r['items'], true);
                        if (is_array($decoded)) {
                            foreach ($decoded as $it) {
                                $qty = isset($it['quantity']) ? (int)$it['quantity'] : 1;
                                $itemsList[] = $qty . 'x ' . ($it['name'] ?? 'Item');
                            }
                        }
                    }
                ?>
                    <div class="order-card">
                        <div class="order-card-top">
                            <span class="order-id">#<?php echo (int)$r['id']; ?></span>
                            <span class="status-badge status-<?php echo htmlspecialchars($r['status'] ?? 'pending'); ?>">
                                <?php echo htmlspecialchars($statusLabels[$r['status']] ?? ($r['status'] ?? 'pending')); ?>
                            </span>
                        </div>

                        <div class="order-card-body">
                            <div class="order-field">
                                <span class="order-field-label"><i class="fas fa-user"></i> Customer</span>
                                <span class="order-field-value"><?php echo htmlspecialchars($r['customer_name'] ?? ''); ?></span>
                            </div>
                            <div class="order-field">
                                <span class="order-field-label"><i class="fas fa-phone"></i> Phone</span>
                                <span class="order-field-value"><?php echo htmlspecialchars($r['whatsapp_number'] ?? ''); ?></span>
                            </div>
                            <div class="order-field">
                                <span class="order-field-label"><i class="fas fa-clock"></i> Requested Time</span>
                                <span class="order-field-value"><?php echo !empty($r['requested_time']) ? htmlspecialchars($r['requested_time']) : 'ASAP'; ?></span>
                            </div>
                            <div class="order-field">
                                <span class="order-field-label"><i class="fas fa-bag-shopping"></i> Items</span>
                                <span class="order-field-value"><?php echo !empty($itemsList) ? htmlspecialchars(implode(', ', $itemsList)) : '-'; ?></span>
                            </div>
                            <?php if (!empty($r['notes'])): ?>
                            <div class="order-field">
                                <span class="order-field-label"><i class="fas fa-note-sticky"></i> Notes</span>
                                <span class="order-field-value"><?php echo htmlspecialchars($r['notes']); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="order-field">
                                <span class="order-field-label"><i class="fas fa-dollar-sign"></i> Total</span>
                                <span class="order-field-value order-total"><?php echo $total; ?></span>
                            </div>
                            <div class="order-field">
                                <span class="order-field-label"><i class="fas fa-calendar-plus"></i> Ordered</span>
                                <span class="order-field-value"><?php echo $r['created_at'] ? date('M j, g:ia', strtotime($r['created_at'])) : '-'; ?></span>
                            </div>
                            <?php if (!empty($r['completed_at'])): ?>
                            <div class="order-field">
                                <span class="order-field-label"><i class="fas fa-calendar-check"></i> Completed</span>
                                <span class="order-field-value"><?php echo date('M j, g:ia', strtotime($r['completed_at'])); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <form method="POST" class="status-form">
                            <input type="hidden" name="order_id" value="<?php echo (int)$r['id']; ?>">
                            <select name="new_status" class="status-select">
                                <option value="pending" <?php echo $r['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="sent" <?php echo $r['status'] === 'sent' ? 'selected' : ''; ?>>Ready for Pickup</option>
                                <option value="completed" <?php echo $r['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo $r['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            <button type="submit" name="update_status" class="update-btn"><i class="fas fa-check"></i> Update</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="pagination">
                <?php
                $qp = [];
                if ($search !== '') $qp['search'] = $search;
                if ($statusFilter !== '') $qp['status_filter'] = $statusFilter;
                $qp['date_from'] = $dateFrom;
                $qp['date_to'] = $dateTo;
                if ($page > 1):
                    $qp['page'] = $page - 1;
                    echo '<a href="viewOrders?' . http_build_query($qp) . '" class="page-link">&laquo; Prev</a>';
                endif;
                for ($i = 1; $i <= $totalPages; $i++):
                    $qp['page'] = $i;
                    $active = $i === $page ? ' class="page-link active"' : ' class="page-link"';
                    echo '<a href="viewOrders?' . http_build_query($qp) . '"' . $active . '>' . $i . '</a>';
                endfor;
                if ($page < $totalPages):
                    $qp['page'] = $page + 1;
                    echo '<a href="viewOrders?' . http_build_query($qp) . '" class="page-link">Next &raquo;</a>';
                endif;
                echo '<span class="page-info">' . $totalOrders . ' total orders</span>';
                ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info" style="text-align:center; padding:20px;">No orders found.</div>
        <?php endif; ?>
    </div>
</body>
</html>