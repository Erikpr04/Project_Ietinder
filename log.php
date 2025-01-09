<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if (!empty($action)) {
        createLog($action);
        echo json_encode(['success' => true, 'message' => 'Log created successfully']);
        exit;
    } 
    echo json_encode(['success' => false, 'message' => 'No action provided']);
    exit;
}


function createLog($action) {
    $date = date("d-m-Y"); // Usar guiones para evitar problemas con nombres de archivos
    $message = "[{$date}] {$action}\n"; // Añadir salto de línea para separar entradas
    $logDir = __DIR__ . "/logs";
    echo $logDir;

    // Asegurarse de que el directorio exista
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    $filename = $logDir . "/" . $date . ".txt";

    // Escribir en el archivo
    if (file_exists($filename)) {
        file_put_contents($filename, $message, FILE_APPEND);
    } else {
        file_put_contents($filename, $message);
    }
}


?>
