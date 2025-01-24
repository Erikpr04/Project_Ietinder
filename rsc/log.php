<?php


function createLog($action) {
    $date = date("Y-m-d"); 
    $timedate= date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . ' +1 hour'));

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