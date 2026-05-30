<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<style>
.carousel.carousel-fade .carousel-item{transition:opacity .8s ease-in-out}
</style>

<nav class="navbar navbar-expand-lg navbar-dark bg-black border-bottom border-secondary border-opacity-25">
    <div class="container py-2">
        <a class="navbar-brand text-white fs-4 fw-bold d-flex align-items-center gap-2" href="index.php">
            <img src="../assets/icons/logo.webp" alt="Absolute Cinema" width="36" height="36">
            <span>ABSOLUTE <span class="text-danger">CINEMA</span></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#siteNav" aria-controls="siteNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="siteNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-lg-2">
                <li class="nav-item"><a href="index.php" class="nav-link text-white">Home</a></li>
                <li class="nav-item"><a href="moviesPage.php" class="nav-link text-white">Movies</a></li>
                <li class="nav-item"><a href="myBookingsPage.php" class="nav-link text-white">My Bookings</a></li>
                <li class="nav-item"><a href="myAccount.php" class="nav-link text-white">My Account</a></li>
            </ul>

            <div class="d-flex align-items-center gap-3 ms-lg-3 mt-3 mt-lg-0">
                <?php if (isset($_SESSION['userType']) && $_SESSION['userType'] == 'User') : ?>
                    <span class="text-white fw-semibold small d-none d-lg-inline">Welcome, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') : 'User'; ?>!</span>
                    <a href="logout.php" class="btn btn-danger btn-sm fw-semibold">Logout</a>
                <?php else : ?>
                    <a href="login.php" class="btn btn-danger btn-sm fw-semibold">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>