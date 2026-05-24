<?php
session_start();
include "../database/conn.php";

// 1. Get the Movie ID from the URL (e.g., movie_desc.php?id=1)
$movie_id = isset($_GET['id']) ? $_GET['id'] : 1;

// 2. Fetch Movie Details
$sql = "SELECT movie_id, title, genre, duration, release_date, description, poster, status FROM tb_movie_table WHERE movie_id = '$movie_id'";
$result = $conn->query($sql);
$movie = $result->fetch_assoc();

// Redirect or show error if movie doesn't exist
if (!$movie) {
    echo '<div class="min-vh-100 bg-black d-flex align-items-center justify-content-center text-white">
                <div class="text-center">
                    <h1>Movie Not Found</h1>
                    <a href="index.php" class="btn btn-danger mt-3">Back to Movies</a>
                </div>
              </div>';
    exit();
}

// 3. Handle Booking Form Submission (Create/Insert)
$booking_success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_tickets'])) {
    // In a real app, user_id comes from the logged-in session. We default to 1 here.
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
    $showtime_id = $_POST['showtime_id'];
    $selected_seats = isset($_POST['seats']) ? $_POST['seats'] : [];
    $num_tickets = count($selected_seats);

    if ($num_tickets > 0 && !empty($showtime_id)) {
        // Insert a booking record for EACH selected seat
        foreach ($selected_seats as $seat_id) {
            $insert_sql = "INSERT INTO booking (showtime_id, user_id, movie_id, seat_id, num_tickets) 
                               VALUES ('$showtime_id', '$user_id', '$movie_id', '$seat_id', 1)";
            $conn->query($insert_sql);
        }
        $booking_success = true;
    }
}
?>

<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $movie['title']; ?> - Absolute Cinema
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .hero-section {
            position: relative;
            height: 400px;
            background-color: #000;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 1) 0%, rgba(0, 0, 0, 0.7) 50%, rgba(0, 0, 0, 0) 100%);
            z-index: 10;
        }

        .hero-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.5;
        }

        .hero-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 20;
        }

        /* Custom checkbox styling for seats to make them look like buttons */
        .btn-check:checked+.btn-outline-secondary {
            background-color: #dc3545;
            /* Bootstrap danger */
            border-color: #dc3545;
            color: white;
        }
    </style>
</head>

<body class="bg-black text-white min-vh-100">

    <div class="hero-section">
        <div class="hero-overlay"></div>
        <img src="<?php echo $movie['poster']; ?>" alt="<?php echo $movie['title']; ?>" class="hero-img">
        <div class="hero-content pb-4">
            <div class="container" style="max-width: 1200px;">
                <h1 class="display-4 fw-bold mb-3">
                    <?php echo $movie['title']; ?>
                </h1>
                <div class="d-flex flex-wrap gap-4 text-light">
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-calendar"></i>
                        <?php echo $movie['release_date']; ?>
                    </span>
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-clock"></i>
                        <?php echo $movie['duration']; ?>
                    </span>
                    <span class="d-flex align-items-center gap-2 text-capitalize">
                        <i class="bi bi-film"></i>
                        <?php echo $movie['genre']; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5" style="max-width: 1200px;">

        <?php if ($booking_success): ?>
            <div class="alert alert-success bg-success text-white border-0 alert-dismissible fade show" role="alert">
                <strong>Success!</strong> Your booking has been confirmed!
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-5">
            <div class="col-lg-8">
                <h2 class="fw-bold mb-3">Synopsis</h2>
                <p class="text-secondary mb-5 lh-lg">
                    <?php echo $movie['description']; ?>
                </p>

                <h3 class="fw-bold fs-5 mb-2">Current Status</h3>
                <p>
                    <?php if ($movie['status'] == 'released'): ?>
                        <span class="badge bg-danger px-3 py-2 fs-6">Now Showing</span>
                    <?php else: ?>
                        <span class="badge bg-secondary px-3 py-2 fs-6">Coming Soon</span>
                    <?php endif; ?>
                </p>
            </div>

            <div class="col-lg-4">

                <?php if ($movie['status'] == 'released'): ?>
                    <div class="card bg-dark border-secondary p-4">
                        <h3 class="fw-bold mb-4">Book Tickets</h3>

                        <form method="POST" action="">

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-light">Select Showtime</label>
                                <select class="form-select bg-black text-white border-secondary" name="showtime_id"
                                    required>
                                    <option value="" selected disabled>Choose a date & time...</option>
                                    <?php
                                    // Fetch available showtimes for this specific movie
                                    $showtime_sql = "SELECT showtime_id, show_date, show_time FROM showtime WHERE movie_id = '$movie_id'";
                                    $showtime_result = $conn->query($showtime_sql);

                                    if ($showtime_result->num_rows > 0) {
                                        while ($st = $showtime_result->fetch_assoc()) {
                                            echo "<option value='" . $st['showtime_id'] . "'>" . $st['show_date'] . " @ " . $st['show_time'] . "</option>";
                                        }
                                    } else {
                                        echo "<option value='' disabled>No showtimes scheduled yet.</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-light mb-3">Select Seats</label>

                                <div class="bg-black p-3 rounded border border-secondary text-center">
                                    <div class="w-75 mx-auto bg-danger mb-4"
                                        style="height: 4px; border-radius: 50% 50% 0 0;"></div>
                                    <small class="text-secondary d-block mb-3">SCREEN</small>

                                    <div class="d-flex flex-wrap justify-content-center gap-2">
                                        <?php
                                        // Ideally, seats are linked to a specific theater. 
                                        // For simplicity, we are pulling a general list of seats.
                                        $seat_sql = "SELECT seat_id, seat_number FROM seat LIMIT 20";
                                        $seat_result = $conn->query($seat_sql);

                                        if ($seat_result->num_rows > 0) {
                                            while ($seat = $seat_result->fetch_assoc()) {
                                                $sid = $seat['seat_id'];
                                                $snum = $seat['seat_number'];
                                                echo "
                                                    <div>
                                                        <input type='checkbox' class='btn-check seat-checkbox' name='seats[]' value='$sid' id='seat_$sid' autocomplete='off'>
                                                        <label class='btn btn-outline-secondary btn-sm rounded-top p-2' for='seat_$sid' style='width: 40px;'>
                                                            <i class='bi bi-person-fill d-block'></i>
                                                            <small style='font-size: 10px;'>$snum</small>
                                                        </label>
                                                    </div>";
                                            }
                                        } else {
                                            echo "<p class='text-secondary small'>No seats configured in database.</p>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" name="book_tickets" class="btn btn-danger w-100 py-3 fw-bold">
                                Confirm Booking
                            </button>
                        </form>
                    </div>

                <?php else: ?>
                    <div class="card bg-dark border-secondary p-5 text-center">
                        <i class="bi bi-calendar-event fs-1 text-secondary mb-3"></i>
                        <h3 class="fw-bold mb-3">Coming Soon</h3>
                        <p class="text-secondary mb-0">
                            This movie will be available for booking soon. Check back closer to the release date!
                        </p>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>