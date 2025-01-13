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
</head>

<body>
    <h1>¡Bienvenido a Discover!</h1>
    <form method="post">
        <button type="submit" name="logout">Cerrar sesión</button>
    </form>
</body>

</html>
