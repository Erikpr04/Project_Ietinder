<!DOCTYPE html>
<html lang="es">
<!-- HEAD SECTION -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwipeIt! - Discover</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css?t=<?php echo time();?>"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="./js/utils.js"></script>
    <script src="https://kit.fontawesome.com/74d6337d15.js" crossorigin="anonymous"></script>

    <script src="./js/discover.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>


<!-- HTML STRUCTURE -->

<main class="main-container">
    <?php
        require_once './rsc/log.php';
        require_once './rsc/db_config.php';

        if (isset($_COOKIE['user_id'])) {
            createLog(action: "Usuario entrado a discover con id: " . $_COOKIE['user_id']);
        }
        else {
            createLog(action: "Usuario no tiene cookie, redirigiendo de discover a login");
            header('Location: ./login.php');
        }
    ?>
    <header class="main-header"><h2>S<span>w</span>ipeIt</h2>
        <div class="options-selector">
        <button class="menu-button">⋮</button>
    </header>
    <div class="menu-content" id="menu-content">
        <div class="slider-container">
            <label for="slider1">Distancia</label>
            <p id="slider1-value">50 km</p>
            <span id="slider1-min">0</span>

            <input type="range" id="slider1" min="0" max="200" value="50">
            <span id="slider1-max">200</span>
        </div>

        <div class="slider-container">
            <label for="slider2">Rango de Edad</label>
            <section class="range-slider">
                <input id="minAge" name="minAgeValue" value="18" min="18" max="100" step="1" type="number">
                <input id="maxAge" name="maxAgeValue" value="50" min="18" max="100" step="1" type="number">
            </section>
        </div>

        <button class="filter-button" type="submit">Filtrar</button>
        </div>
    <div id="main-content">
        <div class="match-overlay" id="matchOverlay">
            <div class="match-notification">
                <h2>¡Es un Match!</h2>
                <div>
                    <button id="continueButton">Seguir Descubriendo</button>
                    <button id="messagesButton">Ir a la Conversación</button>
                </div>
            </div>
        </div>
        <div id="main-content-container">
            <div id="main-content-buttons">
                <div id="dislike-button">
                    <p>No</p>
                </div>
                <div id="like-button">
                    <p>Sí</p>
                </div>
            </div>
        </div>
    </div>
    
    <?php include('footer.php'); ?>

</main>

<script>
    createErrorTag("info", "Te has logueado correctamente")
</script>

</body>
</html>
