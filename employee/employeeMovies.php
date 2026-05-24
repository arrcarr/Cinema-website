<?php
session_start();
include "../database/conn.php";

if (isset($_POST['delete_movie'])) {
    $id = $_POST['movie_id'];
    $conn->query("DELETE FROM tb_movie_table WHERE movie_id = '$id'");
}
$movies = $conn->query("SELECT * FROM tb_movie_table ORDER BY movie_id DESC");
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <title>Manage Movies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-dark text-white min-vh-100 d-flex">
    <?php include '../admin/sidebar_header.php'; ?>

    <div class="flex-grow-1 p-5 overflow-auto">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3">
            <h1 class="fw-bold">Manage Movies</h1>
            <button class="btn btn-danger fw-bold"><i class="bi bi-plus-lg"></i> Add Movie</button>
        </div>

        <div class="card bg-black border-secondary overflow-hidden shadow">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead class="table-active">
                    <tr>
                        <th>ID</th>
                        <th>Poster</th>
                        <th>Title</th>
                        <th>Genre</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $movies->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['movie_id']; ?></td>
                            <td><img src="<?php echo $row['poster']; ?>" alt="poster" width="40" height="60"
                                    class="object-fit-cover rounded"></td>
                            <td class="fw-bold"><?php echo $row['title']; ?></td>
                            <td class="text-capitalize"><?php echo $row['genre']; ?></td>
                            <td>
                                <span
                                    class="badge <?php echo $row['status'] == 'released' ? 'bg-success' : 'bg-warning'; ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-light me-1"><i class="bi bi-pencil"></i></button>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="movie_id" value="<?php echo $row['movie_id']; ?>">
                                    <button type="submit" name="delete_movie" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Delete this movie?');"><i class="bi bi-trash"></i></button>
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