<?php
require_once 'db_config.php'; // Archivo de configuración de la base de datos
require_once 'log.php';      // Archivo de logs

if (!isset($_GET['conversation_id'])) {
    echo json_encode(['success' => false, 'error' => 'ID de conversación no especificado.']);
    exit;
}

$conversation_id = intval($_GET['conversation_id']);
$last_message_id = isset($_GET['last_message_id']) ? intval($_GET['last_message_id']) : 0;
$user_id = isset($_COOKIE['user_id']) ? intval($_COOKIE['user_id']) : null;

if (!$user_id) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado.']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = $pdo->prepare(" SELECT 
        m.id AS message_id,
        m.content,
        m.timestamp,
        m.sender_id,
        CASE 
            WHEN m.sender_id = :current_user_id THEN NULL
            ELSE (SELECT media_path FROM Media WHERE user_id = m.sender_id LIMIT 1)
        END AS sender_photo
    FROM Message m
    WHERE m.conversation_id = :conversation_id
      AND m.id > :last_message_id
    ORDER BY m.timestamp ASC;
    ");
    $query->bindParam(':conversation_id', $conversation_id, PDO::PARAM_INT);
    $query->bindParam(':last_message_id', $last_message_id, PDO::PARAM_INT);
    $query->bindParam(':current_user_id', $user_id, PDO::PARAM_INT);
    $query->execute();

    $messages = $query->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'messages' => $messages]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Error al obtener mensajes: ' . $e->getMessage()]);
}
