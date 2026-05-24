<?php
session_start();
// Temporary override for testing. 
$_SESSION['userType'] = 'employee';
include "../database/conn.php";
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-dark text-white min-vh-100 d-flex">

    <?php include '../admin/sidebar_header.php'; ?>

    <div class="flex-grow-1 p-5 overflow-auto">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3">
            <h1 class="fw-bold">Employee Dashboard</h1>
            <span class="badge bg-secondary fs-6 px-3 py-2">Staff Member</span>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card bg-black border-secondary p-4 h-100 shadow">
                    <h5 class="text-secondary fw-bold text-uppercase mb-3">Active Movies</h5>
                    <h2 class="display-5 fw-bold text-white mb-0">8</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-black border-secondary p-4 h-100 shadow border-warning">
                    <h5 class="text-warning fw-bold text-uppercase mb-3">Pending Reservations</h5>
                    <h2 class="display-5 fw-bold text-white mb-0">14</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-black border-secondary p-4 h-100 shadow">
                    <h5 class="text-secondary fw-bold text-uppercase mb-3">Today's Showtimes</h5>
                    <h2 class="display-5 fw-bold text-white mb-0">12</h2>
                </div>
            </div>
        </div>

        <h3 class="fw-bold mb-3">Reservations Awaiting Approval</h3>
        <div class="card bg-black border-secondary overflow-hidden shadow">
            <table class="table table-dark table-hover mb-0">
                <thead class="table-active">
                    <tr>
                        <th>Booking ID</th>
                        <th>User</th>
                        <th>Movie</th>
                        <th>Date & Time</th>
                        <th>Seats</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="align-middle">#1024</td>
                        <td class="align-middle">johndoe@email.com</td>
                        <td class="align-middle">Taxi Driver</td>
                        <td class="align-middle">Apr 15, 2026 - 8:00 PM</td>
                        <td class="align-middle">D4, D5</td>
                        <td class="align-middle">
                            <button class="btn btn-sm btn-success fw-bold me-1"><i class="bi bi-check-lg"></i>
                                Approve</button>
                            <button class="btn btn-sm btn-danger fw-bold"><i class="bi bi-x-lg"></i> Reject</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="align-middle">#1025</td>
                        <td class="align-middle">janedoe@email.com</td>
                        <td class="align-middle">The Mummy</td>
                        <td class="align-middle">Apr 15, 2026 - 9:30 PM</td>
                        <td class="align-middle">F10</td>
                        <td class="align-middle">
                            <button class="btn btn-sm btn-success fw-bold me-1"><i class="bi bi-check-lg"></i>
                                Approve</button>
                            <button class="btn btn-sm btn-danger fw-bold"><i class="bi bi-x-lg"></i> Reject</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>