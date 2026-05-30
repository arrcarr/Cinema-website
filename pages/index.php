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
            position: relative;
        }
        .carousel-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.6) 50%, rgba(0,0,0,0) 100%);
            display: flex;
            align-items: center;
            padding-left: 2rem;
        }
    </style>
</head>

<body class="bg-black text-white">
    <?php include "header.php"; ?>

    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <?php
            $bgDir = realpath(__DIR__ . '/../assets/backgrounds');
            $images = [];
            if ($bgDir && is_dir($bgDir)) {
                $files = glob($bgDir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
                if ($files !== false) {
                    sort($files);
                    foreach ($files as $f) {
                        $images[] = '../assets/backgrounds/' . basename($f);
                    }
                }
            }
            if (empty($images)) {
                $images[] = '../assets/backgrounds/banner.jpg';
            }
            if (count($images) > 1) {
                $second = $images[1];
                array_splice($images, 1, 1);
                array_unshift($images, $second);
            }
            $static_title = 'Welcome to ABSOLUTE Cinema!';
            $static_subtitle = 'Bringing the best of Cinema to You!';
        ?>

        <div class="carousel-indicators">
            <?php foreach ($images as $i => $img): ?>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?php echo $i; ?>" class="<?php echo $i === 0 ? 'active' : ''; ?>" <?php echo $i === 0 ? 'aria-current="true"' : ''; ?> aria-label="Slide <?php echo $i+1; ?>"></button>
            <?php endforeach; ?>
        </div>

        <div class="carousel-inner">
            <?php foreach ($images as $i => $img): ?>
                <div class="carousel-item <?php echo $i === 0 ? 'active' : ''; ?>" style="background-image: url('<?php echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>');">
                    <div class="carousel-overlay">
                        <div class="container px-5">
                            <div>
                                <h1 class="display-4 fw-bold text-white mb-3"><?php echo htmlspecialchars($static_title, ENT_QUOTES, 'UTF-8'); ?></h1>
                                <p class="fs-4 text-secondary mb-4"><?php echo htmlspecialchars($static_subtitle, ENT_QUOTES, 'UTF-8'); ?></p>
                                <div class="d-flex gap-3">
                                    <a href="moviesPage.php" class="btn btn-danger btn-lg px-4">Browse Movies</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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