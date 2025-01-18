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

    <div id="chatContainer" class="chat-container">
    </div>
    <form action="" id="messageForm" method="post">
            <input type="text" name="" id="message" placeholder="Escribe un mensaje"></input>
            <button type="submit" id="send"><i class="fa-solid fa-paper-plane-top"></i></button>
    </form>
    <?php include('footer.php'); ?>

</body>
</html>