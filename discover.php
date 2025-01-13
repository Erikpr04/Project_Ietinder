<?php
// Verificar si la cookie "user_id" existe
if (!isset($_COOKIE['user_id'])) {
    header("Location: login.php");
    exit();
}

// Cerrar sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    setcookie("user_id", "", time() - 3600, "/"); // Eliminar la cookie
    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discover</title>
    <link type="text/css" rel="stylesheet" href="./css/style.css" />
    <script src="https://kit.fontawesome.com/74d6337d15.js" crossorigin="anonymous"></script>
    <script src="./js/jquery-3.7.1.min.js"></script>
    <script src="./js/utils.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

<body>
    <h1>¡Bienvenido a Discover!</h1>
    <form method="post">
        <button type="submit" name="logout">Cerrar sesión</button>
    </form>
</body>

</html>
