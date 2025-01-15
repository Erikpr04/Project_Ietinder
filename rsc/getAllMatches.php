<?php
    // !!! cookie: user_id:"n"
    if (isset($_COOKIE['user_id'])) {
        $cookieValue = $_COOKIE['user_id'];
        echo "The value of myCookie is: " . $cookieValue;
    } else {
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
    $query = $pdo->prepare("SELECT c.id as idConversation, c.user1_id, c.user2_id, c.started, u.name, 
                            GROUP_CONCAT(m.media_path SEPARATOR ',') as media_paths
                            from Conversation c 
                            join User u on c.user1_id=u.id or c.user2_id=u.id
                            join Media m on u.id = m.user_id
                            where :id in (c.user1_id, c.user2_id)
                            group by c.id, c.user1_id, c.user2_id, c.started, u.name;
                        ");
    $query->bindParam(":id", $cookieValue);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    // if ($results) {
    //     foreach ($results as $row) {
    //         echo "idConversation: " . htmlspecialchars($row['idConversation']) . "<br>";
    //         echo "User1 ID: " . htmlspecialchars($row['user1_id']) . "<br>";
    //         echo "User2 ID: " . htmlspecialchars($row['user2_id']) . "<br>";
    //         echo "Started: " . htmlspecialchars($row['started']) . "<br>";
    //         echo "Name: " . htmlspecialchars($row['name']) . "<br>";
    //         echo "Media Paths: " . htmlspecialchars($row['media_paths']) . "<br><br>";
    //     }
    // } else {
    //     echo "No hay resultados";
    // }

    // Liberar recursos
    unset($pdo);
    unset($query);

    echo json_encode($results);