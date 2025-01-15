<?php
header('Content-Type: application/json');
include_once 'db_config.php';

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit();
}

// Obtener y verificar variables de entorno
$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');


if (!$dbname || !$username || !$password) {
    echo json_encode(['error' => 'Error de configuración del servidor']);
    exit();
}

// Obtener y verificar datos de entrada
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['error' => 'Datos inválidos']);
    exit();
}

// Verificar cookie
if (!isset($_COOKIE['user_id'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

// Validar IDs
$user1_id = isset($data['user1_id']) ? (int)$data['user1_id'] : 0;
$user2_id = isset($data['user2_id']) ? (int)$data['user2_id'] : 0;

if (!$user1_id || !$user2_id) {
    echo json_encode(['error' => 'IDs inválidos']);
    exit();
}

try {
    // Conexión a la base de datos
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Primero, verificar si el otro usuario ya nos dio like
    $checkLikeSql = "SELECT * FROM Interaction 
                     WHERE user1_id = :user2_id 
                     AND user2_id = :user1_id 
                     AND type = 'like'";
    
    $checkStmt = $pdo->prepare($checkLikeSql);
    $checkStmt->execute([
        ':user1_id' => $user1_id,
        ':user2_id' => $user2_id
    ]);
    
    $existingLike = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existingLike) {
        // El otro usuario ya nos dio like, actualizamos ambos registros a matched
        $updateSql = "UPDATE Interaction 
                     SET matched = true 
                     WHERE ((user1_id = :user1_id AND user2_id = :user2_id) 
                     OR (user1_id = :user2_id AND user2_id = :user1_id))
                     AND type = 'like'";
        
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([
            ':user1_id' => $user1_id,
            ':user2_id' => $user2_id
        ]);

        // Hay match
        echo json_encode(['match' => true]);
    } else {
        // El otro usuario no nos ha dado like aún, creamos nuevo registro
        $insertSql = "INSERT INTO Interaction (user1_id, user2_id, type, matched) 
                     VALUES (:user1_id, :user2_id, 'like', false)";
        
        $insertStmt = $pdo->prepare($insertSql);
        $insertStmt->execute([
            ':user1_id' => $user1_id,
            ':user2_id' => $user2_id
        ]);

        // No hay match aún
        echo json_encode(['match' => false]);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la base de datos']);
    exit();
} catch (Exception $e) {
    echo json_encode(['error' => 'Error del servidor']);
    exit();
}
?>