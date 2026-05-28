<?php
include "database/conn.php";

$sql = "SELECT movie_id, title, genre, duration, release_date, description, poster FROM tb_movie_table where status='released'";
$result = $conn->query($sql);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">

    <style>
        .zoom {
            transition: transform .2s;
        }

        .zoom:hover {
            transform: scale(1.1);
            border-color: red;
        }
    </style>

</head>

<body>
    <div class="d-flex flex-row justify-content-evenly pb-5">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '
                    <div class="card bg-dark text-white zoom" style="width:20%">
                    <a href="movieBooking.php?' . $row["movie_id"] . '">
                        <img src="' . $row["poster"] . '" class="card-img-top" alt="...">
                        
                        <div class="card-body">
                            <h5 class="card-title">' . $row["title"] . '</h5>
                            <p class="card-text">' . $row["genre"] . '</p>
                            <span class="card-text">' . $row["duration"] . '</span>
                            <span class="card-text">' . $row["release_date"] . '</span>
                        </div>
                        </a>
                    </div>
                
                ';
            }
        } else {
            echo "0 results";
        }
        ?>

    </div>
</body>

</html>