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
    <script src="./js/discover.js"></script>
</head>
<body>

<div id="main-container">
    <?php
        require_once './rsc/log.php';

        if (isset($_COOKIE['user_id'])) {
            createLog(action: "Usuario entrado a discover con id: " . $_COOKIE['user_id']);
        }
        else {
            createLog(action: "Usuario no tiene cookie, redirigiendo de discover a login");

            header('Location: ./login.php');
        }
    ?>

    <div id="main-header"><h2>S<span>w</span>ipeIt!</h2></div>
    <div id="main-content">
        <div class="match-overlay" id="matchOverlay">
            <div class="match-notification">
                <h2>¡Es un Match!</h>
                <div>
                    <button id="continueButton">Seguir Descubriendo</button>
                    <button id="messagesButton">Ir a Mensajes</button>
                </div>
            </div>
        </div>
        <div id="main-content-container">
            <div id="main-content-buttons">
                <div id="dislike-button">
                    <p>No</p>
                </div>
                <div id="like-button">
                    <p>Yes</p>
                </div>
            </div>
        </div>
    </div>
    
    <?php include('footer.php'); ?>

</div>

</body>
</html>
