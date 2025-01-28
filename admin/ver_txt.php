<?php
// Seguridad: Evita que se acceda a archivos fuera de la carpeta de logs
$baseDir = realpath(__DIR__ . '/../logs');
$file = isset($_GET['file']) ? basename($_GET['file']) : '';

$filePath = realpath("$baseDir/$file");

// Verifica que el archivo esté dentro de la carpeta de logs y sea un TXT
if (!$file || !file_exists($filePath) || strpos($filePath, $baseDir) !== 0 || pathinfo($filePath, PATHINFO_EXTENSION) !== 'txt') {
    die("Archivo no válido.");
}

// Leer contenido del archivo
$content = htmlspecialchars(file_get_contents($filePath));

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/css" rel="stylesheet" href="../css/style.css?t=<?php echo time();?>"/>
    <title><?php echo htmlspecialchars($file); ?></title>
   
</head>
<body id="page-logs">
    <div class="container-logs">
        <h2>Archivo: <?php echo htmlspecialchars($file); ?></h2>
        <pre><?php echo $content; ?></pre>
        <a href="logs.php">⬅ Volver</a>
    </div>
</body>
</html>
