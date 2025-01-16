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
    $date = date("d-m-Y"); 
    $timedate= date("d-m-Y H:i:s", strtotime(date("d-m-Y H:i:s") . ' +1 hour'));

    $message = "[{$timedate}] {$action}\n"; 
    $logDir = dirname(__DIR__) . "/logs";



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