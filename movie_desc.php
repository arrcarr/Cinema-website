<?php 
    include "header.php"; 
    include "conn.php";
    $movie = $_SERVER['QUERY_STRING'];

    $sql = "SELECT * FROM tb_movie_table where movie_id=" . $movie . "";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $row['title'] ?></title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <style>
        .overlay::before {
  content: '';
  z-index: 10;
  width: 100%;
  height: 100%;
  display: block;
  position: absolute;
  background: linear-gradient(0deg, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0) 50%);
}
    </style>
</head>

<body class="bg-black">

    <span class="before">
        <div class="container text-white" style="background-image: url(<?php echo $row['poster']?>); height:50vh; background-position:center; background-size:cover;">
            <h1 class="fw-bold"> <?php echo $row['title']?> </h1>
        </div>
    </span>

    <div class="container text-white">
        <h1 class="fw-bold">Synopsis:</h1>
        <p> <?php echo $row['description']?> </p>
    </div>
</body>
</html>