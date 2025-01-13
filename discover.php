<?php
// Verificar si la cookie "user_id" existe
if (!isset($_COOKIE['user_id'])) {
    // El script ahora estará antes de la cabecera HTML
    echo "<script>sendLog('Intento de acceso a Discover sin sesión activa, redirigiendo a login.php');</script>";
    header("Location: login.php");
    exit();
}

// Cerrar sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    echo "<script>sendLog('Usuario ha cerrado sesión, redirigiendo a login.php');</script>";
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
    
    <!-- Asegúrate de que el script de utils.js esté cargado antes de usar la función sendLog -->
    <script src="./js/utils.js"></script> 

</head>

<body>
    <h1>¡Bienvenido a Discover!</h1>
    <form method="post">
        <button type="submit" name="logout">Cerrar sesión</button>
    </form>
</body>

</html>
