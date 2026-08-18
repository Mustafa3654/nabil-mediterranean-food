<?php
include "../includes/connection.php";
include "../includes/auth.php";
start_secure_session();
require_admin('../login');
check_session_timeout(30);
$csrfToken = ensure_csrf_token();

// Export CSV
if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="contacts_export_' . date('Y-m-d_H-i-s') . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    $output = fopen('php://output', 'w');
    fputs($output, "\xEF\xBB\xBF");
    fputcsv($output, ['ID', 'Name', 'Phone Number', 'Subject', 'Message', 'Submitted']);

    $exportSql = "SELECT id, name, phonenumber, subject, message, submission_date FROM contact_submissions ORDER BY submission_date DESC";
    $exportRes = $conn->query($exportSql);
    if ($exportRes) {
        while ($r = $exportRes->fetch_assoc()) {
            fputcsv($output, [$r['id'], $r['name'], $r['phonenumber'], $r['subject'], $r['message'], $r['submission_date']]);
        }
    }
    fclose($output);
    exit;
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$dateFrom = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
$dateTo = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';

$where = "";
$params = [];
$types = "";

if ($search !== '') {
    $where .= " AND (name LIKE ? OR phonenumber LIKE ? OR subject LIKE ? OR message LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ssss";
}

if ($dateFrom !== '') {
    $where .= " AND DATE(submission_date) >= ?";
    $params[] = $dateFrom;
    $types .= "s";
}
if ($dateTo !== '') {
    $where .= " AND DATE(submission_date) <= ?";
    $params[] = $dateTo;
    $types .= "s";
}

$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM contact_submissions WHERE 1=1" . $where);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalContacts = (int)$countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();
$totalPages = max(1, (int)ceil($totalContacts / $perPage));
if ($page > $totalPages) $page = $totalPages;
$offset = ($page - 1) * $perPage;

$sql = "SELECT * FROM contact_submissions WHERE 1=1" . $where . " ORDER BY submission_date DESC LIMIT ? OFFSET ?";
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Submissions</title>
    <link rel="stylesheet" href="../assets/css/view.css?v=1.2" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="../assets/css/admin-shared.css?v=1.2">
    <style>
        .contacts-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .contact-card {
            background-color: #F7F5EA;
            border: 1px solid rgba(203, 181, 139, 0.4);
            border-radius: 12px;
            padding: 18px 20px;
        }
        .contact-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(203, 181, 139, 0.4);
        }
        .contact-who { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
        .contact-name { font-size: 17px; font-weight: 800; color: #42522B; }
        .contact-phone { font-size: 13px; color: #6b6450; }
        .contact-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 4px; flex-shrink: 0; }
        .contact-date { font-size: 12px; color: #8a8064; white-space: nowrap; }
        .contact-subject {
            display: inline-block;
            background: #e9e2c9;
            color: #42522B;
            font-size: 12px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 10px;
            white-space: nowrap;
        }
        .contact-message {
            font-size: 15px;
            line-height: 1.6;
            color: #2B2B2A;
            white-space: pre-wrap;
            word-break: break-word;
            padding: 4px 2px 2px;
        }

        .export-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .export-bar a {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .export-bar a:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        @media (max-width: 480px) {
            .contact-card-top { flex-direction: column; }
            .contact-meta { align-items: flex-start; }
            .contact-card { padding: 14px; }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1>Contact Submissions</h1>
            <a href="dashboard" class="back-btn"><i class="fas fa-arrow-left"></i> BACK</a>
        </div>

        <div class="export-bar">
            <div></div>
            <a href="viewContacts?export=1"><i class="fas fa-file-excel"></i> Export to Excel</a>
        </div>

        <div class="controls">
            <form method="GET" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap; width:100%;">
                <div class="search-box" style="flex:1; min-width:180px;">
                    <input type="text" name="search" placeholder="Search by name, phone, subject or message..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <label style="font-size:13px; color:#42522B; font-weight:600;">From:
                    <input type="date" name="date_from" value="<?php echo htmlspecialchars($dateFrom); ?>" style="margin-left:4px; padding:8px; border:1px solid #CBB58B; border-radius:5px; font-size:13px;">
                </label>
                <label style="font-size:13px; color:#42522B; font-weight:600;">To:
                    <input type="date" name="date_to" value="<?php echo htmlspecialchars($dateTo); ?>" style="margin-left:4px; padding:8px; border:1px solid #CBB58B; border-radius:5px; font-size:13px;">
                </label>
                <button type="submit" style="padding:8px 16px; background:#42522B; color:white; border:none; border-radius:5px; cursor:pointer; font-weight:600;">Filter</button>
            </form>
        </div>

        <?php if (count($rows) > 0): ?>
            <div class="contacts-list">
                <?php foreach ($rows as $r): ?>
                    <div class="contact-card">
                        <div class="contact-card-top">
                            <div class="contact-who">
                                <span class="contact-name"><?php echo htmlspecialchars($r['name'] ?? ''); ?></span>
                                <?php if (!empty($r['phonenumber'])): ?>
                                <span class="contact-phone"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($r['phonenumber']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="contact-meta">
                                <?php if (!empty($r['subject'])): ?>
                                <span class="contact-subject"><?php echo htmlspecialchars($r['subject']); ?></span>
                                <?php endif; ?>
                                <span class="contact-date"><?php echo $r['submission_date'] ? date('M j, Y g:ia', strtotime($r['submission_date'])) : '-'; ?></span>
                            </div>
                        </div>

                        <div class="contact-message"><?php echo htmlspecialchars($r['message'] ?? ''); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="pagination">
                <?php
                $qp = [];
                if ($search !== '') $qp['search'] = $search;
                if ($dateFrom !== '') $qp['date_from'] = $dateFrom;
                if ($dateTo !== '') $qp['date_to'] = $dateTo;
                if ($page > 1):
                    $qp['page'] = $page - 1;
                    echo '<a href="viewContacts?' . http_build_query($qp) . '" class="page-link">&laquo; Prev</a>';
                endif;
                for ($i = 1; $i <= $totalPages; $i++):
                    $qp['page'] = $i;
                    $active = $i === $page ? ' class="page-link active"' : ' class="page-link"';
                    echo '<a href="viewContacts?' . http_build_query($qp) . '"' . $active . '>' . $i . '</a>';
                endfor;
                if ($page < $totalPages):
                    $qp['page'] = $page + 1;
                    echo '<a href="viewContacts?' . http_build_query($qp) . '" class="page-link">Next &raquo;</a>';
                endif;
                echo '<span class="page-info">' . $totalContacts . ' total submissions</span>';
                ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info" style="text-align:center; padding:20px;">No contact submissions found.</div>
        <?php endif; ?>
    </div>
</body>
</html>