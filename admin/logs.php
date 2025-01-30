<?php
include 'index.php';
require_once '../rsc/log.php';



// Seguridad: Evita acceso a archivos fuera de la carpeta de logs
$baseDir = realpath(__DIR__ . '/../logs');
$logID = isset($_GET['ID']) ? basename($_GET['ID']) : '';
$filePath = realpath("$baseDir/$logID.txt");

// Si se ha pasado un ID, mostrar su contenido
if ($logID && $filePath && file_exists($filePath) && strpos($filePath, $baseDir) === 0) {
    $content = htmlspecialchars(file_get_contents($filePath));
    ?>
     <h4>Archivo: <?php echo htmlspecialchars($logID); ?></h4>

    <div id="page-logs">


        <div class="container-logs">
        <a href="logs.php">⬅ Volver</a>

            <pre><?php echo $content; ?></pre>
        </div>

    </div>


    </html>
    <?php
    exit();
}

// Si no hay un archivo específico, mostrar la lista de logs
$files = glob($baseDir . '/*.txt');
$files = array_map('basename', $files);
usort($files, fn($a, $b) => strcmp($b, $a));

// Paginación
$perPage = 25;
$totalFiles = count($files);
$totalPages = ceil($totalFiles / $perPage);
$page = isset($_GET['page']) ? max(1, min($totalPages, intval($_GET['page']))) : 1;
$startIndex = ($page - 1) * $perPage;
$filesToShow = array_slice($files, $startIndex, $perPage);

?>

    <div class="admin-container">
        <div class="admin-menu-logs">
            <h1>LOGS</h1>
            <table>
                <thead>
                    <tr>
                        <th>Nombre del Archivo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filesToShow as $logFile): ?>
                        <?php $logID = pathinfo($logFile, PATHINFO_FILENAME); // Elimina la extensión ?>
                        <tr class="clickable-row">
                            <td>
                                <a href="logs.php?ID=<?php echo urlencode($logID); ?>">
                                    <?php echo htmlspecialchars($logID); ?>
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
    </div>
</html>
