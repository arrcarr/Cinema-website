<?php
session_start();
include "../database/conn.php";

if (isset($_POST['delete_showtime'])) {
    $id = $_POST['showtime_id'];
    $conn->query("DELETE FROM showtime WHERE showtime_id = '$id'");
}

$sql = "SELECT s.*, m.title, t.theater_name 
            FROM showtime s 
            JOIN tb_movie_table m ON s.movie_id = m.movie_id 
            JOIN theater t ON s.theater_id = t.theater_id
            ORDER BY s.show_date DESC";
$showtimes = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <title>Manage Showtimes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-dark text-white min-vh-100 d-flex">
    <?php include '../admin/sidebar_header.php'; ?>

    <div class="flex-grow-1 p-5 overflow-auto">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3">
            <h1 class="fw-bold">Manage Showtimes</h1>
            <button class="btn btn-danger fw-bold"><i class="bi bi-plus-lg"></i> Add Showtime</button>
        </div>
        <div class="card bg-black border-secondary overflow-hidden shadow">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead class="table-active">
                    <tr>
                        <th>ID</th>
                        <th>Movie</th>
                        <th>Theater</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Seats Left</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $showtimes->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['showtime_id']; ?></td>
                            <td class="fw-bold"><?php echo $row['title']; ?></td>
                            <td><?php echo $row['theater_name']; ?></td>
                            <td><?php echo $row['show_date']; ?></td>
                            <td><?php echo date("g:i A", strtotime($row['show_time'])); ?></td>
                            <td><?php echo $row['available_seats']; ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="showtime_id" value="<?php echo $row['showtime_id']; ?>">
                                    <button type="submit" name="delete_showtime" class="btn btn-sm btn-outline-danger"><i
                                            class="bi bi-trash"></i></button>
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