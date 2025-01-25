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
    die("Error en la conexión a la base de datos");
}

// Verificar si hay una sesión activa con el email del usuario
if (!isset($_SESSION['reset_email'])) {
    die("Acceso no autorizado");
}

$email = $_SESSION['reset_email'];
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validar que las contraseñas coincidan
if ($password !== $confirm_password) {
    die("Las contraseñas no coinciden");
}

// Hashear la nueva contraseña con SHA-256
$hashedPassword = hash("sha256", $password);

// Actualizar la contraseña en la base de datos
$stmt = $pdo->prepare("UPDATE User SET password = :password, verification_token = NULL WHERE email = :email");
$stmt->bindParam(':password', $hashedPassword);
$stmt->bindParam(':email', $email);
$stmt->execute();

// Eliminar la sesión
session_destroy();

echo "Contraseña actualizada con éxito. Ahora puedes iniciar sesión.";
?>
