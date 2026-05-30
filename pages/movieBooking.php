<?php
session_start();
require_once '../database/conn.php';
require_once '../validation/sendBookingEmail.php';

if (!isset($_SESSION['user_id'])) {
	header('Location: login.php');
	exit();
}

function e($value)
{
	return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function formatMovieDuration($duration)
{
	if (!$duration) {
		return 'N/A';
	}

	return date('H:i', strtotime($duration));
}

function ensureSeatsForTheater($conn, $theaterId, $capacity)
{
	$theaterId = (int) $theaterId;
	$capacity = (int) $capacity;

	if ($theaterId <= 0 || $capacity <= 0) {
		return;
	}

	$stmt = $conn->prepare('SELECT COUNT(*) AS total FROM seat WHERE theater_id = ?');
	if (!$stmt) {
		return;
	}

	$stmt->bind_param('i', $theaterId);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result ? $result->fetch_assoc() : ['total' => 0];
	$stmt->close();

	if ((int) ($row['total'] ?? 0) > 0) {
		return;
	}

	$perRow = 10;
	$rows = (int) ceil($capacity / $perRow);
	$created = 0;

	for ($rowIndex = 0; $rowIndex < $rows && $created < $capacity; $rowIndex++) {
		$rowLetter = chr(ord('A') + ($rowIndex % 26));
		for ($seatNumber = 1; $seatNumber <= $perRow && $created < $capacity; $seatNumber++) {
			$seatLabel = $rowLetter . $seatNumber;
			$insert = $conn->prepare('INSERT INTO seat (theater_id, seat_number) VALUES (?, ?)');
			if (!$insert) {
				return;
			}

			$insert->bind_param('is', $theaterId, $seatLabel);
			$insert->execute();
			$insert->close();
			$created++;
		}
	}
}

function seatSortValue($seatNumber)
{
	$seatNumber = strtoupper(trim((string) $seatNumber));
	$row = substr($seatNumber, 0, 1);
	$number = (int) preg_replace('/[^0-9]/', '', substr($seatNumber, 1));

	return ord($row) * 100 + $number;
}

function bindStatementParams(mysqli_stmt $stmt, string $types, array &$params): bool
{
	$references = [$types];
	foreach ($params as $index => $value) {
		$references[$index + 1] = &$params[$index];
	}

	return $stmt->bind_param(...$references);
}

$ticketPrice = 12;
$flashMessage = $_SESSION['booking_flash'] ?? '';
$flashType = $_SESSION['booking_flash_type'] ?? 'success';
unset($_SESSION['booking_flash'], $_SESSION['booking_flash_type']);

$currentUser = null;
$userStmt = $conn->prepare('SELECT username, email, fname, lname FROM user WHERE user_id = ? LIMIT 1');
if ($userStmt) {
	$currentUserId = (int) $_SESSION['user_id'];
	$userStmt->bind_param('i', $currentUserId);
	$userStmt->execute();
	$userResult = $userStmt->get_result();
	$currentUser = $userResult ? $userResult->fetch_assoc() : null;
	$userStmt->close();
}

$movieId = (int) ($_POST['movie_id'] ?? $_SESSION['selected_movie_id'] ?? 0);
$selectedShowtimeId = (int) ($_POST['showtime_id'] ?? $_SESSION['selected_showtime_id'] ?? 0);
$selectedSeatIds = [];
$formError = '';

if ($movieId <= 0) {
	$movie = null;
} else {
	$movieStmt = $conn->prepare('SELECT movie_id, title, genre, duration, release_date, description, poster, status FROM tb_movie_table WHERE movie_id = ? LIMIT 1');
	$movieStmt->bind_param('i', $movieId);
	$movieStmt->execute();
	$movieResult = $movieStmt->get_result();
	$movie = $movieResult ? $movieResult->fetch_assoc() : null;
	$movieStmt->close();
}

if ($movie && isset($_POST['book_tickets'])) {
	$selectedShowtimeId = (int) ($_POST['showtime_id'] ?? 0);
	$selectedSeatIds = isset($_POST['seat_ids']) && is_array($_POST['seat_ids']) ? array_values(array_unique(array_filter(array_map('intval', $_POST['seat_ids'])))) : [];

	if ($selectedShowtimeId <= 0) {
		$formError = 'Please choose a showtime before booking.';
	} elseif (count($selectedSeatIds) === 0) {
		$formError = 'Please select at least one seat.';
	} else {
		$showtimeStmt = $conn->prepare('SELECT s.showtime_id, s.movie_id, s.theater_id, s.show_date, s.show_time, s.available_seats, t.theater_name, t.capacity FROM showtime s INNER JOIN theater t ON t.theater_id = s.theater_id WHERE s.showtime_id = ? AND s.movie_id = ? LIMIT 1');
		$showtimeStmt->bind_param('ii', $selectedShowtimeId, $movieId);
		$showtimeStmt->execute();
		$showtimeResult = $showtimeStmt->get_result();
		$selectedShowtime = $showtimeResult ? $showtimeResult->fetch_assoc() : null;
		$showtimeStmt->close();

		if (!$selectedShowtime) {
			$formError = 'The selected showtime is not available for this movie.';
		} elseif (count($selectedSeatIds) > (int) $selectedShowtime['available_seats']) {
			$formError = 'Not enough seats are left for that showtime.';
		} else {
			ensureSeatsForTheater($conn, $selectedShowtime['theater_id'], $selectedShowtime['capacity']);

			$placeholders = implode(',', array_fill(0, count($selectedSeatIds), '?'));
			$types = str_repeat('i', count($selectedSeatIds));
			$seatCheckSql = 'SELECT seat_id, seat_number FROM seat WHERE theater_id = ? AND seat_id IN (' . $placeholders . ')';
			$seatCheckStmt = $conn->prepare($seatCheckSql);
			if ($seatCheckStmt) {
				$seatParams = array_merge([$selectedShowtime['theater_id']], $selectedSeatIds);
				$bindTypes = 'i' . $types;
				bindStatementParams($seatCheckStmt, $bindTypes, $seatParams);
				$seatCheckStmt->execute();
				$seatResult = $seatCheckStmt->get_result();
				$validSeatIds = [];
				$validSeatNumbers = [];

				while ($seatRow = $seatResult->fetch_assoc()) {
					$validSeatIds[] = (int) $seatRow['seat_id'];
					$validSeatNumbers[(int) $seatRow['seat_id']] = $seatRow['seat_number'];
				}

				$seatCheckStmt->close();

				if (count($validSeatIds) !== count($selectedSeatIds)) {
					$formError = 'One or more selected seats do not belong to this theater.';
				} else {
					$bookedStmt = $conn->prepare('SELECT seat_id FROM booking WHERE showtime_id = ? AND seat_id IN (' . $placeholders . ')');
					if ($bookedStmt) {
						$bookedParams = array_merge([$selectedShowtimeId], $selectedSeatIds);
						bindStatementParams($bookedStmt, $bindTypes, $bookedParams);
						$bookedStmt->execute();
						$bookedResult = $bookedStmt->get_result();
						$bookedSeatIds = [];

						while ($bookedRow = $bookedResult->fetch_assoc()) {
							$bookedSeatIds[] = (int) $bookedRow['seat_id'];
						}

						$bookedStmt->close();

						if (!empty($bookedSeatIds)) {
							$formError = 'One or more selected seats have already been booked.';
						} else {
							$conn->begin_transaction();

							try {
								$insertStmt = $conn->prepare('INSERT INTO booking (showtime_id, user_id, movie_id, seat_id, num_tickets) VALUES (?, ?, ?, ?, 1)');

								if (!$insertStmt) {
									throw new Exception('Unable to prepare booking statement.');
								}

								foreach ($selectedSeatIds as $seatId) {
									$userId = (int) $_SESSION['user_id'];
									$insertStmt->bind_param('iiii', $selectedShowtimeId, $userId, $movieId, $seatId);
									if (!$insertStmt->execute()) {
										throw new Exception('Unable to save booking.');
									}
								}

								$insertStmt->close();

								$newAvailableSeats = max(0, (int) $selectedShowtime['available_seats'] - count($selectedSeatIds));
								$updateStmt = $conn->prepare('UPDATE showtime SET available_seats = ? WHERE showtime_id = ?');
								if (!$updateStmt) {
									throw new Exception('Unable to update showtime availability.');
								}

								$updateStmt->bind_param('ii', $newAvailableSeats, $selectedShowtimeId);
								if (!$updateStmt->execute()) {
									throw new Exception('Unable to update showtime availability.');
								}

								$updateStmt->close();
								$conn->commit();

								$seatNames = [];
								foreach ($selectedSeatIds as $seatId) {
									if (isset($validSeatNumbers[$seatId])) {
										$seatNames[] = $validSeatNumbers[$seatId];
									}
								}

								$_SESSION['booking_flash_type'] = 'success';
								$_SESSION['booking_flash'] = 'Booking confirmed for ' . $movie['title'] . ' on ' . date('M d, Y', strtotime($selectedShowtime['show_date'])) . ' at ' . date('g:i A', strtotime($selectedShowtime['show_time'])) . ' for seats ' . implode(', ', $seatNames) . '.';

								$fullName = trim((string) ($currentUser['fname'] ?? '') . ' ' . (string) ($currentUser['lname'] ?? ''));
								if ($fullName === '') {
									$fullName = $currentUser['username'] ?? 'Guest';
								}

								$showtimeText = date('M d, Y', strtotime($selectedShowtime['show_date'])) . ' at ' . date('g:i A', strtotime($selectedShowtime['show_time']));
								[$bookingEmailSent, $bookingEmailError] = send_booking_confirmation($fullName, $currentUser['email'] ?? '', $movie['title'], $showtimeText, $seatNames);
								if (!$bookingEmailSent) {
									$_SESSION['booking_flash_type'] = 'warning';
									$_SESSION['booking_flash'] .= ' The booking was saved, but the verification email could not be sent' . ($bookingEmailError ? ': ' . $bookingEmailError : '.') ;
								}

								$_SESSION['selected_movie_id'] = $movieId;
								$_SESSION['selected_showtime_id'] = $selectedShowtimeId;
								header('Location: movieBooking.php');
								exit();
							} catch (Exception $exception) {
								$conn->rollback();
								$formError = $exception->getMessage();
							}
						}
					} else {
						$formError = 'Unable to check booked seats.';
					}
				}
			} else {
				$formError = 'Unable to load seat information.';
			}
		}
	}
}

$showtimes = [];
$showtimesByDate = [];
$selectedShowtime = null;
$seatRows = [];
$bookedSeatIds = [];

if ($movie) {
	$_SESSION['selected_movie_id'] = $movieId;

	$showtimeStmt = $conn->prepare('SELECT s.showtime_id, s.show_date, s.show_time, s.available_seats, t.theater_id, t.theater_name, t.capacity FROM showtime s INNER JOIN theater t ON t.theater_id = s.theater_id WHERE s.movie_id = ? ORDER BY s.show_date ASC, s.show_time ASC');
	$showtimeStmt->bind_param('i', $movieId);
	$showtimeStmt->execute();
	$showtimeResult = $showtimeStmt->get_result();

	while ($row = $showtimeResult->fetch_assoc()) {
		$showtimes[] = $row;
		$showtimesByDate[$row['show_date']][] = $row;
	}

	$showtimeStmt->close();

	if ($selectedShowtimeId <= 0 && !empty($showtimes)) {
		$selectedShowtimeId = (int) $showtimes[0]['showtime_id'];
	}

	if ($selectedShowtimeId > 0) {
		$_SESSION['selected_showtime_id'] = $selectedShowtimeId;
	}

	foreach ($showtimes as $showtime) {
		if ((int) $showtime['showtime_id'] === $selectedShowtimeId) {
			$selectedShowtime = $showtime;
			break;
		}
	}

	if ($selectedShowtime) {
		ensureSeatsForTheater($conn, $selectedShowtime['theater_id'], $selectedShowtime['capacity']);

		$seatStmt = $conn->prepare('SELECT seat_id, seat_number FROM seat WHERE theater_id = ? ORDER BY SUBSTRING(seat_number, 1, 1) ASC, CAST(SUBSTRING(seat_number, 2) AS UNSIGNED) ASC');
		$seatStmt->bind_param('i', $selectedShowtime['theater_id']);
		$seatStmt->execute();
		$seatResult = $seatStmt->get_result();

		while ($seat = $seatResult->fetch_assoc()) {
			$seatRows[] = $seat;
		}

		$seatStmt->close();

		if (!empty($seatRows)) {
			$bookedStmt = $conn->prepare('SELECT seat_id FROM booking WHERE showtime_id = ?');
			$bookedStmt->bind_param('i', $selectedShowtimeId);
			$bookedStmt->execute();
			$bookedResult = $bookedStmt->get_result();

			while ($row = $bookedResult->fetch_assoc()) {
				$bookedSeatIds[] = (int) $row['seat_id'];
			}

			$bookedStmt->close();
		}

		usort($seatRows, static function ($left, $right) {
			return seatSortValue($left['seat_number']) <=> seatSortValue($right['seat_number']);
		});
	}
}

$seatGroups = [];
foreach ($seatRows as $seat) {
	$seatNumber = strtoupper(trim($seat['seat_number']));
	$rowKey = substr($seatNumber, 0, 1);
	$seatGroups[$rowKey][] = $seat;
}

ksort($seatGroups);
$selectedSeatIdsFromPost = isset($_POST['seat_ids']) && is_array($_POST['seat_ids']) ? array_map('intval', $_POST['seat_ids']) : [];
if (!empty($selectedSeatIds)) {
	$selectedSeatIdsFromPost = $selectedSeatIds;
}
$totalSelectedSeats = count($selectedSeatIdsFromPost);
$totalPrice = 300;
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $movie ? e($movie['title']) . ' - Booking' : 'Movie Booking'; ?></title>
	<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
	<style>
		body {
			background: radial-gradient(circle at top, rgba(220, 53, 69, 0.14), transparent 28%), #050505;
			color: #fff;
		}

		.hero-shell {
			min-height: 420px;
			position: relative;
			overflow: hidden;
			border-bottom: 1px solid rgba(255, 255, 255, 0.08);
		}

		.hero-shell::before {
			content: '';
			position: absolute;
			inset: 0;
			background: linear-gradient(90deg, rgba(5, 5, 5, 0.96) 0%, rgba(5, 5, 5, 0.76) 42%, rgba(5, 5, 5, 0.35) 100%);
			z-index: 1;
		}

		.hero-image {
			position: absolute;
			inset: 0;
			width: 100%;
			height: 100%;
			object-fit: cover;
			opacity: 0.45;
		}



        

		.panel-card {
			background: linear-gradient(180deg, rgba(18, 18, 18, 0.98), rgba(10, 10, 10, 0.98));
			border: 1px solid rgba(255, 255, 255, 0.08);
			box-shadow: 0 18px 50px rgba(0, 0, 0, 0.4);
			border-radius: 1.25rem;
		}

		.showtime-pill {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			min-height: 54px;
			text-decoration: none;
			border-radius: 0.9rem;
			border: 1px solid rgba(255, 255, 255, 0.08);
			background: rgba(255, 255, 255, 0.03);
			color: #fff;
			transition: transform 0.18s ease, border-color 0.18s ease, background-color 0.18s ease;
		}

		.showtime-pill:hover {
			transform: translateY(-1px);
			border-color: rgba(220, 53, 69, 0.8);
			background: rgba(220, 53, 69, 0.12);
			color: #fff;
		}

		.showtime-pill.active {
			background: #dc3545;
			border-color: #dc3545;
			color: #fff;
			box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.04) inset;
		}

		.seat-grid {
			display: grid;
			gap: 0.65rem;
		}

		.seat-row {
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 0.45rem;
			flex-wrap: wrap;
		}

		.seat-label {
			width: 2.6rem;
			text-align: center;
			font-weight: 700;
			color: rgba(255, 255, 255, 0.8);
		}

		.seat-box {
			position: absolute;
			opacity: 0;
			pointer-events: none;
		}

		.seat-tile {
			width: 2.5rem;
			height: 2.5rem;
			border-radius: 0.7rem 0.7rem 0.35rem 0.35rem;
			border: 1px solid rgba(255, 255, 255, 0.1);
			background: #2b2b2b;
			color: #fff;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			font-size: 0.72rem;
			font-weight: 700;
			cursor: pointer;
			transition: transform 0.15s ease, background-color 0.15s ease, border-color 0.15s ease;
			user-select: none;
		}

		.seat-tile:hover {
			transform: translateY(-1px);
			border-color: rgba(220, 53, 69, 0.6);
		}

		.seat-box:checked + .seat-tile {
			background: #dc3545;
			border-color: #dc3545;
		}

		.seat-box:disabled + .seat-tile {
			background: #151515;
			color: rgba(255, 255, 255, 0.25);
			border-color: rgba(255, 255, 255, 0.05);
			cursor: not-allowed;
			text-decoration: line-through;
		}

		.seat-box:checked:disabled + .seat-tile {
			background: #dc3545;
			color: #fff;
			text-decoration: none;
		}

		.screen-bar {
			height: 0.6rem;
			border-radius: 999px 999px 0 0;
			background: linear-gradient(90deg, #dc3545, #ff6b6b, #dc3545);
			box-shadow: 0 0 22px rgba(220, 53, 69, 0.35);
		}

		.movie-poster {
			border-radius: 1.1rem;
			border: 1px solid rgba(255, 255, 255, 0.08);
			box-shadow: 0 12px 30px rgba(0, 0, 0, 0.5);
		}
	</style>
</head>

<body>
	<?php include 'header.php'; ?>

	<?php if (!$movie): ?>
		<div class="container py-5">
			<div class="panel-card p-5 text-center mx-auto" style="max-width: 700px;">
				<h1 class="fw-bold mb-3">Movie Not Found</h1>
				<p class="text-secondary mb-4">The movie you requested is not available in the database.</p>
				<a href="moviesPage.php" class="btn btn-danger px-4 fw-semibold">Back to Movies</a>
			</div>
		</div>
	<?php else: ?>
		<section class="hero-shell">
			<img src="<?php echo e($movie['poster']); ?>" alt="<?php echo e($movie['title']); ?>" class="hero-image">
			<div class="position-relative z-2 h-100 d-flex align-items-end">
				<div class="container py-5">
					<div class="row align-items-end g-4">
						<div class="col-lg-8">
							<span class="badge rounded-pill text-bg-danger px-3 py-2 mb-3 text-uppercase">Movie Booking</span>
							<h1 class="display-4 fw-bold mb-3"><?php echo e($movie['title']); ?></h1>
							<div class="d-flex flex-wrap gap-3 text-white-50">
								<span class="badge rounded-pill text-bg-dark border border-secondary border-opacity-25 px-3 py-2"><?php echo e($movie['genre']); ?></span>
								<span class="badge rounded-pill text-bg-dark border border-secondary border-opacity-25 px-3 py-2"><?php echo formatMovieDuration($movie['duration']); ?></span>
								<span class="badge rounded-pill text-bg-dark border border-secondary border-opacity-25 px-3 py-2">Release: <?php echo e(date('M d, Y', strtotime($movie['release_date']))); ?></span>
								<span class="badge rounded-pill text-bg-dark border border-secondary border-opacity-25 px-3 py-2 text-capitalize"><?php echo e($movie['status']); ?></span>
							</div>
						</div>
						<div class="col-lg-4 text-lg-end">
							<a href="#booking-panel" class="btn btn-danger btn-lg px-4 fw-semibold">Book Tickets</a>
						</div>
					</div>
				</div>
			</div>
		</section>

		<main class="container py-5">
			<?php if (!empty($flashMessage)): ?>
				<div class="alert alert-<?php echo e($flashType); ?> border-0 rounded-4 shadow-sm mb-4">
					<?php echo e($flashMessage); ?>
				</div>
			<?php endif; ?>

			<?php if (!empty($formError)): ?>
				<div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
					<?php echo e($formError); ?>
				</div>
			<?php endif; ?>

			<div class="row g-4 mb-4">
				<div class="col-lg-8">
					<div class="panel-card p-4 p-lg-5 h-100">
						<div class="row g-4">
							<div class="col-md-5 col-lg-4">
								<img src="<?php echo e($movie['poster']); ?>" alt="<?php echo e($movie['title']); ?>" class="img-fluid movie-poster w-100">
							</div>
							<div class="col-md-7 col-lg-8">
								<h2 class="h3 fw-bold mb-3">Synopsis</h2>
								<p class="text-white-50 mb-4"><?php echo e($movie['description']); ?></p>

								<div class="row g-3">
									<div class="col-sm-6">
										<div class="p-3 rounded-4 bg-black bg-opacity-50 border border-secondary border-opacity-25 h-100">
											<div class="text-white-50 small mb-1">Genre</div>
											<div class="fw-semibold text-capitalize"><?php echo e($movie['genre']); ?></div>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="p-3 rounded-4 bg-black bg-opacity-50 border border-secondary border-opacity-25 h-100">
											<div class="text-white-50 small mb-1">Duration</div>
											<div class="fw-semibold"><?php echo formatMovieDuration($movie['duration']); ?></div>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="p-3 rounded-4 bg-black bg-opacity-50 border border-secondary border-opacity-25 h-100">
											<div class="text-white-50 small mb-1">Release Date</div>
											<div class="fw-semibold"><?php echo e(date('M d, Y', strtotime($movie['release_date']))); ?></div>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="p-3 rounded-4 bg-black bg-opacity-50 border border-secondary border-opacity-25 h-100">
											<div class="text-white-50 small mb-1">Status</div>
											<div class="fw-semibold text-capitalize"><?php echo e($movie['status']); ?></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-lg-4">
					<div class="panel-card p-4 h-100">
						<h3 class="h4 fw-bold mb-3">Booking Summary</h3>
						<div class="d-flex justify-content-between mb-2 text-white-50">
							<span>Date</span>
							<span><?php echo $selectedShowtime ? e(date('M d, Y', strtotime($selectedShowtime['show_date']))) : 'Select showtime'; ?></span>
						</div>
						<div class="d-flex justify-content-between mb-2 text-white-50">
							<span>Time</span>
							<span><?php echo $selectedShowtime ? e(date('g:i A', strtotime($selectedShowtime['show_time']))) : 'Select showtime'; ?></span>
						</div>
						<div class="d-flex justify-content-between mb-2 text-white-50">
							<span>Theater</span>
							<span><?php echo $selectedShowtime ? e($selectedShowtime['theater_name']) : 'Select showtime'; ?></span>
						</div>
						<div class="d-flex justify-content-between mb-2 text-white-50">
							<span>Seats</span>
							<span id="booking-summary-seats"><?php echo $totalSelectedSeats > 0 ? e(implode(', ', array_map('strval', $selectedSeatIdsFromPost))) : 'Not selected'; ?></span>
						</div>
						<hr class="border-secondary my-4">
						<div class="d-flex justify-content-between align-items-center">
							<span class="fw-semibold">Total</span>
							<span class="fs-4 fw-bold text-danger" id="booking-summary-total">PHP <?php echo number_format($totalPrice, 2); ?></span>
						</div>
						<p class="text-white-50 small mt-3 mb-0">Flat booking total: PHP <?php echo number_format($totalPrice, 2); ?>.</p>
					</div>
				</div>
			</div>

			<div id="booking-panel" class="panel-card p-4 p-lg-5">
				<?php if ($movie['status'] !== 'released'): ?>
					<div class="text-center py-5">
						<h2 class="fw-bold mb-3">Coming Soon</h2>
						<p class="text-white-50 mb-0">This movie will be available for booking on <?php echo e(date('M d, Y', strtotime($movie['release_date']))); ?>.</p>
					</div>
				<?php elseif (empty($showtimes)): ?>
					<div class="text-center py-5">
						<h2 class="fw-bold mb-3">No Showtimes Yet</h2>
						<p class="text-white-50 mb-0">There are no showtimes configured for this movie in the database.</p>
					</div>
				<?php else: ?>
					<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
						<div>
							<h2 class="fw-bold mb-1">Book Tickets</h2>
							<p class="text-white-50 mb-0">Choose a showtime, then pick your seats.</p>
						</div>
						<div class="text-white-50 small">Available seats are refreshed from the database.</div>
					</div>

					<div class="mb-4">
						<h3 class="h5 fw-semibold mb-3">Select Showtime</h3>
						<?php foreach ($showtimesByDate as $date => $dateShowtimes): ?>
							<div class="mb-4">
								<div class="text-uppercase text-white-50 small fw-semibold mb-2"><?php echo e(date('M d, Y', strtotime($date))); ?></div>
								<div class="row g-3">
									<?php foreach ($dateShowtimes as $showtime): ?>
										<div class="col-6 col-md-4 col-xl-3">
											<form method="POST" action="movieBooking.php" class="h-100">
												<input type="hidden" name="movie_id" value="<?php echo (int) $movieId; ?>">
												<input type="hidden" name="showtime_id" value="<?php echo (int) $showtime['showtime_id']; ?>">
												<button type="submit" name="select_showtime" class="showtime-pill w-100 px-3 py-3 <?php echo ((int) $showtime['showtime_id'] === (int) $selectedShowtimeId) ? 'active' : ''; ?>">
													<span class="text-center">
														<span class="d-block fw-semibold"><?php echo e(date('g:i A', strtotime($showtime['show_time']))); ?></span>
														<small class="d-block text-white-50"><?php echo e($showtime['theater_name']); ?></small>
													</span>
												</button>
											</form>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>

					<?php if ($selectedShowtime && !empty($seatRows)): ?>
						<form method="POST" action="movieBooking.php">
							<input type="hidden" name="movie_id" value="<?php echo (int) $movieId; ?>">
							<input type="hidden" name="showtime_id" value="<?php echo (int) $selectedShowtimeId; ?>">

							<div class="mb-4">
								<h3 class="h5 fw-semibold mb-3">Select Seats</h3>
								<div class="p-4 rounded-4 bg-black bg-opacity-50 border border-secondary border-opacity-25">
									<div class="text-center mb-4">
										<div class="screen-bar mx-auto mb-2" style="max-width: 640px;"></div>
										<small class="text-white-50">Screen</small>
									</div>

									<div class="seat-grid">
										<?php foreach ($seatGroups as $rowLetter => $seatsInRow): ?>
											<div class="seat-row">
												<div class="seat-label"><?php echo e($rowLetter); ?></div>
												<?php foreach ($seatsInRow as $seat): ?>
													<?php
													$seatId = (int) $seat['seat_id'];
													$isBooked = in_array($seatId, $bookedSeatIds, true);
													$isChecked = in_array($seatId, $selectedSeatIdsFromPost, true);
													?>
													<label>
														<input class="seat-box" type="checkbox" name="seat_ids[]" value="<?php echo $seatId; ?>" <?php echo $isBooked ? 'disabled' : ''; ?> <?php echo $isChecked ? 'checked' : ''; ?>>
														<span class="seat-tile"><?php echo e(preg_replace('/^[A-Z]/', '', $seat['seat_number'])); ?></span>
													</label>
												<?php endforeach; ?>
											</div>
										<?php endforeach; ?>
									</div>

									<div class="d-flex flex-wrap justify-content-center gap-4 mt-4 small text-white-50">
										<div class="d-flex align-items-center gap-2">
											<span class="seat-tile" style="width: 1.6rem; height: 1.6rem;"></span>
											<span>Available</span>
										</div>
										<div class="d-flex align-items-center gap-2">
											<span class="seat-tile" style="width: 1.6rem; height: 1.6rem; background:#dc3545; border-color:#dc3545;"></span>
											<span>Selected</span>
										</div>
										<div class="d-flex align-items-center gap-2">
											<span class="seat-tile" style="width: 1.6rem; height: 1.6rem; background:#151515; color:rgba(255,255,255,.25); text-decoration:line-through;"></span>
											<span>Booked</span>
										</div>
									</div>
								</div>
							</div>

							<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
								<div>
									<h3 class="h5 fw-semibold mb-1">Final Check</h3>
									<p class="text-white-50 mb-0">Selected seats are saved to the database as individual bookings.</p>
								</div>
								<div class="text-end">
									<div class="text-white-50 small">Selected seats</div>
									<div class="fw-bold" id="booking-summary-count"><?php echo (int) $totalSelectedSeats; ?></div>
								</div>
							</div>

							<div class="d-grid">
								<button type="submit" name="book_tickets" class="btn btn-danger btn-lg fw-semibold py-3">Confirm Booking</button>
							</div>
						</form>
					<?php else: ?>
						<div class="text-center py-5">
							<h3 class="fw-bold mb-3">Select a showtime to continue</h3>
							<p class="text-white-50 mb-0">Once a showtime is selected, the seat map for that theater will appear here.</p>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</main>
	<?php endif; ?>

	<script src="../assets/js/bootstrap.bundle.min.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const seatCheckboxes = Array.from(document.querySelectorAll('.seat-box[name="seat_ids[]"]'));
			const summarySeats = document.getElementById('booking-summary-seats');
			const summaryCount = document.getElementById('booking-summary-count');

			function refreshBookingSummary() {
				const selectedSeatLabels = seatCheckboxes
					.filter(function (checkbox) { return checkbox.checked && !checkbox.disabled; })
					.map(function (checkbox) {
						const label = checkbox.closest('label');
						const tile = label ? label.querySelector('.seat-tile') : null;
						return tile ? tile.textContent.trim() : checkbox.value;
					})
					.filter(Boolean);

				const seatText = selectedSeatLabels.length > 0 ? selectedSeatLabels.join(', ') : 'Not selected';

				if (summarySeats) {
					summarySeats.textContent = seatText;
				}

				if (summaryCount) {
					summaryCount.textContent = String(selectedSeatLabels.length);
				}
			}

			seatCheckboxes.forEach(function (checkbox) {
				checkbox.addEventListener('change', refreshBookingSummary);
			});

			refreshBookingSummary();
		});
	</script>
</body>

</html>
