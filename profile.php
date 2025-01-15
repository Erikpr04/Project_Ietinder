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
                echo "The value of myCookie is: " . $cookieValue;
            }
            else{
                echo"cookie hardcodeada <br/>";
                $cookieValue = "1";
            }

            // configuración de la base de datos
            $host = "localhost:3306";
            $dbname = "SwipeITDB";
            $username = "admin";  //!!! cambiar nombre
            $password = "admin";

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
            <legend>Datos personales</legend> <br/><br/>
    
            <label for="name">Nombre:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($result['name']); ?>" /> <br/><br/>

            <label for="lastName">Apellidos:</label>
            <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($result['last_name']); ?>" /> <br/><br/>

            <label for="alias">Alias:</label>
            <input type="text" id="alias" name="alias" value="<?php echo htmlspecialchars($result['alias']); ?>" /> <br/><br/>

            <label for="birthDate">Fecha de cumpleaños:</label>
            <input type="date" id="birthDate" name="birthDate" value="<?php echo htmlspecialchars($result['birth_date']); ?>" /> <br/><br/>

            <label for="location">Localización:</label> <br/><br/>
            <div id="map"></div>
            <script>
                // pasar las coodenadas del php al js
                let coordinates = {
                    lat: <?php echo htmlspecialchars($result['latitude']); ?>,
                    lng: <?php echo htmlspecialchars($result['longitude']); ?>
                };
            </script>
            <br/><br/>
            
            <label for="sexe">Sexo:</label>
            <input type="radio" name="gender" id="male" value="hombre" <?php echo ($result['sex'] === 'hombre') ? 'checked' : ''; ?>>
            <label for="male">Hombre</label>
            <input type="radio" name="gender" id="women" value="mujer" <?php echo ($result['sex'] === 'mujer') ? 'checked' : ''; ?>>
            <label for="women">Mujer</label>
            <input type="radio" name="gender" id="nonBinary" value="no binari" <?php echo ($result['sex'] === 'no binari') ? 'checked' : ''; ?>>
            <label for="nonBinary">No binaria</label> <br/><br/>

            <label for="sexe">Orientación sexual:</label>
            <input type="radio" name="sexOrientation" id="heterosexual" value="heterosexual" <?php echo ($result['sexual_orientation'] === 'heterosexual') ? 'checked' : ''; ?>>
            <label for="heterosexual">Heterosexual</label>
            <input type="radio" name="sexOrientation" id="homosexual" value="homosexual" <?php echo ($result['sexual_orientation'] === 'homosexual') ? 'checked' : ''; ?>>
            <label for="homosexual">Homosexual</label>
            <input type="radio" name="sexOrientation" id="bisexual" value="bisexual" <?php echo ($result['sexual_orientation'] === 'bisexual') ? 'checked' : ''; ?>>
            <label for="bisexual">Bisexual</label> <br/><br/>

            <a href="#">Cambiar fotos</a> <br/><br/>

            <label for="mail">Email:</label>
            <input type="email" id="mail" name="mail" value="<?php echo htmlspecialchars($result['email']); ?>" disabled style="cursor:not-allowed"> <br/><br/>

            <!-- <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" disabled style="cursor:not-allowed"> <br/><br/> -->

            <input type="submit" id="buttonSave" value="Guardar Cambios"/>

        </form>
    </main>
    
</body>

</html>