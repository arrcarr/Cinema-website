<?php
session_start();
include "../database/conn.php";

$message = '';
//generation of seat based on number admin set
function generate_seats_for_theater($conn, $theater_id)
{
    
    $theater_id = intval($theater_id);
    if ($theater_id <= 0) return;

    $tq = $conn->query("SELECT capacity FROM theater WHERE theater_id = '$theater_id' LIMIT 1");
    if (!$tq || $tq->num_rows == 0) return;
    $cap = intval($tq->fetch_assoc()['capacity']);

    $existing_q = $conn->query("SELECT COUNT(*) as cnt FROM seat WHERE theater_id = '$theater_id'");
    $existing = 0;
    if ($existing_q) {
        $existing = intval($existing_q->fetch_assoc()['cnt']);
    }

    $to_create = $cap - $existing;
    if ($to_create <= 0) return;

    $per_row = 10;
    $rows = ceil($cap / $per_row);
    $created = 0;

    for ($r = 0; $r < $rows && $created < $to_create; $r++) {
        $rowLetter = chr(ord('A') + ($r % 26));
        for ($n = 1; $n <= $per_row && $created < $to_create; $n++) {
            $seat_number = $rowLetter . $n;
            $stmt = $conn->prepare("INSERT INTO seat (theater_id, seat_number) VALUES (?, ?)");
            $stmt->bind_param('is', $theater_id, $seat_number);
            $stmt->execute();
            $stmt->close();
            $created++;
        }
    }
}

if (isset($_POST['add_showtime'])) {
    $movie_id = intval($_POST['movie_id']);
    $theater_id = intval($_POST['theater_id']);
    $show_date = $_POST['show_date'];
    $show_time = $_POST['show_time'];
    $available_seats = intval($_POST['available_seats']);
    $conn->query("INSERT INTO showtime (movie_id, theater_id, show_date, show_time, available_seats) VALUES ('$movie_id', '$theater_id', '$show_date', '$show_time', '$available_seats')");
    // Ensure theater seats exist (auto-generate up to capacity)
    generate_seats_for_theater($conn, $theater_id);
    $message = 'Showtime added.';
}

if (isset($_POST['update_showtime'])) {
    $id = intval($_POST['showtime_id']);
    $movie_id = intval($_POST['movie_id']);
    $theater_id = intval($_POST['theater_id']);
    $show_date = $_POST['show_date'];
    $show_time = $_POST['show_time'];
    $available_seats = intval($_POST['available_seats']);
    $conn->query("UPDATE showtime SET movie_id = '$movie_id', theater_id = '$theater_id', show_date = '$show_date', show_time = '$show_time', available_seats = '$available_seats' WHERE showtime_id = '$id'");
    // Ensure theater seats exist (auto-generate up to capacity)
    generate_seats_for_theater($conn, $theater_id);
    $message = 'Showtime updated.';
}

if (isset($_POST['delete_showtime'])) {
    $id = intval($_POST['showtime_id']);
    $conn->query("DELETE FROM showtime WHERE showtime_id = '$id'");
    $message = 'Showtime deleted.';
}

$sql = "SELECT s.*, m.title, t.theater_name 
            FROM showtime s 
            JOIN tb_movie_table m ON s.movie_id = m.movie_id 
            JOIN theater t ON s.theater_id = t.theater_id
            ORDER BY s.show_date DESC";
$showtimes = $conn->query($sql);
$movies = $conn->query("SELECT movie_id, title FROM tb_movie_table ORDER BY title ASC");
$theaters = $conn->query("SELECT theater_id, theater_name FROM theater ORDER BY theater_name ASC");
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
            <button class="btn btn-danger fw-bold" data-bs-toggle="modal" data-bs-target="#addShowtimeModal"><i class="bi bi-plus-lg"></i> Add Showtime</button>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success border-0 shadow-sm fw-bold"><?php echo $message; ?></div>
        <?php endif; ?>

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
                                <button class="btn btn-sm btn-outline-light me-1" data-bs-toggle="modal"
                                    data-bs-target="#editShowtimeModal<?php echo $row['showtime_id']; ?>"><i class="bi bi-pencil"></i></button>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="showtime_id" value="<?php echo $row['showtime_id']; ?>">
                                    <button type="submit" name="delete_showtime" class="btn btn-sm btn-outline-danger"><i
                                            class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="editShowtimeModal<?php echo $row['showtime_id']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content bg-black border-secondary text-white">
                                    <div class="modal-header border-secondary">
                                        <h5 class="modal-title fw-bold">Update Showtime</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body row g-3">
                                            <input type="hidden" name="showtime_id" value="<?php echo $row['showtime_id']; ?>">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Movie</label>
                                                <select class="form-select bg-dark text-white border-secondary" name="movie_id" required>
                                                    <?php $movies->data_seek(0); while ($movie = $movies->fetch_assoc()): ?>
                                                        <option value="<?php echo $movie['movie_id']; ?>" <?php echo ($movie['movie_id'] == $row['movie_id']) ? 'selected' : ''; ?>><?php echo $movie['title']; ?></option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Theater</label>
                                                <select class="form-select bg-dark text-white border-secondary" name="theater_id" required>
                                                    <?php $theaters->data_seek(0); while ($theater = $theaters->fetch_assoc()): ?>
                                                        <option value="<?php echo $theater['theater_id']; ?>" <?php echo ($theater['theater_id'] == $row['theater_id']) ? 'selected' : ''; ?>><?php echo $theater['theater_name']; ?></option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Date</label>
                                                <input type="date" class="form-control bg-dark text-white border-secondary" name="show_date" value="<?php echo $row['show_date']; ?>" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Time</label>
                                                <input type="time" class="form-control bg-dark text-white border-secondary" name="show_time" value="<?php echo $row['show_time']; ?>" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Available Seats</label>
                                                <input type="number" min="1" class="form-control bg-dark text-white border-secondary" name="available_seats" value="<?php echo $row['available_seats']; ?>" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-secondary">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" name="update_showtime" class="btn btn-danger fw-bold">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addShowtimeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-black border-secondary text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold">Add Showtime</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Movie</label>
                            <select class="form-select bg-dark text-white border-secondary" name="movie_id" required>
                                <option value="">Select movie</option>
                                <?php $movies->data_seek(0); while ($movie = $movies->fetch_assoc()): ?>
                                    <option value="<?php echo $movie['movie_id']; ?>"><?php echo $movie['title']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Theater</label>
                            <select class="form-select bg-dark text-white border-secondary" name="theater_id" required>
                                <option value="">Select theater</option>
                                <?php $theaters->data_seek(0); while ($theater = $theaters->fetch_assoc()): ?>
                                    <option value="<?php echo $theater['theater_id']; ?>"><?php echo $theater['theater_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Date</label>
                            <input type="date" class="form-control bg-dark text-white border-secondary" name="show_date" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Time</label>
                            <input type="time" class="form-control bg-dark text-white border-secondary" name="show_time" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Available Seats</label>
                            <input type="number" min="1" class="form-control bg-dark text-white border-secondary" name="available_seats" required>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_showtime" class="btn btn-danger fw-bold">Add Showtime</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>