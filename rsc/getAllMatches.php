<?php
// Cargar las variables del archivo .env
require_once 'db_config.php';
require_once 'log.php';


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
    $query = $pdo->prepare(" SELECT 
        c.id AS idConversation,
        c.user1_id,
        c.user2_id,
        c.started,
        CASE 
            WHEN c.user1_id = :id THEN u2.name
            ELSE u1.name
        END AS other_user_name,
        (SELECT m.media_path
         FROM Media m
         WHERE m.user_id = CASE 
                             WHEN c.user1_id = :id THEN c.user2_id
                             ELSE c.user1_id
                          END
         LIMIT 1) AS media_path
    FROM Conversation c
    JOIN User u1 ON c.user1_id = u1.id
    JOIN User u2 ON c.user2_id = u2.id
    WHERE :id IN (c.user1_id, c.user2_id);
");

    $query->bindParam(":id", $cookieValue);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    // Liberar recursos
    unset($pdo);
    unset($query);

    createLog("Usuario con el id: ".$cookieValue." ha consultado sus matches en la página messages.php");

    echo json_encode($results);
