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

// Depuración: Verificar qué datos llegan en $_POST
error_log('POST data: ' . print_r($data, true));

$requiredFields = ['name', 'lastName', 'alias', 'birthDate', 'gender', 'sexOrientation', 'email', 'latitude', 'longitude', 'password', 'password2'];

// Depuración: Verificar si hay algún campo faltante en $_POST
foreach ($requiredFields as $field) {
    // Verificamos si el campo está vacío o no existe
    if (!isset($data[$field]) || empty(trim($data[$field]))) {
        // Detallamos cuál es el campo que falta
        error_log("Falta el campo: $field | Valor recibido: " . (isset($data[$field]) ? $data[$field] : 'No recibido'));
        
        // Mostrar un mensaje detallado en el log
        echo json_encode(value: ["success" => false, "message" => "Todos los campos son obligatorios."]);
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

// Depuración: Verificar si el archivo foto está siendo recibido
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["success" => false, "message" => "No se ha recibido ninguna foto o ha ocurrido un error en la carga."]);
    error_log('Error de carga de archivo foto: ' . print_r($_FILES['photo'], true));
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

        // Verificar si el alias ya existe en la BD
    $stmtCheckAlias = $pdo->prepare("SELECT id FROM User WHERE alias = :alias");
    $stmtCheckAlias->bindParam(':alias', $data['alias']);
    $stmtCheckAlias->execute();

    if ($stmtCheckAlias->rowCount() > 0) {
        echo json_encode(["success" => false, "message" => "El alias ya está en uso. Prueba con otro."]);
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
    $hashedPassword = hash("sha256", $data['password']);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':verificationToken', $verificationToken);

    // Ejecutamos la inserción del usuario
    if ($stmt->execute()) {
        // Obtener el ID del usuario recién creado
        $user_id = $pdo->lastInsertId();

        // Configuración de directorio y nombre de archivo para la imagen
        $target_dir = __DIR__ . "/../media/";
        //$target_dir = __DIR__ . "/../media2/";//BORRAR ANTES DE SUBIR--SOLO PARA PRUEBAS
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        // Verificar el tipo de archivo
        $allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($file_info, $_FILES['photo']['tmp_name']);
        finfo_close($file_info);

        // Depuración: Verificar tipo de archivo
        error_log('Tipo de archivo recibido: ' . $file_type);

        if (!in_array($file_type, $allowed_types)) {
            echo "Error: El archivo no es una imagen válida (PNG, JPEG o JPG).";
        } else {
            // Generar un nombre único para la imagen
            $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid() . "." . $file_extension;
            $target_file = $target_dir . $file_name;
            $relative_path = "media/" . $file_name;

            // Mover la imagen al directorio objetivo
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                try {
                    // Insertar la imagen en la tabla Media
                    $query = $pdo->prepare("INSERT INTO Media (user_id, media_path) VALUES (:user_id, :media_path)");
                    $query->execute([
                        ":user_id" => $user_id,
                        ":media_path" => $relative_path
                    ]);
                } catch (PDOException $e) {
                    // Si hay un error, eliminar la imagen subida
                    echo "Error al guardar la imagen en la base de datos: " . $e->getMessage();
                    if (file_exists($target_file)) {
                        unlink($target_file);
                    }
                }
            } else {
                echo "Error: No se pudo mover el archivo de imagen.";
            }
        }
   

   
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

            // Crear enlace con el token. CAMBIAR POR EL SERVIDOR///////////////////////////////////////////////
            //$validationLink = "http://localhost:8080/rsc/verify.php?token=$verificationToken";

            // Crear enlace con el token//para servidor
            $validationLink = "https://tinder3.ieti.site/rsc/verify.php?token=$verificationToken";

        // Crear enlace con el token

     // Contenido del correo con el botón
     $mail->isHTML(true);
     $mail->Subject = 'Validar registro';
     $mail->Body = '
     <html>
     <head>
     </head>
         <body style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; font-family: Arial, sans-serif; margin: 0; padding: 0; background-size: 400% 400%; color: #333;">

         <div style="text-align: center; width: 100%; max-width: 460px; padding: 20px; background-color:rgb(255, 169, 56); border-radius: 10px; box-sizing: border-box;">
             <h2 style="font-family: Arial, sans-serif; font-size: 4rem; color: black;">S<span style="color:rgb(255, 0, 0);">w</span>ipeIt</h2>
             <p style="font-size: 1.2rem; color: black;">¡El <span style="color:rgb(255, 0, 0);">amor</span> está a un swipe!</p>
             
             <p style="color: black;">Hola ' . htmlspecialchars($data['name']) . ',</p>
             <p style="color: black;">Gracias por registrarte en SwipeIt. Para completar el registro y activar tu cuenta, haz clic en el siguiente botón:</p>
             <p><a href="' . $validationLink . '" style="background-color: #4CAF50; color: black; padding: 14px 20px; text-align: center; text-decoration: none; display: inline-block; font-size: 1.2rem; border-radius: 5px;">Validar mi cuenta</a></p>
             <p style="color: black;">Si no has realizado este registro, puedes ignorar este correo.</p>
         </div>
         </body>
     </html>';
    
     // Enviar correo
     $mail->send();
     echo json_encode(["success" => true, "message" => "Se ha enviado un correo de validación que debe ser validado antes de 48H."]);
 } catch (Exception $e) {
     echo json_encode(["success" => true, "message" => "Registro exitoso, pero error al enviar correo: " . $mail->ErrorInfo]);
 }
} else {
 echo json_encode(["success" => false, "message" => "Error al registrar el usuario."]);
}
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error en la base de datos: " . $e->getMessage()]);
}

unset($pdo);
unset($stmt);


?>