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
    <title>Listado de Logs</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .pagination { margin-top: 10px; }
    </style>
</head>
<body>
    <h2>Listado de Logs</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre del Archivo</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($filesToShow as $file): ?>
                <tr>
                    <td><?php echo htmlspecialchars($file); ?></td>
                    <td><a href="../logs/<?php echo urlencode($file); ?>" target="_blank">Ver</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="pagination">
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
