<?php
// Cargar las variables del archivo .env
require_once 'db_config.php';
require_once 'log.php';


// Obtener las variables de entorno necesarias con valores por defecto
$host = getenv('DB_HOST') ;
$dbname = getenv('DB_NAME') ;
$username = getenv('DB_USERNAME') ;
$password = getenv('DB_PASSWORD');

// Verificar que las variables requeridas estén definidas
if (!$dbname || !$username || !$password) {
    die("Error de configuración de la base de datos");
}





header('Content-Type: application/json');


if (!isset($_COOKIE['user_id']) ) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}


function getDiscoverData() {
    try {
        // Debug: Verificar cookie
        if (!isset($_COOKIE['user_id'])) {
            return ['error' => 'Usuario no autenticado'];
        }

        $userId = $_COOKIE['user_id'];

        // Debug: Verificar datos de usuario
        $userData = getUserData($userId);

        if (!$userData) {
            return ['error' => 'No se encontró el usuario'];
        }

        // Obtener datos necesarios del usuario
        $userSex = $userData->sex;
        $sexOrientation = $userData->sexual_orientation;
        $lat = $userData->latitude;
        $lon = $userData->longitude;
        

        // Calcular edad
        $birthDate = new DateTime($userData->birth_date);
        $currentDate = new DateTime();  
        $ageInterval = $birthDate->diff($currentDate);  
        $age = $ageInterval->y;  
        

        $profiles = getDBprofiles($lon, $lat, $userSex, $sexOrientation, $userId);

message: 
        $sortedProfiles = sortProfiles($profiles, $age);

        $html = renderProfiles($sortedProfiles);

        // Preparar respuesta
        $response = [
            'success' => true,
            'html' => $html,
            'debug' => [
                'profilesCount' => count($profiles),
                'userAge' => $age,
                'userId' => $userId
            ]
        ];

        return $response;

    } catch (Exception $e) {
        return [
            'error' => 'Error al procesar la solicitud: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ];
    }
}

// Debug: Capturar la respuesta antes de enviarla
$response = getDiscoverData();
echo json_encode($response);




function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371;
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);
    $deltaLat = $lat2 - $lat1;
    $deltaLon = $lon2 - $lon1;
    $a = sin($deltaLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($deltaLon / 2) ** 2;
    $c = 2 * asin(sqrt($a));
    return $earthRadius * $c;
}

// DATABASE INTERACTION FUNCTIONS
function getUserData($userId) {

    global $host,$dbname,$username,$password;

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error al conectar a la base de datos: " . $e->getMessage());
    }

    $sql = "
    SELECT id, name, last_name, alias, birth_date, latitude, longitude, sex, sexual_orientation, email, password
    FROM User
    WHERE id = :userId
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':userId' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        return (object) [
            'id' => $user['id'],
            'name' => $user['name'],
            'last_name' => $user['last_name'],
            'alias' => $user['alias'],
            'birth_date' => $user['birth_date'],
            'latitude' => $user['latitude'],
            'longitude' => $user['longitude'],
            'sex' => $user['sex'],
            'sexual_orientation' => $user['sexual_orientation'],
            'email' => $user['email'],
            'password' => $user['password']
        ];
    } else {
        return null;
    }
}

function getDBprofiles($lon, $lat, $userSex, $sex_orientation, $myId) {
    global $host, $dbname, $username, $password;

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error al conectar a la base de datos: " . $e->getMessage());
    }

    $sql = "
    SELECT DISTINCT u.id, u.name, u.last_name, u.alias, u.latitude, u.longitude, u.sex, u.sexual_orientation, 
        u.birth_date, u.email, u.password, m.media_path
    FROM User u
    LEFT JOIN Media m ON u.id = m.user_id
    WHERE m.media_path IS NOT NULL AND u.id != :myId
    ";

    if ($userSex == 'hombre') {
        if ($sex_orientation == 'heterosexual') {
            $sql .= " AND u.sex = 'mujer' AND u.sexual_orientation IN ('heterosexual', 'bisexual')";
        } elseif ($sex_orientation == 'homosexual') {
            $sql .= " AND u.sex = 'hombre' AND u.sexual_orientation IN ('homosexual', 'bisexual')";
        } elseif ($sex_orientation == 'bisexual') {
            $sql .= " AND (u.sex = 'hombre' OR u.sex = 'mujer') AND u.sexual_orientation IN ('bisexual')";
        }
    } elseif ($userSex == 'mujer') {
        if ($sex_orientation == 'heterosexual') {
            $sql .= " AND u.sex = 'hombre' AND u.sexual_orientation IN ('heterosexual', 'bisexual')";
        } elseif ($sex_orientation == 'homosexual') {
            $sql .= " AND u.sex = 'mujer' AND u.sexual_orientation IN ('homosexual', 'bisexual')";
        } elseif ($sex_orientation == 'bisexual') {
            $sql .= " AND (u.sex = 'hombre' OR u.sex = 'mujer') AND u.sexual_orientation IN ('bisexual')";
        }
    } 


    $stmt = $pdo->prepare($sql);
    $stmt->execute([':myId' => $myId]);

    $profiles = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Añadir imágenes en el array 'pictures'
        $userId = $row['id'];
        $images = [];

        // Consultar todas las imágenes para el usuario actual
        $stmtImages = $pdo->prepare("SELECT media_path FROM Media WHERE user_id = :userId");
        $stmtImages->execute([':userId' => $userId]);

        $imageIndex = 1;
        while ($image = $stmtImages->fetch(PDO::FETCH_ASSOC)) {
            $images["picture" . $imageIndex] = $image['media_path'];
            $imageIndex++;
        }

        // Añadir la lista de imágenes al perfil
        $row['pictures'] = $images;

        // Añadir los otros datos
        $userData = getUserData($row['id']);
        $distance = calculateHaversineDistance($lat, $lon, $row['latitude'], $row['longitude']);
        $row['distance'] = $distance;
        $row['user_data'] = $userData;

        // Calcular la edad
        if (!empty($row['birth_date'])) {
            $birthDate = new DateTime($row['birth_date']);
            $today = new DateTime('today');
            $age = $birthDate->diff($today)->y;  // Edad en años
            $row['age'] = $age;
        } else {
            $row['age'] = null;
        }

        // Solo agregar el perfil una vez
        $profiles[$userId] = $row;  // Usamos el id del usuario como clave para evitar duplicados
    }

    
    createLog($myId." ha encontrado " . count($profiles) . " perfiles");

    // Convertimos el array en una lista indexada de perfiles (sin claves duplicadas)
    return array_values($profiles);
}



// PROFILE MANAGEMENT FUNCTIONS
function sortProfiles($profiles, $myAge) {
    $maxAgeDiff = 100;
    $maxDistance = 20000;

    foreach ($profiles as &$profile) {
        $profile['age'] = date_diff(date_create($profile['birth_date']), date_create('today'))->y;
        $ageDiff = $profile['age'] - $myAge;
        $normalizedAgeDiff = min($ageDiff / $maxAgeDiff, 1);  
        $normalizedDistance = min($profile['distance'] / $maxDistance, 1);
        $profile['score'] = (0.7 * $normalizedAgeDiff) + (0.3 * $normalizedDistance);
    }

    usort($profiles, function($a, $b) {
        return $a['score'] <=> $b['score'];
    });

    return $profiles;
}

function renderProfiles($profiles) {
    $cards = '';
    foreach ($profiles as $index => $profile) {
        $name = htmlspecialchars($profile['name']);
        $age = $profile['age'];
        $user2_id = $profile['id'];
        
        $images = getProfileImages($profile['id']);
        
        if (empty($images)) {
            $images = [['media_path' => '/media/1.jpg']];
        }
        
        $carouselImages = '';
        $indicators = '';
        
        foreach ($images as $i => $image) {
            $activeClass = $i === 0 ? 'active' : '';
            $imgPath = htmlspecialchars($image['media_path']);
            
            $carouselImages .= "<img src=\"$imgPath\" alt=\"$name's Picture\" class=\"carousel-image $activeClass\">";
            $indicators .= "<span class=\"indicator $activeClass\" data-index=\"$i\"></span>";
        }

        $cards .= <<<HTML
        <div class="profile-card" data-user-id="$user2_id">
            <div class="carousel" id="carousel-$index">
                <div class="carousel-images">
                    $carouselImages
                </div>
                <div class="carousel-indicators">
                    $indicators
                </div>
            </div>
            <div class="profile-info">
                <h2>$name, $age</h2>
            </div>
        </div>
        HTML;
    }
    return $cards;
}

function getProfileImages($userId) {
    global $host,$dbname,$username,$password;

    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("SELECT media_path FROM Media WHERE user_id = ? ORDER BY id ASC");
        $stmt->execute([$userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

?>