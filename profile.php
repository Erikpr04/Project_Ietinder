<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDlpD9X2jl0jwfH92yjVCIw2y_ecoVmWRA"></script>
    <script src="/js/jquery-3.7.1.min.js"></script>
    <script src="/js/profile.js"></script>

    <link type="text/css" rel="stylesheet" href="/css/style.css" />
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">   

    <title>Tu perfil</title>

</head>

<body id="profile">

    <main>
        <!-- cookie: user_id:"n" -->
        <?php
            if (isset($_COOKIE['user_id'])) {
                $cookieValue = $_COOKIE['user_id'];
            }

            // configuración de la base de datos
            $host = "localhost:3306";
            $dbname = "SwipeITDB";
            $username = "client";
            $password = "milt0n";

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

            if($result) var_dump($result);

            // Liberar recursos
            unset($pdo);
            unset($query);

        ?>
        
        <form method="post">
            <h3>Datos personales</h3> 

            <div class="profile-personalData">
                <div class="profile-input">
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($result['name']); ?>" />
                    <label for="name">Nombre:</label>
                </div>

                <div class="profile-input">
                    <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($result['last_name']); ?>" />
                    <label for="lastName">Apellidos:</label>
                </div>
            </div>

            <div class="profile-personalData">
                <div class="profile-input">
                    <input type="text" id="alias" name="alias" value="<?php echo htmlspecialchars($result['alias']); ?>" />
                    <label for="alias">Alias:</label>
                </div>

                <div class="profile-input">
                    <input type="date" id="birthDate" name="birthDate" value="<?php echo htmlspecialchars($result['birth_date']); ?>" />
                    <label for="birthDate">Nacimiento:</label>
                </div>
            </div>
            
            <div class="profile-personalData">
            <!-- Sexo -->
                <div class="profile-input">
                    <select name="gender" id="sexe">
                        <option value="hombre" <?php echo ($result['sex'] === 'hombre') ? 'selected' : ''; ?>>Hombre</option>
                        <option value="mujer" <?php echo ($result['sex'] === 'mujer') ? 'selected' : ''; ?>>Mujer</option>
                        <option value="no binari" <?php echo ($result['sex'] === 'no binari') ? 'selected' : ''; ?>>No binaria</option>
                    </select>
                    <label for="sexe">Sexo:</label>
                </div>

                <!-- Orientación Sexual -->
                <div class="profile-input">
                    <select name="sexOrientation" id="orientation">
                        <option value="heterosexual" <?php echo ($result['sexual_orientation'] === 'heterosexual') ? 'selected' : ''; ?>>Heterosexual</option>
                        <option value="homosexual" <?php echo ($result['sexual_orientation'] === 'homosexual') ? 'selected' : ''; ?>>Homosexual</option>
                        <option value="bisexual" <?php echo ($result['sexual_orientation'] === 'bisexual') ? 'selected' : ''; ?>>Bisexual</option>
                    </select>
                    <label for="orientation">Orientación sexual:</label>
                </div>
            </div>

            <div class="profile-input">
                <input type="email" id="mail" name="mail" value="<?php echo htmlspecialchars($result['email']); ?>" disabled style="cursor:not-allowed"> 
                <label for="mail">Email:</label>
            </div>

            <div class="google-maps">
                <label for="location">Localización:</label> 
                <div id="map"></div>
                <script>
                    // pasar las coodenadas del php al js
                    let coordinates = {
                        lat: <?php echo htmlspecialchars($result['latitude']); ?>,
                        lng: <?php echo htmlspecialchars($result['longitude']); ?>
                    };
                </script>
            </div>

            <a href="#">Cambiar fotos</a> 

            <button type="submit" id="buttonSave">Guardar Cambios</button>

        </form>
    </main>
    
</body>

</html>