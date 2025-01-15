<?php
function loadEnv($file) {
    if (!file_exists($file)) {
        die("El archivo .env no existe.");
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Ignorar los comentarios
        if (strpos($line, '#') === 0) {
            continue;
        }

        // Dividir la línea en clave y valor
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

// Cargar las variables del archivo .env
// Cargar las variables del archivo .env
loadEnv(dirname(__DIR__) . '/.env');

$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');
