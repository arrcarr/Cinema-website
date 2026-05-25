<?php
session_start();
require_once '../database/conn.php';

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$filter = isset($_POST['filter']) ? $_POST['filter'] : 'all';

if ($filter === 'now-showing') {
    $sql = "SELECT movie_id, title, genre, duration, release_date, poster, status FROM tb_movie_table WHERE status='released' ORDER BY release_date DESC, title ASC";
} elseif ($filter === 'coming-soon') {
    $sql = "SELECT movie_id, title, genre, duration, release_date, poster, status FROM tb_movie_table WHERE status='unreleased' ORDER BY release_date ASC, title ASC";
} else {
    $sql = "SELECT movie_id, title, genre, duration, release_date, poster, status FROM tb_movie_table ORDER BY release_date DESC, title ASC";
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movies - Absolute Cinema</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        body {
            background: radial-gradient(circle at top, rgba(220, 53, 69, 0.12), transparent 30%), #050505;
            color: #fff;
        }

        .page-shell {
            min-height: calc(100vh - 80px);
        }

        .page-card {
            background: linear-gradient(180deg, rgba(18, 18, 18, 0.98), rgba(10, 10, 10, 0.98));
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.4);
            border-radius: 1.25rem;
        }

        .hover-zoom {
            transition: transform 0.2s ease-in-out, border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .hover-zoom:hover {
            transform: translateY(-3px);
            border-color: rgba(220, 53, 69, 0.65) !important;
            box-shadow: 0 18px 30px rgba(0, 0, 0, 0.35);
        }

        .movie-poster {
            height: 350px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container py-5 page-shell" style="max-width: 1200px;">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div>
                <h1 class="fw-bold mb-1">Movies</h1>
                <p class="text-white-50 mb-0">Browse the catalog and open a movie to start booking.</p>
            </div>
        </div>

        <div class="page-card p-3 p-md-4 mb-4">
            <form method="POST" action="" class="d-flex flex-wrap gap-2">
                <button type="submit" name="filter" value="all" class="btn rounded-pill <?php echo ($filter === 'all') ? 'btn-danger text-white' : 'btn-outline-secondary'; ?>">All Movies</button>
                <button type="submit" name="filter" value="now-showing" class="btn rounded-pill <?php echo ($filter === 'now-showing') ? 'btn-danger text-white' : 'btn-outline-secondary'; ?>">Now Showing</button>
                <button type="submit" name="filter" value="coming-soon" class="btn rounded-pill <?php echo ($filter === 'coming-soon') ? 'btn-danger text-white' : 'btn-outline-secondary'; ?>">Coming Soon</button>
            </form>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col">
                        <form method="POST" action="movieBooking.php" class="h-100">
                            <input type="hidden" name="movie_id" value="<?php echo (int) $row['movie_id']; ?>">
                            <button type="submit" class="btn p-0 w-100 h-100 text-start border-0 bg-transparent">
                                <div class="card h-100 bg-dark border-secondary overflow-hidden shadow hover-zoom">
                                    <img src="<?php echo e($row['poster']); ?>" class="card-img-top movie-poster" alt="<?php echo e($row['title']); ?>">
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                            <h5 class="card-title text-truncate fw-bold text-white mb-0"><?php echo e($row['title']); ?></h5>
                                            <span class="badge rounded-pill text-bg-<?php echo ($row['status'] === 'released') ? 'danger' : 'secondary'; ?> text-uppercase"><?php echo e($row['status']); ?></span>
                                        </div>
                                        <p class="card-text text-secondary mb-3 text-capitalize small"><?php echo e($row['genre']); ?></p>
                                        <div class="mt-auto d-flex justify-content-between align-items-center text-secondary small">
                                            <span><?php echo e($row['duration']); ?></span>
                                            <span><?php echo e(date('M d, Y', strtotime($row['release_date']))); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="page-card p-5">
                        <p class="text-secondary fs-5 mb-0">No movies found</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>