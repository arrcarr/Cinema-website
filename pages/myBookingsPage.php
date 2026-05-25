<?php
session_start();
require_once '../database/conn.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$userId = (int) $_SESSION['user_id'];

$stmt = $conn->prepare('SELECT b.booking_id, b.booking_date, b.num_tickets, m.title, m.poster, s.show_date, s.show_time, t.theater_name, se.seat_number FROM booking b INNER JOIN tb_movie_table m ON m.movie_id = b.movie_id INNER JOIN showtime s ON s.showtime_id = b.showtime_id INNER JOIN theater t ON t.theater_id = s.theater_id INNER JOIN seat se ON se.seat_id = b.seat_id WHERE b.user_id = ? ORDER BY b.booking_date DESC, s.show_date DESC, s.show_time DESC');
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$bookings = [];

while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Absolute Cinema</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        html {
            scrollbar-gutter: stable;
        }

        body {
            background: radial-gradient(circle at top, rgba(220, 53, 69, 0.14), transparent 28%), #050505;
            color: #fff;
            overflow-y: scroll;
        }

        .page-shell {
            min-height: calc(100vh - 80px);
        }

        .panel-card {
            background: linear-gradient(180deg, rgba(18, 18, 18, 0.98), rgba(10, 10, 10, 0.98));
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.4);
            border-radius: 1.25rem;
        }

        .booking-poster {
            width: 92px;
            height: 132px;
            object-fit: cover;
            border-radius: 0.9rem;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container py-5 page-shell">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div>
                <h1 class="fw-bold mb-1">My Bookings</h1>
                <p class="text-white-50 mb-0">Your saved booking history from the database.</p>
            </div>
            <a href="moviesPage.php" class="btn btn-danger fw-semibold">Book Another Movie</a>
        </div>

        <?php if (empty($bookings)): ?>
            <div class="panel-card p-5 text-center">
                <h2 class="fw-bold mb-3">No bookings yet</h2>
                <p class="text-white-50 mb-4">You haven’t booked any tickets yet.</p>
                <a href="moviesPage.php" class="btn btn-danger px-4 fw-semibold">Browse Movies</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($bookings as $booking): ?>
                    <div class="col-12">
                        <div class="panel-card p-3 p-md-4">
                            <div class="row g-3 align-items-center">
                                <div class="col-auto">
                                    <img src="<?php echo e($booking['poster']); ?>" alt="<?php echo e($booking['title']); ?>" class="booking-poster">
                                </div>
                                <div class="col">
                                    <div class="d-flex flex-wrap justify-content-between gap-3">
                                        <div>
                                            <h2 class="h4 fw-bold mb-1"><?php echo e($booking['title']); ?></h2>
                                            <div class="text-white-50 small mb-2">Booked on <?php echo e(date('M d, Y g:i A', strtotime($booking['booking_date']))); ?></div>
                                            <div class="d-flex flex-wrap gap-3 text-white-50 small">
                                                <span>Date: <?php echo e(date('M d, Y', strtotime($booking['show_date']))); ?></span>
                                                <span>Time: <?php echo e(date('g:i A', strtotime($booking['show_time']))); ?></span>
                                                <span>Theater: <?php echo e($booking['theater_name']); ?></span>
                                                <span>Seat: <?php echo e($booking['seat_number']); ?></span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="text-white-50 small">Tickets</div>
                                            <div class="fw-bold fs-5"><?php echo (int) $booking['num_tickets']; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>