<!DOCTYPE html>
<html lang="es">
<head><!--djsl zloc uymy xpry -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDlpD9X2jl0jwfH92yjVCIw2y_ecoVmWRA"></script>
    <script src="./js/jquery-3.7.1.min.js"></script>
    <script src="https://kit.fontawesome.com/74d6337d15.js" crossorigin="anonymous"></script>

    <link type="text/css" rel="stylesheet" href="./css/style.css?t=<?php echo time();?>"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sour+Gummy:wght@400;700&display=swap" rel="stylesheet">   

    <title>SwipeIt! - Registro</title>
</head>

<body id="register">
    <div class="register-container">
        <h2>S<span>w</span>ipeIt</h2>
        <p class="app-description" style="margin-bottom: 0;">¡El <span>amor</span> está a un swipe!</p>

        <form id="registrationForm" method="POST" enctype="multipart/form-data">
        <h3>Introduce tus datos</h3>

            <div class="register-input">
                <label for="name">Nombre:</label>
                <input type="text" id="name" name="name" required />
            </div>

            <div class="register-input">
                <label for="lastName">Apellidos:</label>
                <input type="text" id="lastName" name="lastName" required />
            </div>

            <div class="register-input">
                <label for="alias">Alias:</label>
                <input type="text" id="alias" name="alias" required />
            </div>

            <div class="register-input">
                <label for="birthDate">Nacimiento:</label>
                <input type="date" id="birthDate" name="birthDate" required />
            </div>

            <div class="register-input">
                <label for="sexe">Sexo:</label>
                <select name="gender" id="sexe" required>
                    <option value="hombre">Hombre</option>
                    <option value="mujer">Mujer</option>
                    <option value="no binari">No binaria</option>
                </select>
            </div>

            <div class="register-input">
                <label for="orientation">Orientación sexual:</label>
                <select name="sexOrientation" id="orientation" required>
                    <option value="heterosexual">Heterosexual</option>
                    <option value="homosexual">Homosexual</option>
                    <option value="bisexual">Bisexual</option>
                </select>
            </div>

            <div class="register-input">
                <label for="email">Email:</label>
                <input type="email" id="mail" name="email" required />
            </div>

            <div class="google-maps">
                <div class="register-input">
                    <label for="cityInput">Localización:</label>
                    <!--<input type="text" id="cityInput" placeholder="Ingresa la ciudad" />
                    <button type="button" id="setLocationButton">Ubicación</button> -->
                    <input class="input-cordenadas" id="latitude" name="latitude" value="41.3874">
                    <input class="input-cordenadas" id="longitude" name="longitude" value="2.1686">
                </div>
                <div id="map"></div> 
            </div>

           <!-- <input type="hidden" id="latitude" name="latitude" value="41.3874">
            <input type="hidden" id="longitude" name="longitude" value="2.1686">-->

            <div class="register-input">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required />
            </div>

            <div class="register-input">
                <label for="password2">Verifica la contraseña:</label>
                <input type="password" id="password2" name="password2" required />
            </div>
            <label for="photo">Añade tu foto principal!</label>

            <div class="photo-placeholder">
                <label>
                    <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg" 
                        style="display: none;">
                    <div class="placeholder-content">+</div>
                </label>
            </div>

            <button type="submit" id="buttonSave">Registrarse</button>
        </form>
    </div>

    <script type="text/javascript" src="./js/register.js?t=<?php echo time();?>"></script>
    
</body>
</html>