<?php

// Cargar las variables del archivo .env
require_once 'db_config.php';
require 'log.php';


    // Obtener las variables de entorno necesarias con valores por defecto
    $host = getenv('DB_HOST');
    $dbname = getenv('DB_NAME') ;
    $username = getenv('DB_USERNAME');
    $password = getenv('DB_PASSWORD') ;

    if (isset($_COOKIE['user_id'])) {
        $userid = $_COOKIE['user_id'];
    }else{
        echo "Error, no user id";
    }


    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error al conectar a la base de datos: " . $e->getMessage());
    }

    // Despues eliminamos de la bbdd
    $query = $pdo->prepare("UPDATE User SET account_status='deleted' WHERE id = :user_id");
    $query->execute([
        ":user_id" => $userid,
    ]);

    createLog("User deleted Succesfully");

    echo json_encode(['success' => 'User deleted correctly']);












?>