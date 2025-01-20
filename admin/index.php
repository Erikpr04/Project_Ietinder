<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
</head>
<body>
    <h1>Insertar Perfiles desde JSON a la Base de Datos</h1>
    <form method="POST" enctype="multipart/form-data">
        <label for="jsonFile">Selecciona un archivo JSON:</label>
        <input type="file" name="jsonFile" id="jsonFile" accept=".json" required>
        <button type="submit">Cargar Perfiles</button>
    </form>
</body>
</html>


<?php
include_once "../rsc/db_config.php";

$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME') ;
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD') ;

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}

// Verificar si se ha enviado el archivo JSON
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["jsonFile"])) {
    $file = $_FILES["jsonFile"]["tmp_name"];

    if (file_exists($file)) {
        $jsonData = file_get_contents($file);
        $profiles = json_decode($jsonData, true);
        $stmtUser = $pdo->prepare("DELETE FROM User;")->execute();

        //CAMBIAR
        $stmtUser = $pdo->prepare("ALTER TABLE User AUTO_INCREMENT = 1;")->execute();
        $stmtUser = $pdo->prepare("ALTER TABLE Interaction AUTO_INCREMENT = 1;")->execute();
        $stmtUser = $pdo->prepare("ALTER TABLE Media AUTO_INCREMENT = 1;")->execute();
        $stmtUser = $pdo->prepare("ALTER TABLE Message AUTO_INCREMENT = 1;")->execute();
        $stmtUser = $pdo->prepare("ALTER TABLE Conversation AUTO_INCREMENT = 1;")->execute();
        $stmtUser = $pdo->prepare("ALTER TABLE User_logs AUTO_INCREMENT = 1;")->execute();




        if (json_last_error() === JSON_ERROR_NONE) {
            $insertUserQuery = "
                INSERT INTO User (name, last_name, alias,  latitude, longitude, sex, sexual_orientation, birth_date, email, password)
                VALUES (:name, :last_name, :alias, :latitude, :longitude, :sex, :sexual_orientation, :birth_date, :email, :password)
            ";

            $insertMediaQuery = "
                INSERT INTO Media (user_id, media_path)
                VALUES (:user_id, :media_path)
            ";

            $inserted = 0;
            $errors = 0;

            foreach ($profiles as $profile) {
                try {
                    // Iniciar una transacción para asegurar atomicidad
                    $pdo->beginTransaction();

                    // Insertar datos del usuario
                    $stmtUser = $pdo->prepare($insertUserQuery);
                    $stmtUser->execute([
                        ':name' => $profile['name'],
                        ':last_name' => $profile['last_name'],
                        ':alias' => $profile['alias'],
                        ':latitude' => $profile['location']['latitude'],
                        ':longitude' => $profile['location']['longitude'],
                        ':sex' => $profile['sex'],
                        ':sexual_orientation' => $profile['sexualOrientation'],
                        ':birth_date' => $profile['birthdate'],
                        ':email' => $profile['email'],
                        ':password' => hash('sha256', $profile['password']), // Encriptar contraseña
                    ]);

                    // Obtener el ID del usuario insertado
                    $userId = $pdo->lastInsertId();

                    // Insertar datos de media
                    $stmtMedia = $pdo->prepare($insertMediaQuery);
                    $stmtMedia->execute([
                        ':user_id' => $userId,
                        ':media_path' => "media/".$profile['picture'],
                        
                    ]);
                    $stmtMedia->execute([
                        ':user_id' => $userId,
                        ':media_path' => "media/".$profile['picture2'],
                        
                    ]);

                    // Confirmar la transacción
                    $pdo->commit();
                    $inserted++;
                } catch (PDOException $e) {
                    echo $e;
                    // Revertir la transacción en caso de error
                    $pdo->rollBack();
                    $errors++;
                }
            }

            echo "<p>Perfiles insertados: $inserted</p>";
            echo "<p>Errores: $errors</p>";

            // Mostrar todos los usuarios insertados
            $selectQuery = "SELECT * FROM User";
            $stmt = $pdo->query($selectQuery);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "<h2>Usuarios en la base de datos:</h2>";
            echo "<table border='1'>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Email</th>
                </tr>";
            foreach ($users as $user) {
                echo "<tr>
                    <td>{$user['id']}</td>
                    <td>{$user['name']}</td>
                    <td>{$user['last_name']}</td>
                    <td>{$user['email']}</td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Error al decodificar el archivo JSON.</p>";
        }
    } else {
        echo "<p>No se encontró el archivo.</p>";
    }
}
?>