<?php
session_start();
include "../database/conn.php";

if (isset($_POST['update_status'])) {
    $id = $_POST['booking_id'];
    $new_status = $_POST['status_val'];
    $conn->query("UPDATE booking SET status = '$new_status' WHERE booking_id = '$id'");
}

$sql = "SELECT b.booking_id, b.booking_date, b.status, m.title, u.email, s.show_date, st.seat_number 
            FROM booking b
            JOIN tb_movie_table m ON b.movie_id = m.movie_id
            JOIN user u ON b.user_id = u.user_id
            JOIN showtime s ON b.showtime_id = s.showtime_id
            JOIN seat st ON b.seat_id = st.seat_id
            ORDER BY b.booking_date DESC";
$reservations = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <title>Manage Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-dark text-white min-vh-100 d-flex">
    <?php include '../admin/sidebar_header.php'; ?>

    <div class="flex-grow-1 p-5 overflow-auto">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3">
            <h1 class="fw-bold">Reservations</h1>
        </div>

        <div class="card bg-black border-secondary overflow-hidden shadow">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead class="table-active">
                    <tr>
                        <th>Booking ID</th>
                        <th>User Email</th>
                        <th>Movie</th>
                        <th>Show Date</th>
                        <th>Seat</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $reservations->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['booking_id']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['title']; ?></td>
                            <td><?php echo $row['show_date']; ?></td>
                            <td><?php echo $row['seat_number']; ?></td>
                            <td>
                                <?php
                                $badge = 'bg-secondary';
                                if (isset($row['status'])) {
                                    if ($row['status'] == 'Approved')
                                        $badge = 'bg-success';
                                    if ($row['status'] == 'Rejected')
                                        $badge = 'bg-danger';
                                }
                                ?>
                                <span class="badge <?php echo $badge; ?>"><?php echo $row['status'] ?? 'Pending'; ?></span>
                            </td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                                    <button type="submit" name="update_status" value="Approved"
                                        class="btn btn-sm btn-success me-1"><i class="bi bi-check-lg"></i> Approve</button>
                                    <button type="submit" name="update_status" value="Rejected"
                                        class="btn btn-sm btn-danger"><i class="bi bi-x-lg"></i> Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>