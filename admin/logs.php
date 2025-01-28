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


<?php

$dir = __DIR__ . '/../logs/'; // Directorio donde están los archivos txt
$files = glob($dir . '*.txt'); // Obtener todos los archivos txt

// Extraer solo los nombres de los archivos y ordenar por fecha (nombre del archivo)
$files = array_map('basename', $files);
usort($files, function($a, $b) {
    return strcmp($b, $a); // Orden descendente (más nuevo primero)
});

// Paginación
$perPage = 25;
$totalFiles = count($files);
$totalPages = ceil($totalFiles / $perPage);
$page = isset($_GET['page']) ? max(1, min($totalPages, intval($_GET['page']))) : 1;
$startIndex = ($page - 1) * $perPage;
$filesToShow = array_slice($files, $startIndex, $perPage);

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/css" rel="stylesheet" href="../css/style.css?t=<?php echo time();?>"/>
    <script src="https://kit.fontawesome.com/74d6337d15.js" crossorigin="anonymous"></script>
    <title>Listado de Logs</title>
   
</head>
<body id="admin-logs">


    <div class="admin-container">
        
        <div class="main-header-index-admin"><h2>S<span>w</span>ipeIt</h2></div>

        <div class="admin-menu-logs">
            <h1>LOGS</h1>
            <table>
    <thead>
        <tr>
            <th>Nombre del Archivo</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($filesToShow as $file): ?>
        <tr class="clickable-row">
            <td>
                <a href="ver_txt.php?file=<?php echo urlencode($file); ?>" target="_blank">
                    <?php echo htmlspecialchars($file); ?>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>

</table>

    
</div>
<div class="pagination-logs">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>">Anterior</a>
    <?php endif; ?>
    Página <?php echo $page; ?> de <?php echo $totalPages; ?>
    <?php if ($page < $totalPages): ?>
        <a href="?page=<?php echo $page + 1; ?>">Siguiente</a>
    <?php endif; ?>
</div>

</body>
</html>
