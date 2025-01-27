<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link type="text/css" rel="stylesheet" href="../css/style.css?t=<?php echo time();?>"/>

    <style>
        .page {
            display: none;
        }
        .page.active {
            display: table-row-group;
        }

    </style>
</head>
<body id="admin-index">


<?php
require_once '../rsc/log.php';
require_once '../rsc/db_config.php';





$results_per_page = 25;

$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$username = getenv('DB_USERNAME');
$pass = getenv('DB_PASSWORD');

$connection = new mysqli($host, $username, $pass, $dbname);

if ($connection->connect_error) {
    die("Conexión fallida: " . $connection->connect_error);
}

// Verificar si el parámetro "ID" está presente en la URL
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    // Obtener el valor de ID de forma segura
    $userId = $_GET["id"];

    // Crear la consulta SQL
    $sql = "SELECT * FROM User u LEFT JOIN Media m ON m.user_id = u.id WHERE u.id = ?";

    // Preparar la consulta
    $stmt = $connection->prepare($sql);

    // Vincular el parámetro (ID) a la consulta
    $stmt->bind_param("i", $userId);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener el resultado
    $result = $stmt->get_result();

    // Verificar si se obtuvieron resultados
    if ($result->num_rows > 0) {
        $currentrow = 0;
        // Mostrar los datos del usuario
        while ($row = $result->fetch_assoc()) {
            $currentrow +=1;
            if ($currentrow ==1){
                echo "<h1>Usuario: " . htmlspecialchars($row['name']) . " " . htmlspecialchars($row['last_name']) . "</h1>";
                echo "<p>Alias: " . htmlspecialchars($row['alias']) . "</p>";
                echo "<p>Email: " . htmlspecialchars($row['email']) . "</p>";
                echo "<p>Fecha de nacimiento: " . htmlspecialchars($row['birth_date']) . "</p>";
                echo "<p>Latitud: " . htmlspecialchars(string: $row['latitude']) . "</p>";
                echo "<p>Longitude: " . htmlspecialchars($row['longitude']) . "</p>";
                echo "<p>Sexo: " . htmlspecialchars(string: $row['sex']) . "</p>";
                echo "<p>Orientacion Sexual: " . htmlspecialchars(string: $row['sex']) . "</p>";
                echo "<p>Email: " . htmlspecialchars(string: $row['sex']) . "</p>";
                echo "<p>Estado de la cuenta: " . htmlspecialchars(string: $row['account_status']) . "</p>";
                echo "<p>Rol de la cuenta: " . (is_null($row['role_user']) ? "User" : htmlspecialchars($row['role_user'])) . "</p>";
                echo "<p>Fecha de creacion: " . htmlspecialchars(string: $row['created_at']) . "</p>";
                echo "<p>Última conexion: " . htmlspecialchars(string: $row['last_online']) . "</p>";






            }

            // Agregar más campos según lo necesites
            echo "<p>Foto ".$currentrow.": <img src='" . htmlspecialchars($row['media_path']) . "' alt='User Image'></p>"; // Suponiendo que hay una columna 'image_url'
            // Mostrar más información de la tabla MEDIA si es necesario
        }
    } else {
        echo "No se encontró el usuario con ID: " . htmlspecialchars($userId);
    }

    // Cerrar la sentencia
    $stmt->close();
}
else{
    $sql = "SELECT * FROM User";
    $result = $connection->query($sql);
    if ($result->num_rows > 0) {
        $total_records = $result->num_rows;
        $total_pages = ceil($total_records / $results_per_page);
    echo "<h1>Lista de Usuarios</h1>";
    
    echo '<table id=users-table border="1">';
        echo '<thead>';
        echo '<tr">';
        echo '<th>name</th>';
        echo '<th>last_name</th>';
        echo '<th>alias</th>';
        echo '<th>email</th>';
        echo '</tr>';
        echo '</thead>';
        
        // Dividir los datos en bloques por página
        $current_row = 0;
        $current_page = 1;
        echo '<tbody class="page active" id="page-' . $current_page . '">';
    
        while ($row = $result->fetch_assoc()) {
            if ($current_row > 0 && $current_row % $results_per_page == 0) {
                // Cerrar página actual y abrir la siguiente
                echo '</tbody>';
                $current_page++;
                echo '<tbody class="page" id="page-' . $current_page . '">';
            }
    
            echo '<tr class="user-row" id="' . $row['id'] . '">';
            echo '<td>' . htmlspecialchars($row['name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['last_name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['alias']) . '</td>';
            echo '<td>' . htmlspecialchars($row['email']) . '</td>';
            echo '</tr>';
    
            $current_row++;
        }
    
        echo '</tbody>';
        echo '</table>';
    
        echo '<div style="margin-top: 20px;">';
        for ($i = 1; $i <= $total_pages; $i++) {
            echo '<button type="button" onclick="showPage(' . $i . ')">' . $i . '</button>';
        }
        echo '</div>';




    } else {
        echo 'No results found.';
    }

}



?>

<script>
    function showPage(page) {
        const pages = document.querySelectorAll('.page');
        pages.forEach((pageElement) => {
            pageElement.classList.remove('active');
        });

        document.getElementById('page-' + page).classList.add('active');
    }

    const trElements = document.getElementsByClassName("user-row");

    for (let i = 0; i < trElements.length; i++) {
        const trElement = trElements[i];

        trElement.addEventListener("click", () => {
            const userId = trElement.id;
            window.location.href = `/admin/users.php?id=${userId}`;
        });


    }
</script>

</body>
</html>
