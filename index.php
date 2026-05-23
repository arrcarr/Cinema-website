

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <title>Absolute Cinema - Home Page</title>
</head>
<body class="bg-black">
    <?php
        include "header.php"
    ?>

    <div class="banner text-white p-5" style="background-image: url(assets/backgrounds/banner.jpg)">
        <div class="p-5">
            <h1 class="fw-bold">Welcome to ABSOLUTE Cinema!</h1>
            <p class="fs-3">Bringing the best of Cinema to You!</p>

            <a href="" class="btn btn-danger">Browse Movies</a>
        </div>
    </div>

    <div class="text-white p-4">
        <span class="d-flex flex-row justify-content-between">
            <h2 class="fw-bold">Now Showing</h2>
            <a href="movies.php" class="text-danger fs-5">View All →</a>
        </span>
    </div>

    <?php
        include "movie_card.php"
    ?>

    <div class="text-white p-4">
        <span class="d-flex flex-row justify-content-between">
            <h2 class="fw-bold">Coming Soon</h2>
            <a href="movies.php" class="text-danger fs-5">View All →</a>
        </span>
    </div>
    
    <?php
        include "coming_soon.php"
    ?>


    <script src="assets/js/bootstrap.min.js"></script>
</body>
</html>