<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwipeIt! - Discover</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src = "./discover.js"></script>
    </head>
<body>

<?php
//!!!CAMBIAR A LLAMADA A LA BASE DE DATOS PARA OBTENER LOS PERFILES!!!

// Lista de perfiles
$profiles = [
    [
        "name" => "Leandro",
        "last_name" => "Zabala",
        "alias" => "lzabalaoled",
        "location" => ["city" => "Ferrol", "latitude" => "-83.5856", "longitude" => "-178.1043"],
        "sex" => "home",
        "sexualOrientation" => "heterosexual",
        "picture" => "https://i.ibb.co/8Kq8f1c/image.png",
        "picture2" => "https://picsum.photos/300/533?random=0.4692198948886328",
        "birthdate" => "1963-04-26",
        "email" => "lzabala.soto@iesesteveterradas.cat",
        "password" => "revolver"
    ],
    [
        "name" => "Leandro",
        "last_name" => "Zabala",
        "alias" => "lzabalaoled",
        "location" => ["city" => "Ferrol", "latitude" => "-83.5856", "longitude" => "-178.1043"],
        "sex" => "home",
        "sexualOrientation" => "heterosexual",
        "picture" => "https://i.ibb.co/8Kq8f1c/image.png",
        "picture2" => "https://picsum.photos/300/533?random=0.4692198948886328",
        "birthdate" => "1963-04-26",
        "email" => "lzabala.soto@iesesteveterradas.cat",
        "password" => "revolver"
    ],
];

// Generar tarjetas de perfiles
function renderProfiles($profiles) {
    $cards = '';
    foreach ($profiles as $profile) {
        $name = htmlspecialchars($profile['name']);
        $age = date_diff(date_create($profile['birthdate']), date_create('today'))->y; // Calcular edad
        $city = htmlspecialchars($profile['location']['city']);
        $picture = htmlspecialchars($profile['picture']);
        
        $cards .= <<<HTML
        <div class="profile-card">
            <img src="$picture" alt="$name's Picture" class="profile-picture">
            <div class="profile-info">
                <h2>$name, $age</h2>
                <p>$city</p>
            </div>
        </div>
        HTML;
    }
    return $cards;
}
?>





    <div id ="main-container">
        <div id="main-header">SwipeIt!</div>
        <div id="main-content">
            <div id="main-content-container">    <?php echo renderProfiles($profiles); ?>
            </div>
            <div id="main-content-buttons">
                <div id="dislike-button">    <i class="fas fa-times"></i>
                </div>
                <div id="like-button"><i class="fas fa-heart"></i></div>
            </div>
        </div>
        <div id="main-footer">
            <a>Descubrir</a>
            <a>Mensajes</a>
            <a>Perfil</a>
        </div>

    </div>
   
</body>
</html>