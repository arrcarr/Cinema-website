<?php
session_start();
include "../database/conn.php";

$message = '';

if (isset($_POST['add_theater'])) {
    $theater_name = $_POST['theater_name'];
    $location = $_POST['location'];
    $capacity = intval($_POST['capacity']);
    $conn->query("INSERT INTO theater (theater_name, location, capacity) VALUES ('$theater_name', '$location', '$capacity')");
    $message = 'Theater added.';
}

if (isset($_POST['update_theater'])) {
    $id = intval($_POST['theater_id']);
    $theater_name = $_POST['theater_name'];
    $location = $_POST['location'];
    $capacity = intval($_POST['capacity']);
    $conn->query("UPDATE theater SET theater_name = '$theater_name', location = '$location', capacity = '$capacity' WHERE theater_id = '$id'");
    $message = 'Theater updated.';
}

if (isset($_POST['delete_theater'])) {
    $id = intval($_POST['theater_id']);
    $conn->query("DELETE FROM theater WHERE theater_id = '$id'");
    $message = 'Theater deleted.';
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
            <button class="btn btn-danger fw-bold" data-bs-toggle="modal" data-bs-target="#addTheaterModal"><i class="bi bi-plus-lg"></i> Add Theater</button>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success border-0 shadow-sm fw-bold"><?php echo $message; ?></div>
        <?php endif; ?>

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
                                <button class="btn btn-sm btn-outline-light me-1" data-bs-toggle="modal"
                                    data-bs-target="#editTheaterModal<?php echo $row['theater_id']; ?>"><i class="bi bi-pencil"></i></button>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="theater_id" value="<?php echo $row['theater_id']; ?>">
                                    <button type="submit" name="delete_theater" class="btn btn-sm btn-outline-danger"><i
                                            class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="editTheaterModal<?php echo $row['theater_id']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content bg-black border-secondary text-white">
                                    <div class="modal-header border-secondary">
                                        <h5 class="modal-title fw-bold">Update Theater</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="theater_id" value="<?php echo $row['theater_id']; ?>">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Theater Name</label>
                                                <input type="text" class="form-control bg-dark text-white border-secondary" name="theater_name" value="<?php echo $row['theater_name']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Location</label>
                                                <input type="text" class="form-control bg-dark text-white border-secondary" name="location" value="<?php echo $row['location']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Capacity</label>
                                                <input type="number" min="1" class="form-control bg-dark text-white border-secondary" name="capacity" value="<?php echo $row['capacity']; ?>" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-secondary">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" name="update_theater" class="btn btn-danger fw-bold">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addTheaterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-black border-secondary text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold">Add Theater</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Theater Name</label>
                            <input type="text" class="form-control bg-dark text-white border-secondary" name="theater_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Location</label>
                            <input type="text" class="form-control bg-dark text-white border-secondary" name="location" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Capacity</label>
                            <input type="number" min="1" class="form-control bg-dark text-white border-secondary" name="capacity" required>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_theater" class="btn btn-danger fw-bold">Add Theater</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>