<?php
session_start();
/*header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);*/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require_once 'db_config.php';

// Conectar con la base de datos
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

// Verificar si es una solicitud POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["valid" => false, "message" => "Método no permitido"]);
    exit();
}

// Recibir los datos enviados por AJAX
$email = trim($_POST['email'] ?? '');

if (empty($email)) {
    echo json_encode(["valid" => false, "message" => "El correo es obligatorio"]);
    exit();
}

// Verificar si el usuario existe en la base de datos
$stmt = $pdo->prepare("SELECT id, name FROM User WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(["valid" => false, "message" => "El correo no está registrado"]);
    exit();
}

$name = $user['name'];
$resetToken = bin2hex(random_bytes(16));

// Guardar el token en la base de datos
$updateStmt = $pdo->prepare("UPDATE User SET verification_token = :token WHERE email = :email");
$updateStmt->bindParam(':token', $resetToken);
$updateStmt->bindParam(':email', $email);
$updateStmt->execute();

// Configurar y enviar el correo
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'paugracia7@gmail.com';
    $mail->Password = 'djsl zloc uymy xpry';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    $mail->setFrom('swipeit@iesesteveterradas.cat', 'SwipeIt');
    $mail->addAddress($email);

    // Crear enlace con el token. CAMBIAR POR EL SERVIDOR///////////////////////////////////////////////
    //$resetLink = "http://localhost:8080/rsc/reset_password.php?token=$resetToken";

    // Crear enlace con el token//para servidor
    $resetLink = "https://tinder3.ieti.site/rsc/reset_password.php?token=$resetToken";

    $mail->isHTML(true);
    $mail->Subject = 'Recupera tu password';
    $mail->Body = 
    "
    <html>
    <body>
        <div style='max-width: 460px; text-align: center; padding: 20px; background-color:rgb(255, 169, 56); border-radius: 10px;'>
            <h2 style='color: black;'>S<span style='color:red;'>w</span>ipeIt</h2>
            <p style='color: black;'>¡El <span style='color:red;'>amor</span> está a un swipe!</p>
            <p style='color: black;'>Hola $name,</p>
            <p style='color: black;'>Para cambiar tu contraseña, haz clic en el siguiente enlace:</p>
            <p><a href='$resetLink'  style='background-color: #4CAF50; color: white; padding: 14px 20px; text-decoration: none; border-radius: 5px;'>Recuperar contraseña</a></p>
            <p style='color: black;'>Si no solicitaste este cambio, ignora este mensaje.</p>
        </div>
    </body>
    </html>";

    $mail->send();
    echo json_encode(["valid" => true, "message" => "El mail de recuperación ha sido enviado"]);
} catch (Exception $e) {
    echo json_encode(["valid" => false, "message" => "Error al enviar el correo: " . $mail->ErrorInfo]);
}

unset($pdo);
?>
