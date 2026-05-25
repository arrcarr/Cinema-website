<?php
session_start();
include "../database/conn.php";

function writeBookingActionLog($conn, $action, $description)
{
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        return;
    }

    $stmt = $conn->prepare('INSERT INTO system_logs (user_id, action, description) VALUES (?, ?, ?)');
    if (!$stmt) {
        return;
    }

    $stmt->bind_param('iss', $userId, $action, $description);
    $stmt->execute();
    $stmt->close();
}

$message = '';

if (isset($_POST['approve_booking']) || isset($_POST['reject_booking'])) {
    $bookingId = (int) ($_POST['booking_id'] ?? 0);
    $newStatus = isset($_POST['approve_booking']) ? 'Approved' : 'Rejected';

    if ($bookingId > 0) {
        $stmt = $conn->prepare('UPDATE booking SET status = ? WHERE booking_id = ?');
        if ($stmt) {
            $stmt->bind_param('si', $newStatus, $bookingId);
            if ($stmt->execute()) {
                $message = 'Reservation updated.';
                writeBookingActionLog($conn, $newStatus === 'Approved' ? 'Approve Booking' : 'Reject Booking', $newStatus . ' booking #' . $bookingId);
            }
            $stmt->close();
        }
    }
}

$movieCount = 0;
$bookingCount = 0;
$userCount = 0;
$systemAlertCount = 0;

$movieCountResult = $conn->query("SELECT COUNT(*) AS total FROM tb_movie_table");
if ($movieCountResult) {
    $movieCount = (int) ($movieCountResult->fetch_assoc()['total'] ?? 0);
}

$bookingCountResult = $conn->query("SELECT COUNT(*) AS total FROM booking");
if ($bookingCountResult) {
    $bookingCount = (int) ($bookingCountResult->fetch_assoc()['total'] ?? 0);
}

$userCountResult = $conn->query("SELECT COUNT(*) AS total FROM user");
if ($userCountResult) {
    $userCount = (int) ($userCountResult->fetch_assoc()['total'] ?? 0);
}

$systemAlertResult = $conn->query("SELECT COUNT(*) AS total FROM system_logs WHERE LOWER(action) IN ('delete', 'reject', 'error', 'failed')");
if ($systemAlertResult) {
    $systemAlertCount = (int) ($systemAlertResult->fetch_assoc()['total'] ?? 0);
}

$recentLogs = $conn->query("SELECT l.log_id, l.action, l.description, l.log_date, u.username FROM system_logs l LEFT JOIN user u ON u.user_id = l.user_id ORDER BY l.log_date DESC LIMIT 5");
$pendingReservations = $conn->query("SELECT b.booking_id, u.email, m.title, s.show_date, s.show_time, GROUP_CONCAT(st.seat_number ORDER BY st.seat_number SEPARATOR ', ') AS seats FROM booking b INNER JOIN user u ON u.user_id = b.user_id INNER JOIN tb_movie_table m ON m.movie_id = b.movie_id INNER JOIN showtime s ON s.showtime_id = b.showtime_id INNER JOIN seat st ON st.seat_id = b.seat_id WHERE LOWER(COALESCE(b.status, 'pending')) = 'pending' GROUP BY b.booking_id, u.email, m.title, s.show_date, s.show_time ORDER BY b.booking_date DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-dark text-white min-vh-100 d-flex">

    <?php include 'sidebar_header.php'; ?>

    <div class="flex-grow-1 p-5 overflow-auto">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3">
            <h1 class="fw-bold">Admin Overview</h1>
            <span class="badge bg-danger fs-6 px-3 py-2">System Administrator</span>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success border-0 shadow-sm fw-bold"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card bg-black border-secondary p-4 h-100 shadow">
                    <h5 class="text-secondary fw-bold text-uppercase mb-3">Total Movies</h5>
                    <h2 class="display-5 fw-bold text-white mb-0"><?php echo $movieCount; ?></h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-black border-secondary p-4 h-100 shadow">
                    <h5 class="text-secondary fw-bold text-uppercase mb-3">Total Bookings</h5>
                    <h2 class="display-5 fw-bold text-white mb-0"><?php echo $bookingCount; ?></h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-black border-secondary p-4 h-100 shadow border-danger">
                    <h5 class="text-danger fw-bold text-uppercase mb-3">Registered Users</h5>
                    <h2 class="display-5 fw-bold text-white mb-0"><?php echo $userCount; ?></h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-black border-secondary p-4 h-100 shadow border-danger">
                    <h5 class="text-danger fw-bold text-uppercase mb-3">System Alerts</h5>
                    <h2 class="display-5 fw-bold text-white mb-0"><?php echo $systemAlertCount; ?></h2>
                </div>
            </div>
        </div>

        <h3 class="fw-bold mb-3">Recent System Activity (Logs)</h3>
        <div class="card bg-black border-secondary overflow-hidden shadow">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead class="table-active">
                    <tr>
                        <th>Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recentLogs && $recentLogs->num_rows > 0): ?>
                        <?php while ($log = $recentLogs->fetch_assoc()): ?>
                            <tr>
                                <td class="text-secondary"><?php echo date('M d, Y g:i A', strtotime($log['log_date'])); ?></td>
                                <td><?php echo htmlspecialchars($log['username'] ?? 'System'); ?></td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($log['action']); ?></span></td>
                                <td><?php echo htmlspecialchars($log['description']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-secondary">No logs found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <h3 class="fw-bold mt-5 mb-3">Pending Reservations</h3>
        <div class="card bg-black border-secondary overflow-hidden shadow">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead class="table-active">
                    <tr>
                        <th>Booking ID</th>
                        <th>User</th>
                        <th>Movie</th>
                        <th>Date & Time</th>
                        <th>Seats</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($pendingReservations && $pendingReservations->num_rows > 0): ?>
                        <?php while ($row = $pendingReservations->fetch_assoc()): ?>
                            <tr>
                                <td class="align-middle">#<?php echo (int) $row['booking_id']; ?></td>
                                <td class="align-middle"><?php echo htmlspecialchars($row['email']); ?></td>
                                <td class="align-middle"><?php echo htmlspecialchars($row['title']); ?></td>
                                <td class="align-middle"><?php echo date('M d, Y - g:i A', strtotime($row['show_date'] . ' ' . $row['show_time'])); ?></td>
                                <td class="align-middle"><?php echo htmlspecialchars($row['seats'] ?? ''); ?></td>
                                <td class="align-middle">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="booking_id" value="<?php echo (int) $row['booking_id']; ?>">
                                        <button type="submit" name="approve_booking" class="btn btn-sm btn-success fw-bold me-1"><i class="bi bi-check-lg"></i> Approve</button>
                                        <button type="submit" name="reject_booking" class="btn btn-sm btn-danger fw-bold"><i class="bi bi-x-lg"></i> Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-secondary">No pending reservations.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>