<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="/js/jquery-3.7.1.min.js"></script>
    <script src="/js/messages.js"></script>

    <link type="text/css" rel="stylesheet" href="/css/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">    

    <title>Mensajes</title>
</head>

<body id="messages">

    <header>
        <h2>S<span>w</span>ipeIt</h2>
        <a href="#"> Buscar </a>
    </header>

    
    <main>

        <!-- cookie: user_id:"n" -->
        <?php
            if (isset($_COOKIE['user_id'])) {
                $cookieValue = $_COOKIE['user_id'];
                echo "The value of myCookie is: " . $cookieValue;
            }
            else{
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

            // Liberar recursos
            unset($pdo);
            unset($query);

        ?>

        <div id="containerMatches">
            <h3>Mis matches</h3>

            <!-- para los que han dado match -->
            <div id="matchedProfiles"></div>

        </div>

        <div id="containerMessages">
            <h3>Mensajes</h3>

            <!-- para los que tienes una conversación -->
            <div id="messagedProfiles"></div>
        </div>
    </main>

    <nav>
        <h3>Descubrir</h3>
        <h3>Mensajes</h3>
        <h3>Perfil</h3>
    </nav>


</body>
</html>