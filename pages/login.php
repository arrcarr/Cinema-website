<?php
session_start();


include "../database/conn.php";

$error_message = "";


if (isset($_POST['username']) && isset($_POST['password'])) {
    
    
    $username = $_POST['username'];
    
    $password = md5($_POST['password']);

   
    $sql = "SELECT * FROM user WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
     
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['userType'] = $user['userType'];
        $_SESSION['username'] = $user['username'];

       
        $user_id = $user['user_id'];
        $conn->query("INSERT INTO system_logs (user_id, action, description) VALUES ('$user_id', 'Login', 'User logged in successfully')");

        
        if ($user['userType'] === 'Administrator') {
            header("Location: ../admin/adminIndex.php");
        } elseif ($user['userType'] === 'Employee') {
            header("Location: ../employee/employeeIndex.php");
        } else {
            header("Location: index.php"); 
        }
        exit();
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Absolute Cinema</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #050505;
        }
        
       
        .login-card {
            background-color: #121212;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

       
        .input-icon-wrapper {
            position: relative;
        }

        .input-icon-wrapper .bi {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .input-icon-wrapper .form-control {
            padding-left: 3rem;
            background-color: #0a0a0a !important; 
            border: 1px solid transparent;
            transition: all 0.3s ease;
        }

        .input-icon-wrapper .form-control:focus {
            background-color: #0f0f0f !important;
            border-color: #dc3545;
            box-shadow: none; 
        }

        .btn-custom {
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
        }
        
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }
    </style>
</head>

<body class="text-white d-flex align-items-center justify-content-center min-vh-100 px-3">

    <div class="w-100" style="max-width: 26rem;">

        <div class="d-flex align-items-center justify-content-center gap-2 mb-5">
            <i class="bi bi-film text-danger fs-1"></i>
            <span class="fs-2 fw-bold text-white tracking-wide" style="letter-spacing: 1px;">
                ABSOLUTE <span class="text-danger">CINEMA</span>
            </span>
        </div>

        <div class="login-card p-4 p-md-5 rounded-4">
            <h2 class="fs-4 fw-bold text-white mb-4 text-center">
                Welcome Back
            </h2>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger bg-danger bg-opacity-10 border-danger border-opacity-25 text-danger py-2 px-3 rounded-3 mb-4 text-center text-sm" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i><?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">

                <div class="mb-4">
                    <div class="input-icon-wrapper">
                        <i class="bi bi-person"></i>
                        <input type="text" id="username" name="username"
                            class="form-control py-3 rounded-3 text-white" placeholder="Username"
                            required>
                    </div>
                </div>

                <div class="mb-5">
                    <div class="input-icon-wrapper">
                        <i class="bi bi-lock"></i>
                        <input type="password" id="password" name="password"
                            class="form-control py-3 rounded-3 text-white" placeholder="Password"
                            required>
                    </div>
                </div>

                <button type="submit" class="btn btn-danger w-100 py-3 fw-bold rounded-3 btn-custom">
                    Login
                </button>
            </form>

            <div class="mt-4 text-center">
                <a href="../pages/registrationForm.php" class="text-secondary text-decoration-none transition">
                    Don't have an account? <span class="text-danger">Register</span>
                </a>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>