<?php
session_start();

//Admin check
if (!isset($_SESSION['userType']) || !in_array(strtolower((string) $_SESSION['userType']), ['admin', 'administrator'], true)) {
    die("<h2 style='color:red; text-align:center; margin-top:50px;'>Access Denied. Admins Only.</h2>");
}

include "../database/conn.php";

$message='';

if (isset($_POST['update_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $userType = $_POST['userType'];
    $password = md5($_POST['password']);
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];

    
    $stmt = $conn->prepare('UPDATE user SET username = ?, email = ?, userType = ?, password = ?, fname = ?, lname = ? WHERE user_id = ?');
    if ($stmt) {
        $stmt->bind_param('sssi', $username, $email, $userType, $password, $fname,$lname, $id);
        if ($stmt->execute()) {
            $message = 'User updated successfully.';
        } else {
            $message = 'Failed to update user.';
        }
        $stmt->close();
    } else {
        $message = 'Failed to prepare user update.';
    }
}

if (isset($_POST['delete_user'])) {
    $id = $_POST['user_id'];
    $conn->query("DELETE FROM user WHERE user_id = '$id'");
    $message = 'User deleted successfully.';
}
$users = $conn->query("SELECT * FROM user");
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-dark text-white min-vh-100 d-flex">
    <?php include 'sidebar_header.php'; ?>

    <div class="flex-grow-1 p-5 overflow-auto">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3">
            <h1 class="fw-bold text-danger">Manage Users</h1>
            <form action="EmployeeRegistrationForm.php" method="post">
                <input type="submit" value="Create Account" class="btn btn-danger">
            </form>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success border-0 shadow-sm fw-bold"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="card bg-black border-danger overflow-hidden shadow">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead class="table-active">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['user_id']; ?></td>
                            <td class="fw-bold"><?php echo $row['username']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td>
                                <span class="badge <?php echo $row['userType'] == 'admin' ? 'bg-danger' : 'bg-primary'; ?>">
                                    <?php echo $row['userType']; ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-light me-1" data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $row['user_id']; ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                    <button type="submit" name="delete_user" class="btn btn-sm btn-outline-danger" <?php echo ($row['userType'] == 'admin') ? 'disabled' : ''; ?> onclick="return confirm('Delete this user?');">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    $users->data_seek(0); // Reset database pointer to reuse the dataset
    while ($row = $users->fetch_assoc()):
    ?>
        <div class="modal fade" id="editUserModal<?php echo $row['user_id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content bg-black border-secondary text-white">
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title fw-bold text-danger">Update User</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body row g-3">
                            <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                            
                            <div class="col-12">
                                <label class="form-label fw-bold">Username</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" name="username" value="<?php echo $row['username']; ?>" required>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" class="form-control bg-dark text-white border-secondary" name="email" value="<?php echo $row['email']; ?>" required>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label fw-bold">User Type</label>
                                <select class="form-select bg-dark text-white border-secondary" name="userType" required>
                                    <option value="User">User</option>
                                    <option value="Administrator">Administrator</option>
                                    <option value="Employee">Employee</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Password</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" name="password" value="Change password?">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">First Name</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" name="fname" value="<?php echo $row['fname']; ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Last Name</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" name="lname" value="<?php echo $row['lname']; ?>">
                            </div>

                        </div>

                    </form>
                </div>
            </div>
        </div>
    <?php endwhile; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>