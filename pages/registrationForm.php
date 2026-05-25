<?php
session_start();
include "../database/conn.php"; 
require_once "../validation/verifyOtpEmail.php"; 

if (isset($_POST['register'])) {
    
    )
    $fname    = $_POST['fname'];
    $lname    = $_POST['lname'];
    $birthday = $_POST['birthday'];
    $email    = $_POST['email'];
    $username = $_POST['username'];

    
    $password = md5($_POST['password']);
    $confirmPassword = md5($_POST['confirmPassword']);
    
    $fullname = $fname . " " . $lname;
    $error_message = "";

    if ($password !== $confirmPassword) {
        $error_message = "Passwords do not match.";
    } else {
       
        $check_email = $conn->query("SELECT * FROM user WHERE email = '$email'");
        if ($check_email->num_rows > 0) {
            $error_message = "Email is already registered.";
        } else {
           
            $pfp_imgPath = "";
            if (isset($_FILES['upload_img']) && $_FILES['upload_img']['error'] == 0) {
                
                
                $pfp_imgPath = basename($_FILES["upload_img"]["name"]);
            }

            
            if (empty($pfp_imgPath)) {
                $pfp_imgPath = 'assets/icons/user.png';
            }

            
            $otp = rand(100000, 999999);

            
            $insert_sql = "INSERT INTO user 
                (`username`, `email`, `userType`, `password`, `fname`, `lname`, `birthday`, `pfp_imgPath`, `otp`, `status`) 
                VALUES 
                ('$username', '$email', 'User', '$password', '$fname', '$lname', '$birthday', '$pfp_imgPath', '$otp', 'pending')";

            if ($conn->query($insert_sql) === TRUE) {
                
                
                send_verification($fullname, $email, $otp);
                
                echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
                echo '<script>
                        document.addEventListener("DOMContentLoaded", function() {
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Registered Successfully",
                                text: "Please check your email for the OTP.",
                                background: "#0f0f0f",
                                color: "#ffffff",
                                iconColor: "#dc3545",
                                confirmButtonColor: "#dc3545",
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            }).then(() => {
                                window.location.href = "../validation/otpVerification.php";
                            });
                        });
                      </script>';
            } else {
                $error_message = "Database Error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Absolute Cinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #050505; color-scheme: dark; }
        .login-card { background-color: #121212; border: 1px solid rgba(255, 255, 255, 0.05); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5); }
        .standard-input { background-color: #0a0a0a !important; border: 1px solid transparent; transition: all 0.3s ease; padding-left: 1rem; }
        .form-control:focus { background-color: #0f0f0f !important; border-color: #dc3545; box-shadow: none; }
        .btn-custom:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3); }
    </style>
</head>
<body class="text-white d-flex align-items-center justify-content-center min-vh-100 px-3 py-5">
    <div class="w-100" style="max-width: 38rem;"> 
        
        <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
            <i class="bi bi-film text-danger fs-1"></i>
            <span class="fs-2 fw-bold text-white tracking-wide" style="letter-spacing: 1px;">
                ABSOLUTE <span class="text-danger">CINEMA</span>
            </span>
        </div>

        <div class="login-card p-4 p-md-5 rounded-4">
            <h2 class="fs-4 fw-bold text-white mb-4 text-center border-bottom border-secondary pb-3">Create Account</h2>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger bg-danger bg-opacity-10 border-danger border-opacity-25 text-danger py-2 px-3 rounded-3 mb-4 text-center text-sm">
                    <i class="bi bi-exclamation-circle me-2"></i><?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-secondary mb-2 small fw-medium">First Name</label>
                        <input type="text" name="fname" class="form-control py-3 rounded-3 text-white standard-input" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-secondary mb-2 small fw-medium">Last Name</label>
                        <input type="text" name="lname" class="form-control py-3 rounded-3 text-white standard-input" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary mb-2 small fw-medium">Birthday</label>
                    <input type="date" name="birthday" class="form-control py-3 rounded-3 text-white standard-input" required>
                </div>

                <div class="mb-4">
                    <label class="form-label text-secondary mb-2 small fw-medium">Profile Picture</label>
                    <input type="file" name="upload_img" class="form-control py-2 rounded-3 text-white standard-input" accept="image/*">
                </div>

                <hr class="border-secondary mb-4">

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-secondary mb-2 small fw-medium">Username</label>
                        <input type="text" name="username" class="form-control py-3 rounded-3 text-white standard-input" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-secondary mb-2 small fw-medium">Email Address</label>
                        <input type="email" name="email" class="form-control py-3 rounded-3 text-white standard-input" required>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-secondary mb-2 small fw-medium">Password</label>
                        <input type="password" name="password" class="form-control py-3 rounded-3 text-white standard-input" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-secondary mb-2 small fw-medium">Confirm Password</label>
                        <input type="password" name="confirmPassword" class="form-control py-3 rounded-3 text-white standard-input" required>
                    </div>
                </div>

                <button type="submit" name="register" class="btn btn-danger w-100 py-3 fw-bold rounded-3 btn-custom mt-2">Create Account</button>
            </form>

            <div class="mt-4 text-center">
                <a href="login.php" class="text-secondary text-decoration-none transition">
                    Already have an account? <span class="text-danger">Login</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>