<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/css" rel="stylesheet" href="./css/style.css?t=<?php echo time();?>"/>
    <script src="https://kit.fontawesome.com/74d6337d15.js" crossorigin="anonymous"></script>
    <script src="./js/jquery-3.7.1.min.js"></script>
    <script src="./js/forgot_password.js"></script>
    <script src="./js/utils.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <title>SwipeIt! - Forgot Password</title>
</head>

<body id="login">

    <div class="login-container">
        <form action="" method="post" autocomplete="off">
            <h2>S<span>w</span>ipeIt</h2>
            <p class="app-description">¡El <span>amor</span> está a un swipe!</p>

            <div class="input-container">
                <div class="forgot-instrucciones">
                    <p class="forgot-instrucciones-title">¿Has olvidado la contraseña?</p>
                    <p class="forgot-instrucciones-text">Rellena este formulario para recuperar tu contraseña y recivirás un mail con instrucciones.</p>
                </div>

                <div class="data-container email-container">
                    <div class="input-field" id="email">
                        <input type="text" name="email" maxlength="100" required>
                        <label>Introduce tu correo</label>
                    </div>
                    <p><i class="fa-solid fa-asterisk"></i>Este correo no está registrado</p>
                </div>

                <div class="data-container password-container">
                    <div class="input-field" id="nombre">
                        <input type="text" name="nombre" maxlength="20" required>
                        <label>Introduce tu nombre</label>
                        <i style="display: none;" class="fa-solid fa-eye"></i>
                    </div>
                    <p><i class="fa-solid fa-asterisk"></i>Este nombre no es correcto</p>
                </div>
                
                <div class="data-container password-container">
                    <div class="input-field" id="Apellidos">
                        <input type="text" name="apellidos" maxlength="20" required>
                        <label>Introduce tus apellidos</label>
                        <i style="display: none;" class="fa-solid fa-eye"></i>
                    </div>
                    <p><i class="fa-solid fa-asterisk"></i>Este apellido no es correcto</p>
                </div>
            </div>

            <div class="remember-container">
                <input type="checkbox" name="remember" id="remember" checked>
                <label for="remember">Mantener sesión iniciada</label>
            </div>

            <!-- Submit button -->
            <button type="submit" id="submit-button">Recuperar contraseña</button>
        </form> <!-- Aquí se cierra el formulario -->

    </div>

    <!-- Cargando el archivo JS para la funcionalidad -->
    <script type="text/javascript" src="./js/forgot_password.js?t=<?php echo time();?>"></script>
    
</body>

</html>
