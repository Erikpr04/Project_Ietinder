<?php
require_once '../rsc/log.php';
require_once '../rsc/db_config.php';

if (isset($_COOKIE['user_id'])) {
    $host = getenv('DB_HOST');
    $dbname = getenv('DB_NAME');
    $username = getenv('DB_USERNAME');
    $pass = getenv('DB_PASSWORD');

    $connection = new mysqli($host, $username, $pass, $dbname);

    if ($connection->connect_error) {
        die("Conexión fallida: " . $connection->connect_error);
    }

    $sql = "SELECT role_user FROM User WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $_COOKIE['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['role_user'] === 'admin') {
            createLog(action: "Acceso permitido al panel de administración");
            // Continúa cargando el panel
        } else {
            createLog(action: "Acceso denegado: usuario no admin");
            header("HTTP/1.1 403 Forbidden");
            include '../error/403.php';
            exit();
        }
    } else {
        createLog(action: "Usuario no encontrado, redirigiendo al login");
        header('Location: ../index.php');
        exit();
    }

    $stmt->close();
    $connection->close();
} else {
    createLog(action: "Acceso denegado: usuario no autenticado");
    header("HTTP/1.1 401 Unauthorized");
    include '../error/401.php';
    exit();
}
?>
