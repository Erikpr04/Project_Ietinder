<?php
// Cargar las variables del archivo .env
require_once 'db_config.php';

// Obtener las variables de entorno necesarias con valores por defecto
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: '';
$username = getenv('DB_USERNAME') ?: '';
$password = getenv('DB_PASSWORD') ?: '';

// Verificar que las variables requeridas estén definidas
if (!$dbname || !$username || !$password) {
    die("Error de configuración de la base de datos");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode($_POST['data'], true);

    try {
        // Crear conexión PDO
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error al conectar a la base de datos: ' . $e->getMessage()
        ]);
        exit;
    }

    // Preparar la consulta
    $query = $pdo->prepare("UPDATE User
                            SET name=:name, last_name=:lastName, alias=:alias, birth_date=:birthDate, latitude=:latitude, 
                            longitude=:longitude, sex=:sex, sexual_orientation=:sexOrientation
                            WHERE id=:id");

    $query->bindParam(":name", $data['name']);
    $query->bindParam(":lastName", $data['lastName']);
    $query->bindParam(":alias", $data['alias']);
    $query->bindParam(":birthDate", $data['birthDate']);
    $query->bindParam(":latitude", $data['latitude']);
    $query->bindParam(":longitude", $data['longitude']);
    $query->bindParam(":sex", $data['gender']);
    $query->bindParam(":sexOrientation", $data['sexOrientation']);

    // Obtener el valor de la cookie
    if (isset($_COOKIE['user_id'])) {
        $cookieValue = $_COOKIE['user_id'];
        $query->bindParam(":id", $cookieValue);
    }

    try {
        if ($query->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Datos actualizados correctamente'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error al actualizar los datos'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error al ejecutar la consulta: ' . $e->getMessage()
        ]);
    }

    createLog($cookieValue." ha hecho cambios en su perfil");

    unset($pdo);
    unset($query);
}
