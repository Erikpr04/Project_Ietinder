<?php
require_once '../log.php';

header('Content-Type: application/json');

function getDiscoverData() {
    try {
        // Debug: Verificar cookie
        error_log("Verificando cookie de usuario...");
        if (!isset($_COOKIE['user_id'])) {
            error_log("Cookie no encontrada");
            return ['error' => 'Usuario no autenticado'];
        }

        $userId = $_COOKIE['user_id'];
        error_log("Usuario ID encontrado: " . $userId);

        // Debug: Verificar datos de usuario
        $userData = getUserData($userId);
        error_log("Datos de usuario obtenidos: " . print_r($userData, true));

        if (!$userData) {
            error_log("No se encontraron datos para el usuario: " . $userId);
            return ['error' => 'No se encontró el usuario'];
        }

        // Obtener datos necesarios del usuario
        $userSex = $userData->sex;
        $sexOrientation = $userData->sexual_orientation;
        $lat = $userData->latitude;
        $lon = $userData->longitude;
        
        error_log("Datos extraídos del usuario - Sexo: $userSex, Orientación: $sexOrientation, Lat: $lat, Lon: $lon");

        // Calcular edad
        $birthDate = new DateTime($userData->birth_date);
        $currentDate = new DateTime();  
        $ageInterval = $birthDate->diff($currentDate);  
        $age = $ageInterval->y;  
        
        error_log("Edad calculada: " . $age);

        // Debug: Obtener perfiles
        error_log("Obteniendo perfiles...");
        $profiles = getDBprofiles($lon, $lat, $userSex, $sexOrientation, $userId);
        error_log("Perfiles obtenidos: " . count($profiles));
        error_log("Perfiles raw: " . print_r($profiles, true));

        // Debug: Ordenar perfiles
        $sortedProfiles = sortProfiles($profiles, $age);
        error_log("Perfiles ordenados: " . count($sortedProfiles));

        // Debug: Generar HTML
        error_log("Generando HTML...");
        $html = renderProfiles($sortedProfiles);
        error_log("Longitud del HTML generado: " . strlen($html));
        error_log("Muestra del HTML: " . substr($html, 0, 200) . "...");

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

        error_log("Respuesta preparada: " . print_r($response, true));
        return $response;

    } catch (Exception $e) {
        error_log("Error en getDiscoverData: " . $e->getMessage());
        return [
            'error' => 'Error al procesar la solicitud: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ];
    }
}

// Debug: Capturar la respuesta antes de enviarla
$response = getDiscoverData();
error_log("Respuesta final: " . print_r($response, true));
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
    $host = "localhost";
    $dbname = "SwipeITDB";
    $username = 'client';  
    $password = 'milt0n'; 

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
    $host = "localhost";
    $dbname = "SwipeITDB";
    $username = 'client';  
    $password = 'milt0n'; 

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
    
    createLog($sql);

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':myId' => $myId]);

    $profiles = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $userData = getUserData($row['id']);
        $distance = calculateHaversineDistance($lat, $lon, $row['latitude'], $row['longitude']);
        $row['distance'] = $distance;
        $row['user_data'] = $userData;
        
        // Calcular la edad a partir de la fecha de nacimiento
        if (!empty($row['birth_date'])) {
            $birthDate = new DateTime($row['birth_date']);
            $today = new DateTime('today');
            $age = $birthDate->diff($today)->y;  // Edad en años
            $row['age'] = $age;  // Asignamos la edad calculada al array $row
        } else {
            $row['age'] = null;  
        }
        
        $profiles[] = $row;
    }
    
    createLog("Se han encontrado " . count($profiles) . " Perfiles");


    return $profiles;
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
                <p>$name, $age</p>
            </div>
        </div>
        HTML;
    }
    return $cards;
}

function getProfileImages($userId) {
    $host = "localhost";
    $dbname = "SwipeITDB";
    $username = 'client';  
    $password = 'milt0n'; 
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("SELECT media_path FROM Media WHERE user_id = ? ORDER BY id ASC");
        $stmt->execute([$userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener imágenes: " . $e->getMessage());
        return [];
    }
}

// INTERACTION HANDLING
function handleLike($user1_id, $user2_id) {
    $host = "localhost";
    $dbname = "SwipeITDB";
    $username = 'client';  
    $password = 'milt0n'; 

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error al conectar a la base de datos: " . $e->getMessage());
    }

    $stmt = $pdo->prepare("INSERT INTO Interaction (user1_id, user2_id, type, matched) VALUES (?, ?, 'like', false) ON DUPLICATE KEY UPDATE type='like'");
    $stmt->execute([$user1_id, $user2_id]);

    $stmt = $pdo->prepare("SELECT * FROM Interaction WHERE user1_id = ? AND user2_id = ? AND type = 'like'");
    $stmt->execute([$user2_id, $user1_id]);
    $match = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($match) {
        $stmt = $pdo->prepare("UPDATE Interaction SET matched = true WHERE user1_id = ? AND user2_id = ?");
        $stmt->execute([$user1_id, $user2_id]);
        return true;
    }

    return false;
}


?>