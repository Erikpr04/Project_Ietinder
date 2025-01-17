<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDlpD9X2jl0jwfH92yjVCIw2y_ecoVmWRA"></script>
    <script src="./js/jquery-3.7.1.min.js"></script>
    <script src="./js/profile.js"></script>
    <script src="./js/utils.js"></script>
    <script src="https://kit.fontawesome.com/74d6337d15.js" crossorigin="anonymous"></script>

    <link type="text/css" rel="stylesheet" href="./css/style.css?t=<?php echo time();?>"/>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">   

    <title>SwipeIt! - Your Profile</title>

</head>

<body id="register">

    <div class="register-container">
    <h2>S<span>w</span>ipeIt</h2>
    <p class="app-description">¡El <span>amor</span> está a un swipe!</p>
        <!-- cookie: user_id:"n" -->
        <?php
            require_once './rsc/log.php';
            require_once './rsc/db_config.php';

            // Obtener las variables de entorno necesarias con valores por defecto
            $host = getenv('DB_HOST') ;
            $dbname = getenv('DB_NAME') ;
            $username = getenv('DB_USERNAME') ;
            $password = getenv('DB_PASSWORD');



           // if (isset($_COOKIE['user_id'])) {
            //    createLog(action: "Usuario entrado a profile.php con id: " . $_COOKIE['user_id']);
           // }
           // else {
             //   createLog(action: "Usuario no tiene cookie, redirigiendo de profile.php a login.php");
    
               // header('Location: ./login.php');
           // }

            // recoger cookie
            if (isset($_COOKIE['user_id'])) {
                $cookieValue = $_COOKIE['user_id'];
            }

            // Conexión a la base de datos
            try {
                $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Error al conectar a la base de datos: " . $e->getMessage());
            }

            // Ejecutar consulta 
            $query = $pdo->prepare("SELECT name, last_name,alias,birth_date,latitude,longitude,sex,sexual_orientation,email from User where id=:id;");
            $query->bindParam(":id", $cookieValue);
            $query->execute();
            $result = $query->fetch();

            // Liberar recursos
            unset($pdo);
            unset($query);

        ?>
            <form method="post">
                <h3>Introduce tus datos</h3> 
                <div class="register-personalData">
                    <div class="register-input">
                    <label for="name">Nombre:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($result['name']); ?>" />
                        
                    </div>

                    <div class="register-input">
                    <label for="lastName">Apellidos:</label>

                        <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($result['last_name']); ?>" />
                    </div>
                </div>

                <div class="register-personalData">
                    <div class="register-input">
                    <label for="alias">Alias:</label>
                        <input type="text" id="alias" name="alias" value="<?php echo htmlspecialchars($result['alias']); ?>" />
                        
                    </div>

                    <div class="register-input">
                    <label for="birthDate">Nacimiento:</label>
                        <input type="date" id="birthDate" name="birthDate" value="<?php echo htmlspecialchars($result['birth_date']); ?>" />
                        
                    </div>
                </div>
                
                <div class="register-personalData">
                <!-- Sexo -->
                    <div class="register-input">
                    <label for="sexe">Sexo:</label>
                        <select name="gender" id="sexe">
                            <option value="hombre" <?php echo ($result['sex'] === 'hombre') ? 'selected' : ''; ?>>Hombre</option>
                            <option value="mujer" <?php echo ($result['sex'] === 'mujer') ? 'selected' : ''; ?>>Mujer</option>
                            <option value="no binari" <?php echo ($result['sex'] === 'no binari') ? 'selected' : ''; ?>>No binaria</option>
                        </select>
                        
                    </div>

                    <!-- Orientación Sexual -->
                    <div class="register-input">
                    <label for="orientation">Orientación sexual:</label>
                        <select name="sexOrientation" id="orientation">
                            <option value="heterosexual" <?php echo ($result['sexual_orientation'] === 'heterosexual') ? 'selected' : ''; ?>>Heterosexual</option>
                            <option value="homosexual" <?php echo ($result['sexual_orientation'] === 'homosexual') ? 'selected' : ''; ?>>Homosexual</option>
                            <option value="bisexual" <?php echo ($result['sexual_orientation'] === 'bisexual') ? 'selected' : ''; ?>>Bisexual</option>
                        </select>
                       
                    </div>
                </div>

                <div class="register-input">
                <label for="mail">Email:</label>
                    <input type="email" id="mail" name="mail" value="<?php echo htmlspecialchars($result['email']); ?>" disabled style="cursor:not-allowed"> 
                    
                </div>

                <div class="google-maps">
                    <label for="location">Localización:</label> 
                    <div id="map"></div>
                    <script>
                        // Pasar las coordenadas del PHP a JavaScript
                        let coordinates = {
                            lat: <?php echo isset($result['latitude']) ? htmlspecialchars($result['latitude']) : '0'; ?>,
                            lng: <?php echo isset($result['longitude']) ? htmlspecialchars($result['longitude']) : '0'; ?>
                        };

                        console.log("Coordenadas cargadas:", coordinates); // Para depuración
                    </script>

                </div>
                <!--pass y comprobacion-->
                <div class="register-personalData">
                    <div class="register-input">
                    <label for="password">Password:</label>
                        <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($result['password']); ?>" />
                        
                    </div>

                    <div class="register-input">
                    <label for="password2">Verifica el password:</label>
                        <input type="password2" id="password2" name="password2" value="<?php echo htmlspecialchars($result['password2']); ?>" />
                       
                    </div>
                </div>

                <!--<a href="#">Cambiar fotos</a> -->

                <button type="submit" id="buttonSave">Registrarse</button>

            </form>
            
            <script>
                const buttonSave = document.getElementById('buttonSave');
                buttonSave.addEventListener('click', () => {
                    createErrorTag("info", "Cambios guardados");
                });
            </script>
    </div>
    
</body>

</html>