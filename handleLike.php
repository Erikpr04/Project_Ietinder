<?php
// Establecer tipo de contenido como JSON
header('Content-Type: application/json');


require_once 'log.php';
error_log("entered handlelike");

createLog("Procesando likes");

// Obtener los datos enviados desde el cliente
$data = json_decode(file_get_contents('php://input'), true);

// Verificar si la cookie "user_id" está definida
if (!isset($_COOKIE['user_id'])) {
    echo json_encode(['error' => 'Cookie "user_id" no encontrada.']);
    http_response_code(400); 
    exit();
}

// Obtener valores de los datos enviados
$user1_id = (int)$data['user1_id'];  
$user2_id = (int)$data['user2_id'];

// Validar parámetros requeridos
if (!$user1_id || !$user2_id) {
    createLog("Error en alguno de los ids");

    echo json_encode(['error' => 'Faltan parámetros']);
    http_response_code(400); 
    exit();
}

try {
    // Conectar a la base de datos
    $pdo = new PDO('mysql:host=localhost;dbname=SwipeITDB;charset=utf8mb4', 'root', 'NuevoPAssword');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consultar si existe una interacción entre los dos usuarios
    $sql = "SELECT * FROM Interaction WHERE 
            (user1_id = :user1_id AND user2_id = :user2_id) 
            OR (user1_id = :user2_id AND user2_id = :user1_id)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user1_id' => $user1_id, ':user2_id' => $user2_id]);

    $interaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($interaction) {
        // Si la interacción ya existe y `matched` es falso, actualizar a `true`
        if ($interaction['type'] === 'like' && $interaction['matched'] == false) {
            $updateSql = "UPDATE Interaction 
                          SET matched = true 
                          WHERE (user1_id = :user1_id AND user2_id = :user2_id) 
                          OR (user1_id = :user2_id AND user2_id = :user1_id)";
            
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([':user1_id' => $user1_id, ':user2_id' => $user2_id]);

            // Lógica para notificar al usuario
            echo json_encode(['match' => true]);
            createLog("Usuario ha dado like, hay match");

        } else {
            // Si `matched` ya era verdadero, no hacer nada más
            echo json_encode(['match' => false]);
        }
    } else {
        // Si no existe interacción, crear una nueva con `matched = false`
        $insertSql = "INSERT INTO Interaction (user1_id, user2_id, type, matched) 
                      VALUES (:user1_id, :user2_id, 'like', false)";
        
        $insertStmt = $pdo->prepare($insertSql);
        $insertStmt->execute([':user1_id' => $user1_id, ':user2_id' => $user2_id]);
        createLog("Usuario ha dado like, no hay match");

        echo json_encode(['match' => false]);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
    http_response_code(500); // Código HTTP 500: Error interno del servidor
    exit();
}
?>
