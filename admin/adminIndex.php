<?php
session_start();
// Temporary override for testing. Ensure your login script sets this!
$_SESSION['userType'] = 'admin';
include "../database/conn.php";
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-dark text-white min-vh-100 d-flex">

    <?php include 'sidebar_header.php'; ?>

    <div class="flex-grow-1 p-5 overflow-auto">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3">
            <h1 class="fw-bold">Admin Overview</h1>
            <span class="badge bg-danger fs-6 px-3 py-2">System Administrator</span>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card bg-black border-secondary p-4 h-100 shadow">
                    <h5 class="text-secondary fw-bold text-uppercase mb-3">Total Movies</h5>
                    <h2 class="display-5 fw-bold text-white mb-0">12</h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-black border-secondary p-4 h-100 shadow">
                    <h5 class="text-secondary fw-bold text-uppercase mb-3">Total Bookings</h5>
                    <h2 class="display-5 fw-bold text-white mb-0">145</h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-black border-secondary p-4 h-100 shadow border-danger">
                    <h5 class="text-danger fw-bold text-uppercase mb-3">Registered Users</h5>
                    <h2 class="display-5 fw-bold text-white mb-0">89</h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-black border-secondary p-4 h-100 shadow border-danger">
                    <h5 class="text-danger fw-bold text-uppercase mb-3">System Alerts</h5>
                    <h2 class="display-5 fw-bold text-white mb-0">3</h2>
                </div>
            </div>
        </div>

        <h3 class="fw-bold mb-3">Recent System Activity (Logs)</h3>
        <div class="card bg-black border-secondary p-4">
            <p class="text-secondary">System log data would populate here...</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>