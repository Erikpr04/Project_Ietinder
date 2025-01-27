<?php

require_once 'db_config.php';

$token = $_GET['token'] ?? '';

if (!$token) {
    die("Token no proporcionado.");
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Validar si el token es correcto
    $stmt = $pdo->prepare("SELECT id FROM User WHERE verification_token = :token");
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Token inválido o expirado.");
    }

    // Si el formulario fue enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';

        // Expresión regular para validar la contraseña
        $passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';

        // Validaciones
        if (empty($password) || empty($password2)) {
            $error = "Ambos campos son obligatorios.";
        } elseif ($password !== $password2) {
            $error = "Las contraseñas no coinciden.";
        } elseif (!preg_match($passwordRegex, $password)) {
            $error = "La contraseña debe tener al menos 8 caracteres y contener al menos un número, una minúscula y una mayúscula.";
        } else {
            // Hashear la nueva contraseña
            $hashedPassword = hash("sha256", $data['password']);

            // Actualizar la contraseña en la base de datos y eliminar el token
            $updateStmt = $pdo->prepare("UPDATE User SET password = :password, verification_token = NULL WHERE id = :id");
            $updateStmt->bindParam(':password', $hashedPassword);
            $updateStmt->bindParam(':id', $user['id']);
            $updateStmt->execute();

            // Verificar si la actualización fue exitosa
            if ($updateStmt->rowCount() > 0) {
                $successMessage = "Contraseña actualizada con éxito.";  // Mensaje de éxito
               
            } else {
                $error = "Error al actualizar la contraseña.";
            }
        }
    }
} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/css" rel="stylesheet" href="../css/style.css?t=<?php echo time();?>"/>
    <script src="https://kit.fontawesome.com/74d6337d15.js" crossorigin="anonymous"></script>
    <script src="./js/jquery-3.7.1.min.js"></script>
    <script src="./js/utils.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <title>SwipeIt! - Recuperar Contraseña</title>
</head>

<body id="login">

    <div class="login-container">
        <form action="" method="post" autocomplete="off">
            <h2>S<span>w</span>ipeIt</h2>
            <p class="app-description">¡El <span>amor</span> está a un swipe!</p>

            <div class="input-container">
                <div class="forgot-instrucciones">
                    <p class="forgot-instrucciones-title">Recuperar contraseña</p>
                    <p class="forgot-instrucciones-text">Introduce una nueva contraseña para tu cuenta.</p>
                </div>

                <?php if (!empty($error)) : ?>
                    <p style="color: red; text-align: center;"><?php echo $error; ?></p>
                    <?php elseif (!empty($successMessage)) : ?>
                    <p style="color: blue; text-align: center;"><?php echo $successMessage; ?></p>
                    <script>
                        // Redirigir después de 2 segundos
                        setTimeout(function() {
                            window.location.href = "/login.php";
                        }, 2000);
                    </script>
                <?php endif; ?>

                <div class="data-container password-container">
                    <div class="input-field" id="password-recuperar">
                        <input type="password" name="password" maxlength="20" required>
                        <label>Introduce la nueva contraseña</label>
                        <i style="display: none;" class="fa-solid fa-eye"></i>
                    </div>
                </div>

                <div class="data-container password-container">
                    <div class="input-field" id="password2-recuperar">
                        <input type="password" name="password2" maxlength="20" required>
                        <label>Vuelva a introducir la contraseña</label>
                        <i style="display: none;" class="fa-solid fa-eye"></i>
                    </div>
                </div>

                <button type="submit" id="submit-button">Recuperar contraseña</button>
            </div>
        </form>
    </div>

    

</body>

</html>
