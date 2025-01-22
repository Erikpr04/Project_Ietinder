<?php
require_once './rsc/db_config.php';


$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME') ;
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD') ;


session_start();

// Verificar cookie del usuario
if (!isset($_COOKIE['user_id'])) {
    header('Location: ./login.php');
    exit;
}

$user_id = $_COOKIE['user_id'];
$messages = [];

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}

// Manejo de acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Subir imagen
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        // Configuración de directorio y nombre de archivo
        $target_dir = __DIR__ . "/media/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        // Verificamos tipo de archivo
        $allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($file_info, $_FILES['photo']['tmp_name']);
        finfo_close($file_info);

        if (!in_array($file_type, $allowed_types)) {
            $messages[] = "Error: El archivo no es una imagen válida (PNG, JPEG o JPG).";
        } else {
            // Generamos nombre único para el archivo
            $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid() . "." . $file_extension;
            $target_file = $target_dir . $file_name;
            $relative_path = "media/" . $file_name;

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                try {
                    $query = $pdo->prepare("INSERT INTO Media (user_id, media_path) VALUES (:user_id, :media_path)");
                    $query->execute([
                        ":user_id" => $user_id,
                        ":media_path" => $relative_path
                    ]);
                    $messages[] = "Imagen subida y guardada correctamente.";
                    //Al terminar, recargamos pagina
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                } catch (PDOException $e) {
                    //Si da error se elimina el archivo guardado en local
                    $messages[] = "Error al guardar en la base de datos: " . $e->getMessage();
                    if (file_exists($target_file)) {
                        unlink($target_file);
                    }
                }
            } else {
                $messages[] = "Error: No se pudo mover el archivo.";
            }
        }
    }

    // Eliminar imagen
    if (isset($_POST['delete_photo_id'])) {
        $media_id = filter_var($_POST['delete_photo_id'], FILTER_VALIDATE_INT);
        
        if ($media_id !== false) {
            try {
                // Verificamos si es la única foto
                $query = $pdo->prepare("SELECT COUNT(*) as total FROM Media WHERE user_id = :user_id");
                $query->execute([":user_id" => $user_id]);
                $total = $query->fetch(PDO::FETCH_ASSOC)['total'];

                if ($total <= 1) {
                    $messages[] = "No se puede eliminar la única foto del perfil.";
                } else {
                    // Obtenemos información de la foto a eliminar
                    $query = $pdo->prepare("SELECT media_path FROM Media WHERE id = :media_id AND user_id = :user_id");
                    $query->execute([
                        ":media_id" => $media_id,
                        ":user_id" => $user_id
                    ]);
                    $photo = $query->fetch(PDO::FETCH_ASSOC);

                    if ($photo) {
                        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        // Eliminamos primero el archivo físico
                        $full_path = __DIR__ . "/" . $photo['media_path'];
                        if (file_exists($full_path)) {
                            unlink($full_path);
                        }

                        // Despues eliminamos de la bbdd
                        $query = $root_pdo->prepare("DELETE FROM Media WHERE id = :media_id AND user_id = :user_id");
                        $query->execute([
                            ":media_id" => $media_id,
                            ":user_id" => $user_id
                        ]);
                        
                        $messages[] = "Imagen eliminada correctamente.";
                        
                        // Al eliminar recargamos la página
                        header("Location: " . $_SERVER['PHP_SELF']); 
                        exit;
                    }
                }
            } catch (PDOException $e) {
                $messages[] = "Error al eliminar la imagen: " . $e->getMessage();
            }
        }
    }
}

// Obtenemos imágenes del usuario
try {
    $query = $pdo->prepare("SELECT id, media_path FROM Media WHERE user_id = :user_id ORDER BY id ASC");
    $query->execute([":user_id" => $user_id]);
    $photos = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $messages[] = "Error al cargar las imágenes: " . $e->getMessage();
    $photos = [];
}

// Limpiamos pdo
$pdo = null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/css" rel="stylesheet" href="./css/style.css?t=<?php echo time(); ?>"/>
    <script src="https://kit.fontawesome.com/74d6337d15.js" crossorigin="anonymous"></script>
    <script src="./js/jquery-3.7.1.min.js"></script>
    <script src="./js/utils.js"></script>

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">   


    <title>SwipeIt! - Edit Photos</title>
</head>
<body id="photos">
    
    <main class="main-container">
        <div class="main-header"><h2>S<span>w</span>ipeIt</h2></div>
            <div class="main-photos-content">

        
                <h3>Add Your Photos</h3>
                <div class="photo-grid">
                <!-- Iteramos sobre las imagenes obtenidas -->

                <?php foreach ($photos as $index => $photo): ?>
                    <?php if ($index === 0): ?>
                        <div class="photo-placeholder main-photo">
                            <div class="main-photo-tag">Foto Principal</div>
                            <?php if (count($photos) > 1): ?>
                                <form method="post">
                                    <input type="hidden" name="delete_photo_id" value="<?php echo htmlspecialchars($photo['id']); ?>">
                                    <button type="submit" class="delete-button">×</button>
                                </form>
                            <?php endif; ?>
                            <img src="<?php echo htmlspecialchars($photo['media_path']); ?>" alt="Foto Principal">
                        </div>
                    <?php else: ?>
                        <div class="photo-placeholder">
                            <form method="post">
                                <input type="hidden" name="delete_photo_id" value="<?php echo htmlspecialchars($photo['id']); ?>">
                                <button type="submit" class="delete-button">×</button>
                            </form>
                            <img src="<?php echo htmlspecialchars($photo['media_path']); ?>" alt="Photo">
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <!-- Si hay menos de 6 imagenes, se genera otro input-->

                <?php if (count($photos) < 6): ?>
                    <?php for ($i = count($photos); $i < 6; $i++): ?>
                    <div class="photo-placeholder">
                        <form method="post" enctype="multipart/form-data">
                            <label>
                                <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg" 
                                    style="display: none;" onchange="this.form.submit()">
                                <div class="placeholder-content">+</div>
                            </label>
                        </form>
                    </div>
                <?php endfor; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php include('footer.php'); ?>

    </main>

</body>
</html>