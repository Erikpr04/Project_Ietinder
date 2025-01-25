<?php
session_start();
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
$name = trim($_POST['name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');

if (empty($email) || empty($name)) {
    echo json_encode(["valid" => false, "message" => "Faltan datos obligatorios"]);
    exit();
}

// Verificar si el usuario existe en la base de datos
$stmt = $pdo->prepare("SELECT id FROM User WHERE email = :email AND name = :name AND last_name = :lastName");
$stmt->bindParam(':email', $email);
$stmt->bindParam(':name', $name);
$stmt->bindParam(':lastName', $lastName);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    echo json_encode(["valid" => false, "message" => "Los datos introducidos no son correctos"]);
    exit();
}

// Generar un token único para la recuperación de contraseña
$resetToken = bin2hex(random_bytes(16));
$updateStmt = $pdo->prepare("UPDATE User SET verification_token = :token WHERE email = :email");
$updateStmt->bindParam(':token', $resetToken);
$updateStmt->bindParam(':email', $email);
$updateStmt->execute();

// Configurar y enviar el correo
$mail = new PHPMailer(true);
try {
    // Configuración del servidor SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';  
    $mail->SMTPAuth = true;
    $mail->Username = 'paugracia7@gmail.com';  
    $mail->Password = 'djsl zloc uymy xpry'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    $mail->setFrom('swipeit@iesesteveterradas.cat', 'SwipeIt');
    $mail->addAddress($email);

    //enlace para apache
    //$resetLink = "https://tinder3.ieti.site/forgot_password.php?token=$resetToken";

    // Crear enlace con el token. CAMBIAR POR EL SERVIDOR///////////////////////////////////////////////
    $resetLink = "http://localhost:8080/rsc/verify.php?token=$verificationToken";

    $mail->isHTML(true);
    $mail->Subject = 'Recuperación de contraseña';
    $mail->Body = 
    '
     <html>
     <head>
     </head>
         <body style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; font-family: Arial, sans-serif; margin: 0; padding: 0; background-size: 400% 400%; color: #333;">

         <div style="text-align: center; width: 100%; max-width: 460px; padding: 20px; background-color:rgb(255, 169, 56); border-radius: 10px; box-sizing: border-box;">
             <h2 style="font-family: Arial, sans-serif; font-size: 4rem; color: black;">S<span style="color:rgb(255, 0, 0);">w</span>ipeIt</h2>
             <p style="font-size: 1.2rem; color: black;">¡El <span style="color:rgb(255, 0, 0);">amor</span> está a un swipe!</p>
             
             <p style="color: black;">Hola ' . $name . ',</p>
             <p style="color: black;">Gracias por confiar en SwipeIt. Para completar el cambio de contraseña, haz clic en el siguiente botón:</p>
             <p><a href='. $resetLink . ' style="background-color:rgb(83, 76, 175); color: black; padding: 14px 20px; text-align: center; text-decoration: none; display: inline-block; font-size: 1.2rem; border-radius: 5px;">Recuperar contraseña</a></p>
             <p style="color: black;">Si no has realizado este registro, puedes ignorar este correo.</p>
         </div>
         </body>
     </html>';

    $mail->send();
    echo json_encode(["valid" => true, "message" => "El mail de recuperación ha sido enviado"]);

} catch (Exception $e) {
    echo json_encode(["valid" => false, "message" => "Error al enviar el correo: " . $mail->ErrorInfo]);
}

unset($pdo);
?>
