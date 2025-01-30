<?php
require_once 'db_config.php';
require_once 'log.php';

$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

// Verificar que las variables requeridas estén definidas
if (!$dbname || !$username || !$password) {
    die("Error de configuración de la base de datos");
}

if (!isset($_GET['message_id']) || !isset($_COOKIE['user_id'])) {
    die(json_encode(['success' => false, 'error' => 'Faltan parámetros necesarios.'])); 
}

$messageId = $_GET['message_id'];
$currentUserId = $_COOKIE['user_id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si el mensaje ya tiene un corazón
    $query = $pdo->prepare("SELECT is_heart FROM Message WHERE id = :message_id AND sender_id != :current_user_id");
    $query->bindParam(':message_id', $messageId, PDO::PARAM_INT);
    $query->bindParam(':current_user_id', $currentUserId, PDO::PARAM_INT);
    $query->execute();
    
    $result = $query->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        // Cambiar el valor de is_heart
        $newHeartStatus = $result['is_heart'] ? 0 : 1;  // Si ya es 1 (corazón marcado), lo cambiamos a 0, y viceversa
        
        $updateQuery = $pdo->prepare("UPDATE Message SET is_heart = :new_heart_status WHERE id = :message_id");
        $updateQuery->bindParam(':new_heart_status', $newHeartStatus, PDO::PARAM_INT);
        $updateQuery->bindParam(':message_id', $messageId, PDO::PARAM_INT);
        $updateQuery->execute();
        
        echo json_encode(['success' => true, 'new_status' => $newHeartStatus]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se encontró el mensaje o no es tuyo.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

unset($pdo);
createLog("Usuario $currentUserId actualizó el corazón para el mensaje $messageId.");
?>
