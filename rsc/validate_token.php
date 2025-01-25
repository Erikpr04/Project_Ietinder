<?php
session_start();
require '../vendor/autoload.php';
require_once 'db_config.php';

$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["valid" => false, "message" => "Error en la conexión a la base de datos"]);
    exit();
}

// Obtener el token desde la URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    echo json_encode(["valid" => false, "message" => "Token no proporcionado"]);
    exit();
}

// Verificar si el token es válido
$stmt = $pdo->prepare("SELECT email FROM User WHERE verification_token = :token");
$stmt->bindParam(':token', $token);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(["valid" => false, "message" => "Token inválido o expirado"]);
    exit();
}

// Guardar el email en sesión para que se use en el cambio de contraseña
$_SESSION['reset_email'] = $user['email'];

echo json_encode(["valid" => true, "email" => $user['email']]);
?>
