<?php
session_start();

// Strict Admin Check
if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'admin') {
    die("<h2 style='color:red; text-align:center; margin-top:50px;'>Access Denied. Admins Only.</h2>");
}

include "../database/conn.php";

if (isset($_POST['delete_user'])) {
    $id = $_POST['user_id'];
    $conn->query("DELETE FROM user WHERE user_id = '$id'");
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
        </div>
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
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                    <button type="submit" name="delete_user" class="btn btn-sm btn-outline-danger" <?php echo ($row['userType'] == 'admin') ? 'disabled' : ''; ?>><i
                                            class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>