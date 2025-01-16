<?php

require_once 'db_config.php';

if (isset($_POST['idUsuario'])) {

    $idUsuario = $_POST['idUsuario'];

    // configuración de la base de datos
// Obtener las variables de entorno necesarias con valores por defecto
$host = getenv('DB_HOST') ;
$dbname = getenv('DB_NAME') ;
$username = getenv('DB_USERNAME') ;
$password = getenv('DB_PASSWORD');

    // Conexión a la base de datos
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error al conectar a la base de datos: ' . $e->getMessage()
        ]);
        exit;
    }

    $query = $pdo->prepare("
    SELECT m.content, u.name
    FROM Message m
    JOIN User u ON m.sender_id = u.id
    WHERE m.sender_id = :idSender
    ORDER BY m.timestamp DESC
    LIMIT 1
");
$query->bindParam(":idSender", $idUsuario);


    try {
        if ($query->execute()) {
            $result = $query->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                echo json_encode($result['content']);
            } else {
                echo json_encode("No hay mensajes para este usuario.");
            }
        }
        else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error al recoger el último mensaje'
            ]);
        }
    }
    catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error al ejecutar la consulta: ' . $e->getMessage()
        ]);
    }


    unset($pdo);
    unset($query);
}