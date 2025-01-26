<?php

require_once 'db_config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        // Validar que el token aún existe
        $stmt = $pdo->prepare("SELECT id FROM User WHERE verification_token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(["valid" => false, "message" => "Token inválido"]);
            exit();
        }

        // Hashear la nueva contraseña con SHA-256
        $hashedPassword = hash("sha256", $password);

        // Actualizar contraseña y eliminar token solo si la actualización es exitosa
        $updateStmt = $pdo->prepare("UPDATE User SET password = :password, account_status = 'active', verification_token = NULL WHERE verification_token = :token");
        $updateStmt->bindParam(':password', $hashedPassword);
        $updateStmt->bindParam(':token', $token);
        $updateStmt->execute();

        // Verificar si realmente se actualizó la contraseña
        if ($updateStmt->rowCount() > 0) {
            echo json_encode(["valid" => true, "message" => "Contraseña restablecida y cuenta activada correctamente"]);
            header("Location: /login.php");
            exit();
        } else {
            echo json_encode(["valid" => false, "message" => "Error: No se pudo actualizar la contraseña"]);
        }
        exit();



    } catch (PDOException $e) {
        echo "Error en la base de datos: " . $e->getMessage();
    }
} else {
    echo "Token no proporcionado.";
}


?>
