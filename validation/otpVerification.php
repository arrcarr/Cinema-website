<?php
session_start();
require_once "../database/conn.php"; // Adjust path if needed

if(isset($_POST['ver'])){
    // User input (raw OTP from the form)
    $otp_input = trim($_POST['otp']);

    // Check against the 'user' table
    $otpsql = "SELECT * FROM `user` WHERE `otp` = '".$otp_input."'";
    $result = $conn->query($otpsql);

    if ($result->num_rows == 1) {
        // Change otp field to null and status to active
        $updatesql= "UPDATE `user` SET `otp` = NULL , `status` = 'Active' WHERE `otp` = '".$otp_input."'";
        $conn->query($updatesql);

        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Account Activated",
                        text: "You can now log in.",
                        background: "#0f0f0f",
                        color: "#ffffff",
                        iconColor: "#dc3545",
                        confirmButtonColor: "#dc3545",
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = "../pages/login.php"; // Redirect to login
                    });
                });
              </script>';
    } else {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        position: "center",
                        icon: "error",
                        title: "Invalid OTP",
                        text: "The code you entered is incorrect.",
                        background: "#0f0f0f",
                        color: "#ffffff",
                        iconColor: "#dc3545",
                        confirmButtonColor: "#dc3545",
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                });
              </script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Absolute Cinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #050505; color-scheme: dark; }
        .login-card { background-color: #121212; border: 1px solid rgba(255, 255, 255, 0.05); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5); }
        .otp-input { 
            background-color: #0a0a0a !important; border: 1px solid transparent; 
            text-align: center; letter-spacing: 0.5rem; font-size: 1.5rem; transition: all 0.3s ease; 
        }
        .otp-input:focus { background-color: #0f0f0f !important; border-color: #dc3545; box-shadow: none; }
        .btn-custom:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3); }
    </style>
</head>
<body class="text-white d-flex align-items-center justify-content-center min-vh-100 px-3 py-5">

    <div class="w-100" style="max-width: 28rem;">
        
        <div class="d-flex align-items-center justify-content-center gap-2 mb-5">
            <i class="bi bi-film text-danger fs-1"></i>
            <span class="fs-2 fw-bold text-white tracking-wide" style="letter-spacing: 1px;">
                ABSOLUTE <span class="text-danger">CINEMA</span>
            </span>
        </div>

        <div class="login-card p-4 p-md-5 rounded-4 text-center">
            
            <i class="bi bi-envelope-check text-danger mb-3 d-block" style="font-size: 3rem;"></i>
            <h2 class="fs-4 fw-bold text-white mb-2">OTP Verification</h2>
            <p class="text-secondary small mb-4">A One-Time Password was sent to your email.</p>

            <form action="" method="post">
                <div class="mb-4">
                    <label class="form-label text-secondary small fw-medium mb-2">Enter the OTP Number</label>
                    <input type="text" name="otp" class="form-control py-3 rounded-3 text-white otp-input fw-bold" 
                           placeholder="------" maxlength="6" required autocomplete="off" />
                </div>
                
                <input type="submit" name="ver" value="Verify Account" class="btn btn-danger w-100 py-3 fw-bold rounded-3 btn-custom mb-3">
            </form>

            <div class="mt-2">
                <a href="login.php" class="text-secondary text-decoration-none transition small hover-opacity-75">
                    <i class="bi bi-arrow-left me-1"></i> Back to Login
                </a>
            </div>
        </div>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>