<script src="./js/utils.js"></script>

<?php
if (isset($_COOKIE['user_id'])) {
    echo "<script>sendLog('User {$_COOKIE['user_id']} tried to access into Login.php with an active session, redirecting him to discover.php').then(() => { window.location.href = 'discover.php'; });</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/css" rel="stylesheet" href="./css/style.css?t=<?php echo time();?>"/>
    <script src="https://kit.fontawesome.com/74d6337d15.js" crossorigin="anonymous"></script>
    <script src="./js/jquery-3.7.1.min.js"></script>
    <script src="./js/login.js"></script>
    <script src="./js/utils.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body id="login">
    <div class="login-container">
        <form action="" method="post" autocomplete="off">
            <h2>S<span>w</span>ipeIt</h2>
            <p class="app-description">¡El <span>amor</span> está a un swipe!</p>

            <div class="input-container">

                <div class="data-container email-container">
                    <div class="input-field" id="email">
                        <input type="text" name="email" maxlength="40" required>
                        <label>Introduce tu correo</label>
                    </div>
                    <p><i class="fa-solid fa-asterisk"></i>Este correo no está registrado</p>
                </div>

                <div class="data-container password-container">
                    <div class="input-field" id="password">
                        <input type="password" name="password" maxlength="20" required>
                        <label>Introduce la contraseña</label>
                        <i class="fa-solid fa-eye"></i>
                    </div>
                    <p><i class="fa-solid fa-asterisk"></i>La contraseña es incorrecta</p>
                </div>

            </div>

            <div class="remember-container">
                <input type="checkbox" name="remember" id="remember" checked>
                <label for="remember">Mantener sesión iniciada</label>
            </div>

            <!-- Submit button -->
            <button type="submit">Iniciar sesión</button>

            <!-- Forgot password and create account links -->
            <div class="forget-createAccount">
                <a href="#">¿Has olvidado la contraseña?</a>
                <a href="#">Crea una cuenta nueva</a>
            </div>
        </form>
        <?php

        ?>
        <?php

        // Inicializar variables
        $email = null;
        $password = null;

        // Manejo del formulario POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar que las variables existan antes de usarlas
            $email = isset($_POST['email']) ? trim($_POST['email']) : null;
            $password = isset($_POST['password']) ? trim($_POST['password']) : null;
            $remember = isset($_POST['remember']); // Checkbox para mantener la sesión
        
            if ($email && $password) {
                // Conexión a la base de datos
                $connection = new mysqli('localhost', 'client', 'milt0n', 'SwipeITDB');

                if ($connection->connect_error) {
                    die("Conexión fallida: " . $connection->connect_error);
                }

                // Consultar el usuario por email
                $sql = "SELECT id, password FROM User WHERE email = ? AND account_status = 'active'";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    $hashed_password = $user['password'];
                    $user_id = $user['id'];

                    // Verificar la contraseña
                    echo hash('sha256', $password);
                    if (hash('sha256', $password) === $hashed_password) {
                        // Guardar cookie si se marca "recordar sesión"
                        if ($remember) {
                            setcookie("user_id", $user_id, time() + (30 * 24 * 60 * 60), "/"); // 30 días
                            echo "<script>sendLog('User $user_id logged in, redirecting him to discover.php from login.php.').then(() => { window.location.href = 'discover.php'; });</script>";
                        } else {
                            echo "<script>sendLog('User logged in, redirecting him to discover.php from login.php without session.').then(() => { window.location.href = 'discover.php'; });</script>";
                        }
                        exit();
                    } else {
                        echo "<script>
                            document.querySelector('#password').classList.add('wrong-data');
                            document.querySelector('.password-container p').style.display = 'block';
                            // Error de contraseña incorrecta
                            sendLog('Error de login: contraseña incorrecta para email $email');
                        </script>";
                    }
                } else {
                    echo "<script>
                        document.querySelector('#email').classList.add('wrong-data');
                        document.querySelector('#password').classList.add('wrong-data');
                        document.querySelectorAll('.data-container p').forEach(el => el.style.display = 'block');
                        // Error de email no registrado
                        sendLog('Error de login: email no registrado $email');
                    </script>";
                }

                $stmt->close();
                $connection->close();
            }
        }
        ?>

    </div>

</body>

</html>