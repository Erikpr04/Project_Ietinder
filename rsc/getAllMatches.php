<?php
// Cargar las variables del archivo .env
require_once 'db_config.php';

// Obtener las variables de entorno necesarias con valores por defecto
$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME') ;
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD') ;

// Verificar que las variables requeridas estén definidas
if (!$dbname || !$username || !$password) {
    die("Error de configuración de la base de datos");
}

    // !!! cookie: user_id:"n"
    if (isset($_COOKIE['user_id'])) {
        $cookieValue = $_COOKIE['user_id'];
    } 

    // Conexión a la base de datos
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error al conectar a la base de datos: " . $e->getMessage());
    }

    // Ejecutar consulta 
    $query = $pdo->prepare("SELECT c.id AS idConversation,c.user1_id,c.user2_id,c.started,u2.name AS name,
                                (SELECT m.media_path
                                    FROM Media m
                                    WHERE (m.user_id = c.user1_id OR m.user_id = c.user2_id)
                                    LIMIT 1) AS media_path       
                            FROM Conversation c
                            JOIN User u ON c.user1_id = u.id OR c.user2_id = u.id
                            JOIN User u2 ON c.user2_id = u2.id
                            WHERE :id IN (c.user1_id, c.user2_id)
                            GROUP BY c.id, c.user1_id, c.user2_id, c.started, u2.name;
                            ");
    $query->bindParam(":id", $cookieValue);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    // Liberar recursos
    unset($pdo);
    unset($query);

