<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode($_POST['data'], true);

    // configuración de la base de datos
    $host = "localhost:3306";
    $dbname = "SwipeITDB";
    $username = "client";
    $password = "milt0n";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error al conectar a la base de datos: ' . $e->getMessage()
        ]);
        exit;
        die("Error al conectar a la base de datos: " . $e->getMessage());
    }

    $query = $pdo->prepare("UPDATE User
                            SET name=:name, last_name=:lastName, alias=:alias, birth_date=:birthDate, latitude=:latitude, 
                            longitude=:longitude, sex=:sex, sexual_orientation=:sexOrientation
                            WHERE id=:id");

    $query->bindParam(":name", $data['name']);
    $query->bindParam(":lastName", $data['lastName']);
    $query->bindParam(":alias", $data['alias']);
    $query->bindParam(":birthDate", $data['birthDate']);
    $query->bindParam(":latitude", $data['latitude']);
    $query->bindParam(":longitude", $data['longitude']);
    $query->bindParam(":sex", $data['gender']);
    $query->bindParam(":sexOrientation", $data['sexOrientation']);

    if (isset($_COOKIE['user_id'])) {
        $cookieValue = $_COOKIE['user_id'];
        $query->bindParam(":id", $cookieValue);
    } else {
        $cookieValue = "1";
        $query->bindParam(":id", $cookieValue);
    }

    try {
        if ($query->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Datos actualizados correctamente'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error al actualizar los datos'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error al ejecutar la consulta: ' . $e->getMessage()
        ]);
    }

    unset($pdo);
    unset($query);
}