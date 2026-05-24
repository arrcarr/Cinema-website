<?php
session_start();
include "../database/conn.php";

if (isset($_POST['delete_theater'])) {
    $id = $_POST['theater_id'];
    $conn->query("DELETE FROM theater WHERE theater_id = '$id'");
}
$theaters = $conn->query("SELECT * FROM theater");
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <title>Manage Theaters</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-dark text-white min-vh-100 d-flex">
    <?php include '../admin/sidebar_header.php'; ?>

    <div class="flex-grow-1 p-5 overflow-auto">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3">
            <h1 class="fw-bold">Manage Theaters</h1>
            <button class="btn btn-danger fw-bold"><i class="bi bi-plus-lg"></i> Add Theater</button>
        </div>
        <div class="card bg-black border-secondary overflow-hidden shadow">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead class="table-active">
                    <tr>
                        <th>ID</th>
                        <th>Theater Name</th>
                        <th>Location</th>
                        <th>Capacity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $theaters->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php echo $row['theater_id']; ?>
                            </td>
                            <td class="fw-bold">
                                <?php echo $row['theater_name']; ?>
                            </td>
                            <td>
                                <?php echo $row['location']; ?>
                            </td>
                            <td>
                                <?php echo $row['capacity']; ?> Seats
                            </td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="theater_id" value="<?php echo $row['theater_id']; ?>">
                                    <button type="submit" name="delete_theater" class="btn btn-sm btn-outline-danger"><i
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