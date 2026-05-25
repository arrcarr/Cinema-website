<?php
session_start();
include "../database/conn.php";

$message = '';

function save_movie_poster($file_input_name)
{
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] !== 0) {
        return '';
    }

    $upload_dir = "../assets/posters/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $filename = uniqid('poster_', true) . '_' . basename($_FILES[$file_input_name]['name']);
    $target_path = $upload_dir . $filename;

    if (move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $target_path)) {
        return $target_path;
    }

    return '';
}

if (isset($_POST['add_movie'])) {
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $duration = $_POST['duration'];
    $release_date = $_POST['release_date'];
    $description = $_POST['description'];
    $poster = save_movie_poster('poster_image');
    $status = $_POST['status'];

    if ($poster === '') {
        $message = 'Poster upload failed.';
    } else {
        $stmt = $conn->prepare('INSERT INTO tb_movie_table (title, genre, duration, release_date, description, poster, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
        if ($stmt) {
            $stmt->bind_param('sssssss', $title, $genre, $duration, $release_date, $description, $poster, $status);
            if ($stmt->execute()) {
                $message = 'Movie added.';
            } else {
                $message = 'Failed to add movie.';
            }
            $stmt->close();
        } else {
            $message = 'Failed to prepare movie insert.';
        }
    }
}

if (isset($_POST['update_movie'])) {
    $id = intval($_POST['movie_id']);
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $duration = $_POST['duration'];
    $release_date = $_POST['release_date'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $poster = save_movie_poster('poster_image');

    if ($poster === '') {
        $poster = $_POST['current_poster'];
    }

    $stmt = $conn->prepare('UPDATE tb_movie_table SET title = ?, genre = ?, duration = ?, release_date = ?, description = ?, poster = ?, status = ? WHERE movie_id = ?');
    if ($stmt) {
        $stmt->bind_param('sssssssi', $title, $genre, $duration, $release_date, $description, $poster, $status, $id);
        if ($stmt->execute()) {
            $message = 'Movie updated.';
        } else {
            $message = 'Failed to update movie.';
        }
        $stmt->close();
    } else {
        $message = 'Failed to prepare movie update.';
    }
}

if (isset($_POST['delete_movie'])) {
    $id = intval($_POST['movie_id']);
    $stmt = $conn->prepare('DELETE FROM tb_movie_table WHERE movie_id = ?');
    if ($stmt) {
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $message = 'Movie deleted.';
        } else {
            $message = 'Failed to delete movie.';
        }
        $stmt->close();
    } else {
        $message = 'Failed to prepare movie delete.';
    }
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
            <button class="btn btn-danger fw-bold" data-bs-toggle="modal" data-bs-target="#addMovieModal"><i class="bi bi-plus-lg"></i> Add Movie</button>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success border-0 shadow-sm fw-bold"><?php echo $message; ?></div>
        <?php endif; ?>

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
                            <td><img src="<?php echo $row['poster']; ?>" alt="poster" width="40" height="60" class="object-fit-cover rounded"></td>
                            <td class="fw-bold"><?php echo $row['title']; ?></td>
                            <td class="text-capitalize"><?php echo $row['genre']; ?></td>
                            <td>
                                <span class="badge <?php echo $row['status'] == 'released' ? 'bg-success' : 'bg-warning'; ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-light me-1" data-bs-toggle="modal" data-bs-target="#editMovieModal<?php echo $row['movie_id']; ?>"><i class="bi bi-pencil"></i></button>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="movie_id" value="<?php echo $row['movie_id']; ?>">
                                    <button type="submit" name="delete_movie" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this movie?');"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    $movies->data_seek(0);
    while ($row = $movies->fetch_assoc()):
    ?>
        <div class="modal fade" id="editMovieModal<?php echo $row['movie_id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content bg-black border-secondary text-white">
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title fw-bold">Update Movie</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="modal-body row g-3">
                            <input type="hidden" name="movie_id" value="<?php echo $row['movie_id']; ?>">
                            <input type="hidden" name="current_poster" value="<?php echo $row['poster']; ?>">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Title</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" name="title" value="<?php echo $row['title']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Genre</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" name="genre" value="<?php echo $row['genre']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Duration</label>
                                <input type="time" class="form-control bg-dark text-white border-secondary" name="duration" value="<?php echo substr($row['duration'], 0, 5); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Release Date</label>
                                <input type="date" class="form-control bg-dark text-white border-secondary" name="release_date" value="<?php echo $row['release_date']; ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Poster Image</label>
                                <input type="file" class="form-control bg-dark text-white border-secondary" name="poster_image" accept="image/*">
                                <small class="text-secondary">Leave blank to keep the current poster.</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Description</label>
                                <textarea class="form-control bg-dark text-white border-secondary" name="description" rows="4" required><?php echo $row['description']; ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Status</label>
                                <select class="form-select bg-dark text-white border-secondary" name="status" required>
                                    <option value="released" <?php echo ($row['status'] == 'released') ? 'selected' : ''; ?>>released</option>
                                    <option value="unreleased" <?php echo ($row['status'] == 'unreleased') ? 'selected' : ''; ?>>unreleased</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-secondary">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="update_movie" class="btn btn-danger fw-bold">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endwhile; ?>

    <div class="modal fade" id="addMovieModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content bg-black border-secondary text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold">Add Movie</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Title</label>
                            <input type="text" class="form-control bg-dark text-white border-secondary" name="title" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Genre</label>
                            <input type="text" class="form-control bg-dark text-white border-secondary" name="genre" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Duration</label>
                            <input type="time" class="form-control bg-dark text-white border-secondary" name="duration" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Release Date</label>
                            <input type="date" class="form-control bg-dark text-white border-secondary" name="release_date" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Poster Image</label>
                            <input type="file" class="form-control bg-dark text-white border-secondary" name="poster_image" accept="image/*" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Description</label>
                            <textarea class="form-control bg-dark text-white border-secondary" name="description" rows="4" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <select class="form-select bg-dark text-white border-secondary" name="status" required>
                                <option value="released">released</option>
                                <option value="unreleased">unreleased</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_movie" class="btn btn-danger fw-bold">Add Movie</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>