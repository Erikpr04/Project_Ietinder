
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar Perfiles</title>
</head>
<body>
    <h1>Insertar Perfiles desde JSON a la Base de Datos</h1>
    <form method="POST" action="process.php" enctype="multipart/form-data">
        <label for="jsonFile">Selecciona un archivo JSON:</label>
        <input type="file" name="jsonFile" id="jsonFile" accept=".json" required>
        <button type="submit">Cargar Perfiles</button>
    </form>
</body>
</html>


<?php
// Configuración de la base de datos
$host = "localhost";
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

// Verificar si se ha enviado el archivo JSON
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["jsonFile"])) {
    $file = $_FILES["jsonFile"]["tmp_name"];

    if (file_exists($file)) {
        $jsonData = file_get_contents($file);
        $profiles = json_decode($jsonData, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            $insertQuery = "
                INSERT INTO users (name, last_name, alias, city, latitude, longitude, sex, sexualOrientation, picture, picture2, birthdate, email, password)
                VALUES (:name, :last_name, :alias, :city, :latitude, :longitude, :sex, :sexualOrientation, :picture, :picture2, :birthdate, :email, :password)
            ";

            $inserted = 0;
            $errors = 0;

            foreach ($profiles as $profile) {
                try {
                    $stmt = $pdo->prepare($insertQuery);
                    $stmt->execute([
                        ':name' => $profile['name'],
                        ':last_name' => $profile['last_name'],
                        ':alias' => $profile['alias'],
                        ':city' => $profile['location']['city'],
                        ':latitude' => $profile['location']['latitude'],
                        ':longitude' => $profile['location']['longitude'],
                        ':sex' => $profile['sex'],
                        ':sexualOrientation' => $profile['sexualOrientation'],
                        ':picture' => $profile['picture'],
                        ':picture2' => $profile['picture2'],
                        ':birthdate' => $profile['birthdate'],
                        ':email' => $profile['email'],
                        ':password' => password_hash($profile['password'], PASSWORD_DEFAULT), // Encriptar la contraseña
                    ]);
                    $inserted++;
                } catch (PDOException $e) {
                    $errors++;
                }
            }

            echo "<p>Perfiles insertados: $inserted</p>";
            echo "<p>Errores: $errors</p>";
        } else {
            echo "<p>Error al decodificar el archivo JSON.</p>";
        }
    } else {
        echo "<p>No se encontró el archivo.</p>";
    }
}
?>
