<?php 
session_start();
if(isset($_POST["submit"])){
    $_SESSION['login'] = true;
    header("Location: ../index.php");
}

?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="../css/bootstrap.css">
        <link rel="stylesheet" href="../js/bootstrap.js">
        <link rel="stylesheet" href="../js/popper.min.js">
    </head>
    <body class="col-12 bg-warning vh-100 d-flex align-items-center justify-content-center">
        <main class="col-8 h-75 bg-dark border-5 d-flex" style="border-radius: 10px;">
            <div class="col-5 p-5 h-100 d-flex flex-column align-items-center justify-content-center border-end border-light">
                <img src="/asset/certificate of.png" alt="" class="w-100 bg-light" style="box-shadow: 0px 0px 10px black inset;">
                <label for="" class="h4 mt-4 fw-bold text-light">SMKN WARSIH JAKARTA</label>
            </div>
            <div class="col-7 h-100 d-flex flex-column align-items-center justify-content-center">
                <label for="" class="h3 text-center text-light" style="max-width: 85%;">FORM ABSENSI SMKN WARSIH JAKARTA</label>
                <form action="" method="post" class="w-auto">
                    <input type="submit" class="btn border-1 border-dark w-25 mt-5 bg-warning fw-bold" name="submit">MASUK</input>
                </form>
            </div>
        </main>
    </body>
</html>
