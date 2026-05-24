<?php
session_start();

include "../database/conn.php";

// 1. Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);

$bookings = [];

// 2. If logged in, fetch their bookings
if ($is_logged_in) {
    $user_id = $_SESSION['user_id'];

    // We use JOINs to connect the booking table to the movie, showtime, and seat tables
    // so we can display human-readable details (like title and seat number) instead of just IDs.
    $sql = "SELECT 
                    b.booking_id, 
                    b.booking_date, 
                    m.title, 
                    m.poster, 
                    s.show_date, 
                    s.show_time, 
                    st.seat_number
                FROM booking b
                JOIN tb_movie_table m ON b.movie_id = m.movie_id
                JOIN showtime s ON b.showtime_id = s.showtime_id
                JOIN seat st ON b.seat_id = st.seat_id
                WHERE b.user_id = '$user_id'
                ORDER BY s.show_date DESC, s.show_time DESC";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
    }
}
?>

<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Absolute Cinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-black text-white min-vh-100">

    <div class="container py-5" style="max-width: 1000px;">
        <h1 class="fw-bold mb-5 border-bottom border-secondary pb-3">My Bookings</h1>

        <?php if (!$is_logged_in): ?>

            <div class="card bg-dark border-secondary p-5 text-center rounded-4 shadow">
                <i class="bi bi-lock-fill text-danger mb-3" style="font-size: 4rem;"></i>
                <h3 class="fw-bold">Login Required</h3>
                <p class="text-secondary fs-5 mb-4">Please log in to view and manage your movie tickets.</p>
                <div>
                    <a href="login.php" class="btn btn-danger px-5 py-3 fw-bold fs-5 rounded-3">Go to Login</a>
                </div>
            </div>

        <?php elseif (empty($bookings)): ?>

            <div class="card bg-dark border-secondary p-5 text-center rounded-4 shadow">
                <i class="bi bi-ticket-perforated text-secondary mb-3" style="font-size: 4rem;"></i>
                <h3 class="fw-bold">No Bookings Found</h3>
                <p class="text-secondary fs-5 mb-4">You haven't booked any movies yet. Ready for a cinematic experience?</p>
                <div>
                    <a href="index.php" class="btn btn-danger px-5 py-3 fw-bold fs-5 rounded-3">Browse Movies</a>
                </div>
            </div>

        <?php else: ?>

            <div class="row row-cols-1 g-4">
                <?php foreach ($bookings as $ticket): ?>
                    <div class="col">
                        <div class="card bg-dark border-secondary overflow-hidden rounded-4 shadow-sm hover-effect">
                            <div class="row g-0 h-100">

                                <div class="col-md-3 col-4">
                                    <img src="<?php echo $ticket['poster']; ?>"
                                        class="img-fluid h-100 w-100 object-fit-cover border-end border-secondary"
                                        alt="<?php echo $ticket['title']; ?>" style="min-height: 200px;">
                                </div>

                                <div class="col-md-9 col-8">
                                    <div class="card-body p-4 d-flex flex-column h-100">

                                        <div class="d-flex justify-content-between align-items-start mb-4">
                                            <h4 class="card-title fw-bold text-white mb-0 fs-3">
                                                <?php echo $ticket['title']; ?>
                                            </h4>
                                            <span class="badge bg-danger fs-6 px-3 py-2">Booking #
                                                <?php echo $ticket['booking_id']; ?>
                                            </span>
                                        </div>

                                        <div class="row text-secondary mb-auto g-3">
                                            <div class="col-sm-4">
                                                <small class="d-block text-uppercase fw-bold text-muted mb-1">Date</small>
                                                <span class="fs-5 text-white"><i class="bi bi-calendar-event text-danger"></i>
                                                    <?php echo $ticket['show_date']; ?>
                                                </span>
                                            </div>
                                            <div class="col-sm-4">
                                                <small class="d-block text-uppercase fw-bold text-muted mb-1">Time</small>
                                                <span class="fs-5 text-white"><i class="bi bi-clock text-danger"></i>
                                                    <?php echo date("g:i A", strtotime($ticket['show_time'])); ?>
                                                </span>
                                            </div>
                                            <div class="col-sm-4">
                                                <small class="d-block text-uppercase fw-bold text-muted mb-1">Seat</small>
                                                <span class="fs-5 text-white"><i class="bi bi-person-fill text-danger"></i>
                                                    <?php echo $ticket['seat_number']; ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div
                                            class="text-secondary small mt-4 pt-3 border-top border-secondary border-opacity-50">
                                            Purchased on:
                                            <?php echo date('M d, Y g:i A', strtotime($ticket['booking_date'])); ?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>