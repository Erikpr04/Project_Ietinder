<body id="user-index">
    <main>
        <?php
        include 'index.php';
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

        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $userId = intval($_GET["id"]); 

            $sql = "SELECT * FROM User u LEFT JOIN Media m ON m.user_id = u.id WHERE u.id = ?";
            $stmt = $connection->prepare($sql);

            if (!$stmt) {
                die("Error en la preparación de la consulta: " . $connection->error);
            }

            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $currentrow = 0;
                while ($row = $result->fetch_assoc()) {
                    $currentrow += 1;
                    if ($currentrow == 1) {
                        echo "<h1>Usuario: " . htmlspecialchars($row['name']) . " " . htmlspecialchars($row['last_name']) . "</h1>";
                        echo "<p>Alias: " . htmlspecialchars($row['alias']) . "</p>";
                        echo "<p>Email: " . htmlspecialchars($row['email']) . "</p>";
                        echo "<p>Fecha de nacimiento: " . htmlspecialchars($row['birth_date']) . "</p>";
                        echo "<p>Latitud: " . htmlspecialchars($row['latitude']) . "</p>";
                        echo "<p>Longitud: " . htmlspecialchars($row['longitude']) . "</p>";
                        echo "<p>Sexo: " . htmlspecialchars($row['sex']) . "</p>";
                        echo "<p>Orientación Sexual: " . htmlspecialchars($row['sexual_orientation']) . "</p>";
                        echo "<p>Estado de la cuenta: " . htmlspecialchars($row['account_status']) . "</p>";
                        echo "<p>Rol de la cuenta: " . (is_null($row['role_user']) ? "User" : htmlspecialchars($row['role_user'])) . "</p>";
                        echo "<p>Fecha de creación: " . htmlspecialchars($row['created_at']) . "</p>";
                        echo "<p>Última conexión: " . htmlspecialchars($row['last_online']) . "</p>";
                    }

                    if (!empty($row['media_path'])) {
                        echo "<p>Foto " . $currentrow . ": <div class='photo-placeholder'> <img src='/" . htmlspecialchars($row['media_path']) . "' alt='User Image'> </div> </p>";
                    }
                }
            } else {
                echo "No se encontró el usuario con ID: " . htmlspecialchars($userId);
            }

            $stmt->close();
        } else {
            // Obtener la página actual desde el parámetro GET
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $offset = ($page - 1) * $results_per_page;

            // Consulta para obtener el total de registros
            $sql_count = "SELECT COUNT(*) as total FROM User";
            $result_count = $connection->query($sql_count);
            $row_count = $result_count->fetch_assoc();
            $total_records = $row_count['total'];
            $total_pages = ceil($total_records / $results_per_page);

            // Consulta para obtener los registros de la página actual
            $sql = "SELECT * FROM User LIMIT $offset, $results_per_page";
            $result = $connection->query($sql);

            if ($result->num_rows > 0) {
                echo "<h1>Lista de Usuarios</h1>";
                echo '<table id="users-table" border="1">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Nombre</th>';
                echo '<th>Apellido</th>';
                echo '<th>Alias</th>';
                echo '<th>Email</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                while ($row = $result->fetch_assoc()) {
                    echo '<tr class="user-row" data-user-id="' . htmlspecialchars($row['id']) . '">';
                    echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['last_name']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['alias']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';

                // Mostrar la paginación
                echo '<div style="margin-top: 20px;">';
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $page) {
                        echo '<span style="margin: 5px; font-weight: bold;">' . $i . '</span>';
                    } else {
                        echo '<a href="?page=' . $i . '" style="margin: 5px;">' . $i . '</a>';
                    }
                }
                echo '</div>';
            } else {
                echo 'No se encontraron resultados.';
            }
        }

        $connection->close();
        ?>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const userRows = document.querySelectorAll('.user-row');

            userRows.forEach(row => {
                row.addEventListener('click', () => {
                    const userId = row.getAttribute('data-user-id');

                    if (userId) {
                        window.location.href = `/admin/users.php?id=${userId}`;
                    }
                });
            });
        });
    </script>
</body>
</html>