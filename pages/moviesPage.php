<?php
include "../database/conn.php";

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

if ($filter == 'now-showing') {
    $sql = "SELECT movie_id, title, genre, duration, release_date, description, poster, status FROM tb_movie_table WHERE status='released'";
} elseif ($filter == 'coming-soon') {
    $sql = "SELECT movie_id, title, genre, duration, release_date, description, poster, status FROM tb_movie_table WHERE status='unreleased'";
} else {
    $sql = "SELECT movie_id, title, genre, duration, release_date, description, poster, status FROM tb_movie_table";
}

$result = $conn->query($sql);
?>

<?php
include 'header.php';
?>

<style>
    .hover-zoom {
        transition: transform 0.2s ease-in-out, border-color 0.2s ease-in-out;
    }

    .hover-zoom:hover {
        transform: scale(1.03);
        border-color: var(--bs-danger) !important;
    }
</style>

<div class="bg-black min-vh-100 py-5">
    <div class="container" style="max-width: 1200px;">
        <h1 class="fw-bold mb-4 text-white">All Movies</h1>

        <ul class="nav nav-underline border-bottom border-secondary mb-4 pb-1 gap-3">
            <li class="nav-item">
                <a class="nav-link fw-medium <?php echo ($filter == 'all') ? 'active text-danger border-danger' : 'text-secondary link-light'; ?>"
                    href="?filter=all">
                    All Movies
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-medium <?php echo ($filter == 'now-showing') ? 'active text-danger border-danger' : 'text-secondary link-light'; ?>"
                    href="?filter=now-showing">
                    Now Showing
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-medium <?php echo ($filter == 'coming-soon') ? 'active text-danger border-danger' : 'text-secondary link-light'; ?>"
                    href="?filter=coming-soon">
                    Coming Soon
                </a>
            </li>
        </ul>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '
                        <div class="col">
                            <a href="movieBooking.php?id=' . $row["movie_id"] . '" class="text-decoration-none">
                                <div class="card h-100 bg-dark hover-zoom border-secondary overflow-hidden shadow">
                                    
                                    <img src="' . $row["poster"] . '" class="card-img-top object-fit-cover" alt="' . $row["title"] . '" style="height: 350px;">
                                    
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title text-truncate fw-bold text-white mb-1">' . $row["title"] . '</h5>
                                        <p class="card-text text-secondary mb-3 text-capitalize small">' . $row["genre"] . '</p>
                                        
                                        <div class="mt-auto d-flex justify-content-between align-items-center text-secondary small">
                                            <span>' . $row["duration"] . '</span>
                                            <span>' . $row["release_date"] . '</span>
                                        </div>
                                    </div>
                                    
                                </div>
                            </a>
                        </div>
                    ';
                }
            } else {
                echo '
                    <div class="col-12 text-center py-5">
                        <p class="text-secondary fs-5">No movies found</p>
                    </div>
                ';
            }
            ?>
        </div>
    </div>
</div>