<?php
session_start();
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require_once 'db_config.php';

// Obtener credenciales de la BD
$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
    exit();
}

$data = $_POST;

$requiredFields = ['name', 'lastName', 'alias', 'birthDate', 'gender', 'sexOrientation', 'email', 'latitude', 'longitude', 'password', 'password2'];
foreach ($requiredFields as $field) {
    if (!isset($data[$field]) || empty(trim($data[$field]))) {
        echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios."]);
        exit();
    }
}

// Validar email
if (strpos($data['email'], '@iesesteveterradas.cat') === false) {
    echo json_encode(["success" => false, "message" => "El email debe ser del dominio @iesesteveterradas.cat"]);
    exit();
}

// Validar contraseñas
if ($data['password'] !== $data['password2']) {
    echo json_encode(["success" => false, "message" => "Las contraseñas no coinciden."]);
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si el email ya existe
    $stmtCheck = $pdo->prepare("SELECT id FROM User WHERE email = :email");
    $stmtCheck->bindParam(':email', $data['email']);
    $stmtCheck->execute();

    if ($stmtCheck->rowCount() > 0) {
        echo json_encode(["success" => false, "message" => "Ya existe un usuario con ese email."]);
        exit();
    }

    // Generar un token único
    $verificationToken = bin2hex(random_bytes(16));

    // Insertar usuario
    $stmt = $pdo->prepare("INSERT INTO User (name, last_name, alias, birth_date, sex, sexual_orientation, email, latitude, longitude, password, account_status, verification_token) 
        VALUES (:name, :lastName, :alias, :birthDate, :gender, :sexOrientation, :email, :latitude, :longitude, :password, 'pending', :verificationToken)");

    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':lastName', $data['lastName']);
    $stmt->bindParam(':alias', $data['alias']);
    $stmt->bindParam(':birthDate', $data['birthDate']);
    $stmt->bindParam(':gender', $data['gender']);
    $stmt->bindParam(':sexOrientation', $data['sexOrientation']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':latitude', $data['latitude']);
    $stmt->bindParam(':longitude', $data['longitude']);
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':verificationToken', $verificationToken);

    if ($stmt->execute()) {
        // Enviar correo con el botón de validación
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

            // Configurar remitente y destinatario
            $mail->setFrom('swipeit@iesesteveterradas.cat', 'SwipeIt');
            $mail->addAddress($data['email']); // Destinatario

            // Crear enlace con el token. CAMBIAR POR EL SERVIDOR
            $validationLink = "http://localhost:8080/rsc/verify.php?token=$verificationToken";

            // Contenido del correo con el botón
            $mail->isHTML(true);
            $mail->Subject = 'Validar registro';
            $mail->Body    = '
                <p>Hola ' . htmlspecialchars($data['name']) . ',</p>
                <p>Gracias por registrarte en SwipeIt. Para completar el registro y activar tu cuenta, haz clic en el siguiente botón:</p>
               
            ';

            //<p><a href="' . $validationLink . '" style="background-color: #4CAF50; color: white; padding: 14px 20px; text-align: center; text-decoration: none; display: inline-block;">Validar mi cuenta</a></p>
            //<p>Si no has realizado este registro, puedes ignorar este correo.</p>

            // Enviar correo
            $mail->send();
            echo json_encode(["success" => true, "message" => "Registro exitoso. Se ha enviado un correo de validación."]);
        } catch (Exception $e) {
            echo json_encode(["success" => true, "message" => "Registro exitoso, pero error al enviar correo: " . $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Error al registrar el usuario."]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error en la base de datos: " . $e->getMessage()]);
}

/*session_start();
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require_once 'db_config.php';

// Obtener credenciales de la BD
$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
    exit();
}

$data = $_POST;

$requiredFields = ['name', 'lastName', 'alias', 'birthDate', 'gender', 'sexOrientation', 'email', 'latitude', 'longitude', 'password', 'password2'];
foreach ($requiredFields as $field) {
    if (!isset($data[$field]) || empty(trim($data[$field]))) {
        echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios."]);
        exit();
    }
}

// Validar email
if (strpos($data['email'], '@iesesteveterradas.cat') === false) {
    echo json_encode(["success" => false, "message" => "El email debe ser del dominio @iesesteveterradas.cat"]);
    exit();
}

// Validar contraseñas
if ($data['password'] !== $data['password2']) {
    echo json_encode(["success" => false, "message" => "Las contraseñas no coinciden."]);
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si el email ya existe
    $stmtCheck = $pdo->prepare("SELECT id FROM User WHERE email = :email");
    $stmtCheck->bindParam(':email', $data['email']);
    $stmtCheck->execute();

    if ($stmtCheck->rowCount() > 0) {
        echo json_encode(["success" => false, "message" => "Ya existe un usuario con ese email."]);
        exit();
    }

    // Insertar usuario
    $stmt = $pdo->prepare("INSERT INTO User (name, last_name, alias, birth_date, sex, sexual_orientation, email, latitude, longitude, password, account_status) 
        VALUES (:name, :lastName, :alias, :birthDate, :gender, :sexOrientation, :email, :latitude, :longitude, :password, 'pending')");

    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':lastName', $data['lastName']);
    $stmt->bindParam(':alias', $data['alias']);
    $stmt->bindParam(':birthDate', $data['birthDate']);
    $stmt->bindParam(':gender', $data['gender']);
    $stmt->bindParam(':sexOrientation', $data['sexOrientation']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':latitude', $data['latitude']);
    $stmt->bindParam(':longitude', $data['longitude']);
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt->bindParam(':password', $hashedPassword);

    if ($stmt->execute()) {
        // Solo enviar correo si el registro fue exitoso
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

            // Configurar remitente y destinatario
            $mail->setFrom('swipeit@iesesteveterradas.cat', 'SwipeIt');
            $mail->addAddress($data['email']); // Destinatario

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Validar registro';
            $mail->Body    = 'Test1 - Este es un correo de prueba';

            // Enviar correo
            $mail->send();
            echo json_encode(["success" => true, "message" => "Registro exitoso. Se ha enviado un correo de validación."]);
        } catch (Exception $e) {
            echo json_encode(["success" => true, "message" => "Registro exitoso, pero error al enviar correo: " . $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Error al registrar el usuario."]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error en la base de datos: " . $e->getMessage()]);
}*/

