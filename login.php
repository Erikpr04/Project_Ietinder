<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/css" rel="stylesheet" href="./css/style.css" />
    <script src="https://kit.fontawesome.com/74d6337d15.js" crossorigin="anonymous"></script>
    <script src="./js/jquery-3.7.1.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body>
    <div class="login-container">
        <form action="" method="post" autocomplete="off">
            <h2>S<span>w</span>ipeIt</h2>
            <p class="app-description">¡El <span>amor</span> está a un swipe!</p>

            <div class="input-container">

                <div class="data-container email-container">
                    <div class="input-field" id="email" >
                        <input type="text" name="email" maxlength="20" required>
                        <label>Introduce tu correo</label>
                    </div>
                    <p><i class="fa-solid fa-asterisk"></i>Este correo no está registrado</p>
                </div>
                
                <div class="data-container password-container">
                    <div class="input-field" id="password" >
                        <input type="password" name="password" maxlength="20" required>
                        <label>Introduce la contraseña</label>
                        <i class="fa-solid fa-eye"></i>
                    </div>
                    <p><i class="fa-solid fa-asterisk"></i>La contraseña es incorrecta</p>
                </div>
            </div>

            <div class="remember-container">
                <input type="checkbox" name="remember" id="remember"> 
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
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $email = $_POST['email'];
                $password = $_POST['password'];
        
                $connection = new mysqli('localhost', 'client', 'milt0n', 'SwipeItDB');
        
                if ($connection->connect_error) {
                    die("Conexión fallida: " . $connection->connect_error);
                }
        
                $sql = "SELECT * FROM User WHERE email = ? AND account_status = 'active'";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
        
                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    $hashed_password = $user['password'];
        
                    if (hash('sha256', $password) === $hashed_password) {
                        header("Location: discover.php");
                        exit();
                    } else {
                        echo "<script>
                        $('#password').addClass('wrong-data');
                        $('.password-container p').css('display', 'block');
                        </script>";
                    }
                } else {
                    echo "<script>
                    $('#email, #password').addClass('wrong-data');
                    $('.data-container p').css('display', 'block');
                    </script>";
                }
        
                $stmt->close();
                $connection->close();
            }
            ?>
    </div>

    <script>
        $(function() {
            $(".input-field").removeClass("wrong-data");
            $(".data-container p").css("display", "none");
            $("#password i").on("click", function() {
                $(this).toggleClass("fa-eye fa-eye-slash");
                $("#password input").attr("type", function(index, attr) {
                    return attr == "password" ? "text" : "password";
                });
            });
        });
    </script>
    
</body>

</html>