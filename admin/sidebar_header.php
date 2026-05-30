<?php
$rawUserType = strtolower((string) ($_SESSION['userType'] ?? 'employee'));
if ($rawUserType === 'administrator') {
    $rawUserType = 'admin';
}

$userType = in_array($rawUserType, ['admin', 'employee'], true) ? $rawUserType : 'employee';
$currentPage = basename($_SERVER['PHP_SELF']);

function isSidebarActive($currentPage, $targets)
{
    return in_array($currentPage, (array) $targets, true) ? 'active' : '';
}
?>
<style>
    .sidebar {
        width: 280px;
        height: 100vh;
        position: sticky;
        top: 0;
        display: flex;
        flex-direction: column;
    }

    .sidebar-menu {
        flex: 1 1 auto;
        overflow-y: auto;
    }

    .sidebar-menu::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-menu::-webkit-scrollbar-thumb {
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
        color: white !important;
    }
</style>

<div class="d-flex flex-column flex-shrink-0 p-4 text-bg-dark border-end border-secondary sidebar bg-black">
    <a href="#" class="d-flex align-items-center mb-4 text-white text-decoration-none">
        <i class="bi bi-film text-danger fs-2 me-2"></i>
        <span class="fs-4 fw-bold">ABSOLUTE <span class="text-danger">CINEMA</span></span>
    </a>

    <div class="sidebar-menu pe-1">
        <div class="text-secondary small fw-bold text-uppercase mb-3">Menu</div>

        <ul class="nav nav-pills flex-column mb-auto gap-2">
        <?php if ($userType === 'employee'): ?>
        <li class="nav-item">
            <a href="../employee/employeeIndex.php" class="nav-link text-white <?php echo isSidebarActive($currentPage, 'employeeIndex.php'); ?>">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="../employee/employeeMovies.php" class="nav-link text-white <?php echo isSidebarActive($currentPage, 'employeeMovies.php'); ?>">
                <i class="bi bi-camera-reels me-2"></i> Movies
            </a>
        </li>
        <li>
            <a href="../employee/employeeTheaters.php" class="nav-link text-white <?php echo isSidebarActive($currentPage, 'employeeTheaters.php'); ?>">
                <i class="bi bi-building me-2"></i> Theaters
            </a>
        </li>
        <li>
            <a href="../employee/employeeShowtime.php" class="nav-link text-white <?php echo isSidebarActive($currentPage, 'employeeShowtime.php'); ?>">
                <i class="bi bi-clock-history me-2"></i> Showtimes
            </a>
        </li>
        <li>
            <a href="../employee/employeeReservations.php" class="nav-link text-white <?php echo isSidebarActive($currentPage, 'employeeReservations.php'); ?>">
                <i class="bi bi-ticket-detailed me-2"></i> Reservations
            </a>
        </li>

        <?php elseif ($userType === 'admin'): ?>
        <li class="nav-item">
            <a href="../admin/adminIndex.php" class="nav-link text-white <?php echo isSidebarActive($currentPage, 'adminIndex.php'); ?>">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="../employee/employeeMovies.php" class="nav-link text-white <?php echo isSidebarActive($currentPage, 'employeeMovies.php'); ?>">
                <i class="bi bi-camera-reels me-2"></i> Movies
            </a>
        </li>
        <li>
            <a href="../employee/employeeTheaters.php" class="nav-link text-white <?php echo isSidebarActive($currentPage, 'employeeTheaters.php'); ?>">
                <i class="bi bi-building me-2"></i> Theaters
            </a>
        </li>
        <li>
            <a href="../employee/employeeShowtime.php" class="nav-link text-white <?php echo isSidebarActive($currentPage, 'employeeShowtime.php'); ?>">
                <i class="bi bi-clock-history me-2"></i> Showtimes
            </a>
        </li>
        <li>
            <a href="../employee/employeeReservations.php" class="nav-link text-white <?php echo isSidebarActive($currentPage, 'employeeReservations.php'); ?>">
                <i class="bi bi-ticket-detailed me-2"></i> Reservations
            </a>
        </li>
            <li class="mt-3">
                <div class="text-secondary small fw-bold text-uppercase mb-2 px-3">System Access</div>
            </li>
            <li>
                <a href="../admin/adminUser.php" class="nav-link text-white <?php echo isSidebarActive($currentPage, 'adminUser.php'); ?>">
                    <i class="bi bi-people me-2"></i> Users
                </a>
            </li>
            <li>
                <a href="../admin/adminLogs.php" class="nav-link text-white <?php echo isSidebarActive($currentPage, 'adminLogs.php'); ?>">
                    <i class="bi bi-journal-text me-2"></i> Logs
                </a>
            </li>
        <?php endif; ?>
            </ul>
        </div>

        <div class="mt-auto pt-3 border-top border-secondary">
            <div class="d-flex align-items-center text-white mb-3">
                <img src="../assets/icons/user.png" alt="" width="32"
                    height="32" class="rounded-circle me-2">
                <strong class="text-capitalize"><?php echo $userType; ?></strong>
            </div>
            <form action="../pages/logout.php" method="POST" class="m-0">
                <button type="submit" class="btn btn-danger w-100 fw-semibold">
                    <i class="bi bi-box-arrow-right me-2"></i> Sign out
                </button>
            </form>
        </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>