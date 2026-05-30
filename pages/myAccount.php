<?php
session_start();
require_once "../database/conn.php"; 


if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php"); 
    exit();
}

$user_id = $_SESSION['user_id'];
$alert_type = "";
$alert_message = "";




if (isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $current_password_hash = md5($current_password);
    $new_password_hash = md5($new_password);

    
    $stmt = $conn->prepare("SELECT password FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    $stmt->close();

    if (!$user_data || $user_data['password'] !== $current_password_hash) {
        $alert_type = "danger";
        $alert_message = "Security Error: The current password you entered is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $alert_type = "danger";
        $alert_message = "Security Error: The new passwords do not match.";
    } else {
        $update_stmt = $conn->prepare("UPDATE user SET password = ? WHERE user_id = ?");
        $update_stmt->bind_param("si", $new_password_hash, $user_id);
        if ($update_stmt->execute()) {
            $alert_type = "success";
            $alert_message = "Your password has been changed successfully.";
        } else {
            $alert_type = "danger";
            $alert_message = "Database error while updating password.";
        }
        $update_stmt->close();
    }
}


if (isset($_POST['update_pfp']) && isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] == 0) {
    $target_dir = __DIR__ . "/uploads/profile_pics/";
    $public_dir = "uploads/profile_pics/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    
    $file_name = rand(1000,9999) . '_' . basename($_FILES["profilePic"]["name"]);
    $pfp_fs_path = $target_dir . $file_name;
    $pfp_imgPath = $public_dir . $file_name;
    
    if (move_uploaded_file($_FILES["profilePic"]["tmp_name"], $pfp_fs_path)) {
        $update_stmt = $conn->prepare("UPDATE user SET pfp_imgPath = ? WHERE user_id = ?");
        $update_stmt->bind_param("si", $pfp_imgPath, $user_id);
        if ($update_stmt->execute()) {
            $alert_type = "success";
            $alert_message = "Your profile picture has been updated.";
        } else {
            $alert_type = "danger";
            $alert_message = "Database error while updating profile picture.";
        }
        $update_stmt->close();
    } else {
        $alert_type = "danger";
        $alert_message = "Failed to upload image. Check folder permissions.";
    }
}


$stmt = $conn->prepare("SELECT username, email, fname, lname, userType, pfp_imgPath FROM user WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

function resolve_profile_picture_src($pfpPath, $username)
{
    $pfpPath = trim((string) $pfpPath);

    if ($pfpPath === '') {
        return '../assets/icons/user.png';
    }

    if (preg_match('#^https?://#i', $pfpPath)) {
        return '../assets/icons/user.png';
    }

    $normalizedPath = ltrim($pfpPath, '/');

    if (strpos($normalizedPath, 'pages/uploads/profile_pics/') === 0) {
        $normalizedPath = substr($normalizedPath, strlen('pages/'));
    } elseif (strpos($normalizedPath, 'assets/') === 0) {
        $normalizedPath = '../' . $normalizedPath;
    } elseif (strpos($normalizedPath, 'uploads/profile_pics/') !== 0) {
        $normalizedPath = 'uploads/profile_pics/' . basename($normalizedPath);
    }

    return $normalizedPath;
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Absolute Cinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #050505; color: #fff; }
        .dashboard-card {
            background-color: #121212;
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        .standard-input { 
            background-color: #0a0a0a !important; 
            border: 1px solid transparent; 
            transition: all 0.3s ease; 
        }
        .form-control:focus { 
            background-color: #0f0f0f !important; 
            border-color: #dc3545; 
            box-shadow: none; 
        }
        .btn-custom:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3); 
        }
        .profile-img { 
            width: 140px; 
            height: 140px; 
            object-fit: cover; 
            border: 3px solid #dc3545; 
        }
        .profile-preview-wrap {
            position: relative;
            display: inline-block;
        }
        .profile-preview-badge {
            position: absolute;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 999px;
            padding: 0.35rem 0.6rem;
            font-size: 0.75rem;
            line-height: 1;
        }
    </style>
</head>
<body class="min-vh-100">

    <?php include '../pages/header.php'; ?>

    <div class="container py-5" style="max-width: 900px;">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold m-0 border-start border-danger border-4 ps-3">Account Settings</h1>
        </div>

        <?php if (!empty($alert_message)): ?>
            <div class="alert alert-<?php echo $alert_type; ?> alert-dismissible fade show rounded-3 mb-4 d-flex align-items-center" role="alert">
                <?php if($alert_type == 'success') echo '<i class="bi bi-check-circle-fill me-3 fs-5"></i>'; ?>
                <?php if($alert_type == 'danger') echo '<i class="bi bi-exclamation-triangle-fill me-3 fs-5"></i>'; ?>
                <div><?php echo $alert_message; ?></div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="dashboard-card p-4 text-center h-100">
                    
                    <?php 
                        $pfp = resolve_profile_picture_src($user['pfp_imgPath'] ?? '', $user['username'] ?? 'User');
                    ?>
                    <div class="profile-preview-wrap mb-3">
                        <img id="profilePreview" src="<?php echo htmlspecialchars($pfp, ENT_QUOTES, 'UTF-8'); ?>" class="rounded-circle profile-img shadow" alt="Profile Picture Preview">
                        <span class="profile-preview-badge">Preview</span>
                    </div>
                    
                    <h4 class="fw-bold mb-1">
                        <?php echo htmlspecialchars($user['fname'] . ' ' . $user['lname']); ?>
                    </h4>
                    <p class="text-secondary small mb-3">@<?php echo htmlspecialchars($user['username']); ?></p>
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill text-capitalize mb-4">
                        <?php echo htmlspecialchars($user['userType']); ?>
                    </span>

                    <hr class="border-secondary opacity-25 mb-4">

                    <form action="" method="POST" enctype="multipart/form-data" class="text-start">
                        <label class="form-label text-secondary small fw-medium mb-2"><i class="bi bi-camera me-1"></i> Update Avatar</label>
                        <input type="file" name="profilePic" id="profilePicInput" class="form-control form-control-sm mb-3 standard-input" accept="image/*" required>
                        <button type="submit" name="update_pfp" class="btn btn-outline-secondary btn-sm w-100 fw-bold rounded-3 btn-custom">Upload Photo</button>
                    </form>

                </div>
            </div>

            <div class="col-md-8">
                <div class="dashboard-card p-4 p-md-5 h-100">
                    
                    <h5 class="fw-bold border-bottom border-secondary pb-3 mb-4"><i class="bi bi-person-lines-fill me-2 text-danger"></i> Profile Information</h5>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-secondary small fw-medium">Email Address</div>
                        <div class="col-sm-8 text-light"><?php echo htmlspecialchars($user['email']); ?></div>
                    </div>
                    <div class="row mb-5">
                        <div class="col-sm-4 text-secondary small fw-medium">Username</div>
                        <div class="col-sm-8 text-light">@<?php echo htmlspecialchars($user['username']); ?></div>
                    </div>

                    <h5 class="fw-bold border-bottom border-secondary pb-3 mb-4"><i class="bi bi-shield-lock-fill me-2 text-danger"></i> Security</h5>
                    
                    <form action="" method="POST">
                        <div class="mb-4">
                            <label class="form-label text-secondary small fw-medium">Current Password</label>
                            <input type="password" name="current_password" class="form-control py-2 rounded-3 text-white standard-input" placeholder="Verify your current password" required>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-medium">New Password</label>
                                <input type="password" name="new_password" class="form-control py-2 rounded-3 text-white standard-input" placeholder="New Password" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-medium">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control py-2 rounded-3 text-white standard-input" placeholder="Confirm Password" required>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" name="update_password" class="btn btn-danger px-4 py-2 fw-bold rounded-3 btn-custom">Update Password</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            const input = document.getElementById('profilePicInput');
            const preview = document.getElementById('profilePreview');
            const previewKey = 'absoluteCinemaProfilePreview';

            if (!input || !preview) {
                return;
            }

            const cachedPreview = sessionStorage.getItem(previewKey);
            if (cachedPreview) {
                preview.src = cachedPreview;
                sessionStorage.removeItem(previewKey);
            }

            input.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (!file) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (event) {
                    const dataUrl = event.target.result;
                    preview.src = dataUrl;
                    sessionStorage.setItem(previewKey, dataUrl);
                };
                reader.readAsDataURL(file);
            });
        })();
    </script>
</body>
</html>