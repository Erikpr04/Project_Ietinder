<?php
    require_once './rsc/log.php';

    if (isset($_COOKIE['user_id'])) {

        require_once './rsc/db_config.php';

        // Obtener las variables de entorno necesarias con valores por defecto
        $host = getenv('DB_HOST');
        $dbname = getenv('DB_NAME') ;
        $username = getenv('DB_USERNAME');
        $pass = getenv('DB_PASSWORD') ;

        $connection = new mysqli($host, $username, $pass, $dbname);

        if ($connection->connect_error) {
            die("Conexión fallida: " . $connection->connect_error);
        }

        $sql = "INSERT INTO User_logs (user_id) VALUES (?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $_COOKIE['user_id']);
        $stmt->execute();

        createLog(action: "Usuario con id: " . $_COOKIE['user_id'] . " logged, redirigiendo a discover desde index");
        header('Location: ./discover.php');
    }
    else {
        createLog(action: "Usuario no loggeado, redirigiendo a logging desde index");
        header('Location: ./login.php');
    }