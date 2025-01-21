<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Victor Valero, Erik Pinto, Marta Arévalo, Pau Gracia">
    <meta name="description" content="Esta es la vista chat de la app SwipeIt">

    <script src="./js/jquery-3.7.1.min.js"></script>
    <script src="./js/chat.js"></script>
    <script src="https://kit.fontawesome.com/74d6337d15.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.7.2/css/all.css">

    <link type="text/css" rel="stylesheet" href="./css/style.css?t=<?php echo time();?>"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">    

    <title>SwipeIt! - Chat</title>
</head>
<body id="chat">
    <?php
        require_once './rsc/log.php';
    ?>

    <div class="main-header">
        <h2>S<span>w</span>ipeIt</h2>
    </div>
<div class="chat-header-container">
    <a href="/messages.php">
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <div id="userInfo" class="chat-header">
        <!-- Aquí se cargarán el nombre y la imagen del usuario -->
    </div>
</div>


    <div class="tab">
        <button class="tablinks" onclick="openCity(event, 'tab-chatContainer')">Conversation</button>
        <button class="tablinks" onclick="openCity(event, 'tab-profileContainer')">Profile</button>
    </div>

    <div id="tab-chatContainer" class="tabcontent">
        <div id="chatContainer" class="chat-container">

        </div>
        <form action="" id="messageForm" method="post" autocomplete="off">
            <input type="text" name="" id="message" placeholder="Escribe un mensaje"></input>
            <button type="submit" id="send"><i class="fa-solid fa-paper-plane-top"></i></button>
    </form>
    </div>

    <div id="tab-profileContainer" class="tabcontent">
        <div id="profileContainer">

        </div>
    </div>



    <?php include('footer.php'); ?>

    <script>

document.addEventListener('DOMContentLoaded', () => {
    const defaultTab = document.querySelector('.tablinks'); // Primer tab
    if (defaultTab) {
        defaultTab.click(); // Simula un clic para activar el tab predeterminado
    }
});

function openCity(evt, tab) {
    var tabcontent = document.getElementsByClassName("tabcontent");
    for (var i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none"; // Oculta todo
    }

    var tablinks = document.getElementsByClassName("tablinks");
    for (var i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Muestra el tab correspondiente y añade la clase activa
    document.getElementById(tab).style.display = "flex";
    evt.currentTarget.className += " active";
}

    </script>

</body>
</html>