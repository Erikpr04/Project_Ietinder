<script src="./js/utils.js"></script>

<?php
if (!isset($_COOKIE['user_id'])) {
    echo "<script>sendLog('User tried to access into Discover.php without an active session, redirecting him to login.php').then(() => { window.location.href = 'login.php'; });</script>";
    exit();
}

// Cerrar sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    $userId_holder = $_COOKIE["user_id"];
    setcookie("user_id", "", time() - 3600, "/"); // Eliminar la cookie
    echo "<script>sendLog('User $userId_holder dropped his session, redirecting him to login.php from discover.php').then(() => { window.location.href = 'login.php'; });</script>";

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
