<?php
require_once 'db_config.php';
require_once 'log.php';

$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

if (!isset($_GET['conversation_id']) || !isset($_COOKIE['user_id'])) {
    die(json_encode(['success' => false, 'error' => 'Faltan parámetros necesarios.']));
}

$conversationId = $_GET['conversation_id'];
$currentUserId = $_COOKIE['user_id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = $pdo->prepare("SELECT 
            CASE 
                WHEN c.user1_id = :current_user_id THEN u2.name
                ELSE u1.name
            END AS name,
            (SELECT m.media_path
             FROM Media m
             WHERE m.user_id = CASE 
                                 WHEN c.user1_id = :current_user_id THEN c.user2_id
                                 ELSE c.user1_id
                              END
             LIMIT 1) AS photo,
            CASE 
                WHEN c.user1_id = :current_user_id THEN TIMESTAMPDIFF(YEAR, u2.birth_date, CURDATE())
                ELSE TIMESTAMPDIFF(YEAR, u1.birth_date, CURDATE())
            END AS age
        FROM Conversation c
        JOIN User u1 ON c.user1_id = u1.id
        JOIN User u2 ON c.user2_id = u2.id
        WHERE c.id = :conversation_id
          AND :current_user_id IN (c.user1_id, c.user2_id)
        LIMIT 1;
    ");
    
    $query->bindParam(':conversation_id', $conversationId, PDO::PARAM_INT);
    $query->bindParam(':current_user_id', $currentUserId, PDO::PARAM_INT);
    $query->execute();

    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode(['success' => true, 'data' => $result]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se encontró información para esta conversación.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

unset($pdo);
createLog("Usuario $currentUserId solicitó perfil del usuario en la conversación $conversationId.");
?>
