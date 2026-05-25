<?php
session_start();

//Checks if user is an admin
if (!isset($_SESSION['userType']) || !in_array(strtolower((string) $_SESSION['userType']), ['admin', 'administrator'], true)) {
    die("<h2 style='color:red; text-align:center; margin-top:50px;'>Access Denied. Admins Only.</h2>");
}

include "../database/conn.php";

//Join function with user table
$sql = "SELECT l.log_id, l.action, l.description, l.log_date, u.username, u.userType 
            FROM system_logs l 
            LEFT JOIN user u ON l.user_id = u.user_id 
            ORDER BY l.log_date DESC";
$logs = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <title>System Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-dark text-white min-vh-100 d-flex">
    <?php include 'sidebar_header.php'; ?>

    <div class="flex-grow-1 p-5 overflow-auto">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3">
            <h1 class="fw-bold text-danger">System Logs</h1>
            <span class="badge bg-secondary fs-6 px-3 py-2"><i class="bi bi-clock-history me-1"></i> Real-time
                Tracking</span>
        </div>

        <div class="card bg-black border-secondary overflow-hidden shadow">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead class="table-active">
                    <tr>
                        <th style="width: 10%;">Log ID</th>
                        <th style="width: 20%;">Date & Time</th>
                        <th style="width: 20%;">User</th>
                        <th style="width: 15%;">Action</th>
                        <th style="width: 35%;">Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($logs && $logs->num_rows > 0): ?>
                        <?php while ($row = $logs->fetch_assoc()): ?>
                            <tr>
                                <td class="text-secondary">#<?php echo $row['log_id']; ?></td>
                                <td><?php echo date('M d, Y g:i A', strtotime($row['log_date'])); ?></td>
                                <td>
                                    <span
                                        class="fw-bold"><?php echo htmlspecialchars($row['username'] ?? 'System/Deleted User'); ?></span>
                                    <?php if (isset($row['userType'])): ?>
                                        <span class="badge bg-secondary ms-1 text-capitalize" style="font-size: 0.7rem;">
                                            <?php echo htmlspecialchars($row['userType']); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $badgeColor = 'bg-primary';
                                    $action = strtolower($row['action']);
                                    if (strpos($action, 'delete') !== false || strpos($action, 'reject') !== false)
                                        $badgeColor = 'bg-danger';
                                    if (strpos($action, 'approve') !== false || strpos($action, 'add') !== false)
                                        $badgeColor = 'bg-success';
                                    if (strpos($action, 'login') !== false)
                                        $badgeColor = 'bg-info text-dark';
                                    ?>
                                    <span class="badge <?php echo $badgeColor; ?>">
                                        <?php echo htmlspecialchars($row['action']); ?>
                                    </span>
                                </td>
                                <td class="text-light"><?php echo htmlspecialchars($row['description']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-secondary">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No system logs found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>