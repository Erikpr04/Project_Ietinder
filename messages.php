<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Victor Valero, Erik Pinto, Marta Arévalo">
    <meta name="description" content="Esta es la vista mensajes de la app SwipeIt">

    <script src="/js/jquery-3.7.1.min.js"></script>
    <script src="/js/messages.js"></script>

    <link type="text/css" rel="stylesheet" href="/css/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">    

    <title>Mensajes</title>
</head>

<body id="messages">

    <div id="containerPrincipal">
        <header>
            <h2>S<span>w</span>ipeIt</h2>
            <a href="#"> Buscar </a>
        </header>

        <main>
            <div id="containerMatches">
                <h3>Mis matches</h3>

                <!-- para los que han dado match -->
                <div id="matchedProfiles"></div>

            </div>

            <div id="containerMessages">
                <h3>Mensajes</h3>

                <!-- para los que tienes una conversación -->
                <div id="messagedProfiles"></div>
            </div>
        </main>

        <?php include('footer.php'); ?>
    </div>

</body>
</html>