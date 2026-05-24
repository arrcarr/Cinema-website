<?php
// We check the session variable 'userType' (which should be set during login)
// For testing, you can force this to 'admin' or 'employee'
$userType = isset($_SESSION['userType']) ? $_SESSION['userType'] : 'employee';
?>
<style>
    .sidebar {
        width: 280px;
        height: 100vh;
        /* Lock height to the viewport */
        position: sticky;
        /* Keep it on screen when scrolling */
        top: 0;
        /* Anchor it to the top */
        overflow-y: auto;
        /* Allow internal scrolling if menu gets too long */
    }

    /* Optional: Custom scrollbar styling for the sidebar to keep it looking clean */
    .sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background-color: #495057;
        border-radius: 4px;
    }

    .sidebar .nav-link {
        transition: background-color 0.2s, color 0.2s;
    }

    .sidebar .nav-link:hover {
        background-color: #2d2d2d;
    }

    .sidebar .nav-link.active {
        background-color: #dc3545 !important;
        /* Bootstrap danger red */
        color: white !important;
    }
</style>

<div class="d-flex flex-column flex-shrink-0 p-4 text-bg-dark border-end border-secondary sidebar bg-black">
    <a href="#" class="d-flex align-items-center mb-4 text-white text-decoration-none">
        <i class="bi bi-film text-danger fs-2 me-2"></i>
        <span class="fs-4 fw-bold">ABSOLUTE <span class="text-danger">CINEMA</span></span>
    </a>

    <div class="text-secondary small fw-bold text-uppercase mb-3">Menu</div>

    <ul class="nav nav-pills flex-column mb-auto gap-2">
        <li class="nav-item">
            <a href="../employee/employeeIndex.php" class="nav-link text-white active">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="../employee/employeeMovies.php" class="nav-link text-white">
                <i class="bi bi-camera-reels me-2"></i> Movies
            </a>
        </li>
        <li>
            <a href="../employee/employeeTheaters.php" class="nav-link text-white">
                <i class="bi bi-building me-2"></i> Theaters
            </a>
        </li>
        <li>
            <a href="../employee/employeeShowtime.php" class="nav-link text-white">
                <i class="bi bi-clock-history me-2"></i> Showtimes
            </a>
        </li>
        <li>
            <a href="../employee/employeeReservations.php" class="nav-link text-white">
                <i class="bi bi-ticket-detailed me-2"></i> Reservations
            </a>
        </li>

        <?php if ($userType === 'admin'): ?>
            <li class="mt-3">
                <div class="text-secondary small fw-bold text-uppercase mb-2 px-3">System Access</div>
            </li>
            <li>
                <a href="../admin/adminUser.php" class="nav-link text-white">
                    <i class="bi bi-people me-2"></i> Users
                </a>
            </li>
            <li>
                <a href="../admin/adminLogs.php" class="nav-link text-white">
                    <i class="bi bi-journal-text me-2"></i> Logs
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <hr class="border-secondary">
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
            data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://ui-avatars.com/api/?name=<?php echo $userType; ?>&background=random" alt="" width="32"
                height="32" class="rounded-circle me-2">
            <strong class="text-capitalize">
                <?php echo $userType; ?>
            </strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
            <li><a class="dropdown-item" href="#">Profile</a></li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="../logout.php">Sign out</a></li>
        </ul>
    </div>
</div>