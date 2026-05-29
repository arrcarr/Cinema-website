<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <title>Absolute Cinema - Home Page</title>
    <style>
        .carousel-item {
            height: 500px;
            background-size: cover;
            background-position: center;
        }
        .carousel-overlay {
            background: linear-gradient(to right, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.6) 50%, rgba(0,0,0,0) 100%);
            height: 100%;
            width: 100%;
            
        }
    </style>
</head>

<body class="bg-black text-white">
    <?php include "header.php"; ?>

    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>
        
        <div class="carousel-inner">
            <div class="carousel-item active" style="background-image: url('../assets/backgrounds/banner.jpg');">
                <div class="carousel-overlay d-flex align-items-center">
                    <div class="container px-5">
                        <div class="max-w-2xl">
                            <h1 class="display-4 fw-bold text-white mb-3">Welcome to ABSOLUTE Cinema!</h1>
                            <p class="fs-4 text-secondary mb-4">Bringing the best of Cinema to You!</p>
                            <div class="d-flex gap-3">
                                <a href="moviesPage.php" class="btn btn-danger btn-lg px-4">Browse Movies</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <section class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0">Now Showing</h2>
            <a href="moviesPage.php" class="text-danger text-decoration-none fs-5">View All →</a>
        </div>
        <div >
            <?php 
            include "../movie_card.php"; 
            ?>
        </div>
    </section>

    <section class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0">Coming Soon</h2>
            <a href="moviesPage.php" class="text-danger text-decoration-none fs-5">View All →</a>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <?php 
            include "../coming_soon.php"; 
            ?>
        </div>
    </section>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>