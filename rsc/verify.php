<?php
require_once 'db_config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verificar si el token existe y obtener la fecha de creación
        $stmt = $pdo->prepare("SELECT id, created_at FROM User WHERE verification_token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Token válido, obtener la fecha de creación
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $created_at = new DateTime($user['created_at']);
            $now = new DateTime();

            // Calcular la diferencia entre la fecha de creación y la fecha actual
            $interval = $created_at->diff($now);

            // Si la diferencia es mayor que 48 horas, borrar al usuario
            if ($interval->days > 2 || ($interval->days == 2 && $interval->h > 0)) {
                // Borrar el usuario si han pasado más de 48 horas
                $stmtDelete = $pdo->prepare("DELETE FROM User WHERE verification_token = :token");
                $stmtDelete->bindParam(':token', $token);
                $stmtDelete->execute();
                echo "El plazo para validar la cuenta ha expirado. El usuario ha sido eliminado.";
            } else {
                // Si está dentro del plazo, activar la cuenta
                $stmtUpdate = $pdo->prepare("UPDATE User SET account_status = 'active', verification_token = NULL WHERE verification_token = :token");
                $stmtUpdate->bindParam(':token', $token);
                $stmtUpdate->execute();
                echo "Cuenta activada exitosamente.";
            }
        } else {
            echo "Token inválido.";
        }
    } catch (PDOException $e) {
        echo "Error en la base de datos: " . $e->getMessage();
    }
} else {
    echo "Token no proporcionado.";
}
?>
