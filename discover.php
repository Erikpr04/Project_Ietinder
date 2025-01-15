<script src="./js/utils.js"></script>

<!DOCTYPE html>
<html lang="en">
<!-- HEAD SECTION -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwipeIt! - Discover</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="./discover.js"></script>
</head>
<body>

<?php
require_once 'log.php';

// COOKIE AND USER SESSION MANAGEMENT
if (isset($_COOKIE['user_id'])) {
    createLog(action: "Usuario entrado a discover.php con id: " . $_COOKIE['user_id']);
}

?>

<!-- HTML STRUCTURE -->
<div id="main-container">
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
    <div id="main-footer">
        <a href="/discover.php">Descubrir</a>
        <a href="/messages.php">Mensajes</a>
        <a href="/profile.php">Perfil</a>
    </div>
</div>

</body>
</html>
