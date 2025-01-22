<?php
require_once 'db_config.php'; // Asegúrate de incluir tu archivo de configuración de base de datos

// Obtener datos del formulario enviados mediante AJAX
$data = json_decode(file_get_contents('php://input'), true);

// Validar los datos recibidos
if (!isset($data['conversation_id']) || !isset($data['sender_id']) || !isset($data['content'])) {
    http_response_code(400); // Código de error de solicitud incorrecta
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

$conversation_id = $data['conversation_id'];
$sender_id = $data['sender_id'];
$content = $data['content'];

// Conectar a la base de datos
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insertar el mensaje en la tabla `Message`
    $stmt = $pdo->prepare("
        INSERT INTO Message (conversation_id, sender_id, content, timestamp) 
        VALUES (:conversation_id, :sender_id, :content, NOW())
    ");
    $stmt->bindParam(':conversation_id', $conversation_id);
    $stmt->bindParam(':sender_id', $sender_id);
    $stmt->bindParam(':content', $content);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message_id' => $pdo->lastInsertId()]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo insertar el mensaje']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
