
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
            $userId = intval($_GET["id"]); // Sanitizar el ID del usuario

            // Consulta para obtener los datos del usuario y sus medios asociados
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
                        // Mostrar información básica del usuario
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

                    // Mostrar las fotos del usuario
                    if (!empty($row['media_path'])) {
                        echo "<p>Foto " . $currentrow . ": <div class='photo-placeholder'> <img src='/" . htmlspecialchars($row['media_path']) . "' alt='User Image'> </div> </p>";
                    }
                }
            } else {
                echo "No se encontró el usuario con ID: " . htmlspecialchars($userId);
            }

            $stmt->close();
        } else {
            $sql = "SELECT * FROM User";
            $result = $connection->query($sql);

            if ($result->num_rows > 0) {
                $total_records = $result->num_rows;
                $total_pages = ceil($total_records / $results_per_page);

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

                // Paginación
                $current_row = 0;
                $current_page = 1;
                echo '<tbody class="page active" id="page-' . $current_page . '">';

                while ($row = $result->fetch_assoc()) {
                    if ($current_row > 0 && $current_row % $results_per_page == 0) {
                        echo '</tbody>';
                        $current_page++;
                        echo '<tbody class="page" id="page-' . $current_page . '">';
                    }

                    echo '<tr class="user-row" id="' . htmlspecialchars($row['id']) . '">';
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
                    echo '<button id="pagination-button-' . $i . '" class="pagination-button" type="button" onclick="showPage(' . $i . ')">' . $i . '</button>';
                }
                echo '</div>';
            } else {
                echo 'No se encontraron resultados.';
            }
        }

        $connection->close();
        ?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const buttons = document.querySelectorAll('.pagination-button');

        if (buttons.length > 0) {
            buttons[0].classList.add('active');
        }

        function showPage(page) {
            const pages = document.querySelectorAll('.page');
            pages.forEach((pageElement) => {
                pageElement.classList.remove('active');
            });

            const selectedPage = document.getElementById('page-' + page);
            if (selectedPage) {
                selectedPage.classList.add('active');
            }

            buttons.forEach((button) => {
                button.classList.remove('active');
            });

            const activeButton = document.getElementById('pagination-button-' + page);
            if (activeButton) {
                activeButton.classList.add('active');
            }
        }

        // Expone la función showPage globalmente
        window.showPage = showPage;

        const trElements = document.getElementsByClassName('user-row');
        for (let i = 0; i < trElements.length; i++) {
            const trElement = trElements[i];
            trElement.addEventListener('click', () => {
                const userId = trElement.id;
                window.location.href = `/admin/users.php?id=${userId}`;
            });
        }
    });
</script>


    </main>
</body>
</html>