<?php
session_start();
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

$requiredFields = ['name', 'lastName', 'alias', 'birthDate', 'gender', 'sexOrientation', 'email', 'latitude', 'longitude', 'password', 'password2','photo'];
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["success" => false, "message" => "No se ha recibido ninguna foto o ha ocurrido un error en la carga."]);
    exit();
}

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

    // Insertar usuario en la base de datos
// Inserción del usuario
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
    $target_dir = __DIR__ . "/media/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // Verificar el tipo de archivo
    $allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $file_type = finfo_file($file_info, $_FILES['photo']['tmp_name']);
    finfo_close($file_info);

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
} 


    if ($query->execute()) {
        // Crear enlace con el token
        $validationLink = "https://tinder3.ieti.site/rsc/verify.php?token=$verificationToken";

        // Contenido del correo
        $subject = 'Validar registro';
        $message = '
        <html>
        <head>
        <title>Validación de cuenta</title>
        </head>
        <body style="text-align: center; font-family: Arial, sans-serif;">
            <h2 style="color: black;">S<span style="color: red;">w</span>ipeIt</h2>
            <p style="color: black;">¡El <span style="color: red;">amor</span> está a un swipe!</p>
            <p style="color: black;">Hola ' . htmlspecialchars($data['name']) . ',</p>
            <p style="color: black;">Gracias por registrarte en SwipeIt. Para completar el registro y activar tu cuenta, haz clic en el siguiente botón:</p>
            <p><a href="' . $validationLink . '" style="background-color: #4CAF50; color: white; padding: 14px 20px; text-decoration: none; display: inline-block; font-size: 1.2rem; border-radius: 5px;">Validar mi cuenta</a></p>
            <p style="color: black;">Si no has realizado este registro, puedes ignorar este correo.</p>
        </body>
        </html>';

        // Cabeceras para enviar correo en formato HTML
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: SwipeIt <swipeit@iesesteveterradas.cat>" . "\r\n";

        // Enviar correo con mail()
        if (mail($data['email'], $subject, $message, $headers)) {
            echo json_encode(["success" => true, "message" => "Se ha enviado un correo de validación que debe ser validado antes de 48H."]);
            
        } else {
            echo json_encode(["success" => false, "message" => "Registro exitoso, pero error al enviar correo."]);
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