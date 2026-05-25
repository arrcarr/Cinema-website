<?php
session_start();
include "../database/conn.php";

$message = '';

if (isset($_POST['add_reservation'])) {
    $user_id = intval($_POST['user_id']);
    $movie_id = intval($_POST['movie_id']);
    $showtime_id = intval($_POST['showtime_id']);
    $seat_id = intval($_POST['seat_id']);
    $num_tickets = intval($_POST['num_tickets']);
    $status = $_POST['status'];
    $conn->query("INSERT INTO booking (showtime_id, user_id, movie_id, seat_id, num_tickets, status) VALUES ('$showtime_id', '$user_id', '$movie_id', '$seat_id', '$num_tickets', '$status')");
    $message = 'Reservation added.';
}

if (isset($_POST['update_reservation'])) {
    $id = intval($_POST['booking_id']);
    $status = $_POST['status'];
    $conn->query("UPDATE booking SET status = '$status' WHERE booking_id = '$id'");
    $message = 'Reservation updated.';
}

if (isset($_POST['delete_reservation'])) {
    $id = intval($_POST['booking_id']);
    $conn->query("DELETE FROM booking WHERE booking_id = '$id'");
    $message = 'Reservation deleted.';
}

$sql = "SELECT b.booking_id, b.booking_date, b.status, m.title, u.email, s.show_date, s.show_time, st.seat_number 
            FROM booking b
            JOIN tb_movie_table m ON b.movie_id = m.movie_id
            JOIN user u ON b.user_id = u.user_id
            JOIN showtime s ON b.showtime_id = s.showtime_id
            JOIN seat st ON b.seat_id = st.seat_id
            ORDER BY b.booking_date DESC";
$reservations = $conn->query($sql);
$users = $conn->query("SELECT user_id, username, email FROM user ORDER BY user_id DESC");
$movies = $conn->query("SELECT movie_id, title FROM tb_movie_table ORDER BY title ASC");
$showtimes = $conn->query("SELECT showtime_id, show_date, show_time FROM showtime ORDER BY show_date DESC, show_time DESC");
$seats = $conn->query("SELECT seat_id, seat_number FROM seat ORDER BY seat_number ASC");
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-dark text-white min-vh-100 d-flex">
    <?php include '../admin/sidebar_header.php'; ?>

    <div class="flex-grow-1 p-5 overflow-auto">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3">
            <h1 class="fw-bold">Reservations</h1>
            <button class="btn btn-danger fw-bold" data-bs-toggle="modal" data-bs-target="#addReservationModal"><i class="bi bi-plus-lg"></i> Add Reservation</button>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success border-0 shadow-sm fw-bold"><?php echo $message; ?></div>
        <?php endif; ?>

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
                    <?php if ($reservations && $reservations->num_rows > 0): ?>
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
                                    if (($row['status'] ?? '') === 'Approved') {
                                        $badge = 'bg-success';
                                    } elseif (($row['status'] ?? '') === 'Rejected') {
                                        $badge = 'bg-danger';
                                    }
                                    ?>
                                    <span class="badge <?php echo $badge; ?>"><?php echo $row['status'] ?? 'Pending'; ?></span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#editReservationModal<?php echo $row['booking_id']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                                            <button type="submit" name="delete_reservation" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this reservation?');">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <div class="modal fade" id="editReservationModal<?php echo $row['booking_id']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content bg-black border-secondary text-white">
                                        <div class="modal-header border-secondary">
                                            <h5 class="modal-title fw-bold">Update Reservation</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Status</label>
                                                    <select class="form-select bg-dark text-white border-secondary" name="status" required>
                                                        <option value="Pending" <?php echo (($row['status'] ?? '') === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="Approved" <?php echo (($row['status'] ?? '') === 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                                        <option value="Rejected" <?php echo (($row['status'] ?? '') === 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-secondary">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" name="update_reservation" class="btn btn-danger fw-bold">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-secondary">No reservations found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="modal fade" id="addReservationModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content bg-black border-secondary text-white">
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title fw-bold">Add Reservation</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">User</label>
                                <select class="form-select bg-dark text-white border-secondary" name="user_id" required>
                                    <option value="">Select user</option>
                                    <?php while ($user = $users->fetch_assoc()): ?>
                                        <option value="<?php echo $user['user_id']; ?>"><?php echo $user['username']; ?> (<?php echo $user['email']; ?>)</option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Movie</label>
                                <select class="form-select bg-dark text-white border-secondary" name="movie_id" required>
                                    <option value="">Select movie</option>
                                    <?php while ($movie = $movies->fetch_assoc()): ?>
                                        <option value="<?php echo $movie['movie_id']; ?>"><?php echo $movie['title']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Showtime</label>
                                <select class="form-select bg-dark text-white border-secondary" name="showtime_id" required>
                                    <option value="">Select showtime</option>
                                    <?php while ($showtime = $showtimes->fetch_assoc()): ?>
                                        <option value="<?php echo $showtime['showtime_id']; ?>"><?php echo $showtime['show_date']; ?> @ <?php echo date('g:i A', strtotime($showtime['show_time'])); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Seat</label>
                                <select class="form-select bg-dark text-white border-secondary" name="seat_id" required>
                                    <option value="">Select seat</option>
                                    <?php while ($seat = $seats->fetch_assoc()): ?>
                                        <option value="<?php echo $seat['seat_id']; ?>"><?php echo $seat['seat_number']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tickets</label>
                                <input type="number" min="1" class="form-control bg-dark text-white border-secondary" name="num_tickets" value="1" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Status</label>
                                <select class="form-select bg-dark text-white border-secondary" name="status" required>
                                    <option value="Pending">Pending</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-secondary">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_reservation" class="btn btn-danger fw-bold">Add Reservation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>