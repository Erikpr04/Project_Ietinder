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

$sql = "SELECT * FROM User";
$result = $connection->query($sql);

if ($result->num_rows > 0) {
    $total_records = $result->num_rows;
    $total_pages = ceil($total_records / $results_per_page);

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
            window.location.href = `/user_info.php?id=${userId}`;
        });


    }
</script>

</body>
</html>
