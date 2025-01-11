<!DOCTYPE html>
<html lang="en">

<head>
  <link type="text/css" rel="stylesheet" href="./css/style.css" />
  <script src="https://kit.fontawesome.com/74d6337d15.js" crossorigin="anonymous"></script>
  <script src="./js/jquery-3.7.1.min.js"></script>
</head>

<body>
  <div class="login-container">
    <form action="" method="post" autocomplete="off">
      <h2>Iniciar sesión</h2>

      <div class="input-container">
        <div class="input-field">
          <input type="text" name="email" required>
          <label>Introduce tu correo</label>
        </div>

        <!-- Password input field -->
        <div class="input-field">
          <input type="password" name="password" required>
          <label>Introduce la contraseña</label>
        </div>
      </div>

      <!-- Submit button -->
      <button type="submit">Acceder</button>

      <!-- Forgot password and create account links -->
      <div class="forget-createAccount">
        <a href="#">¿Has olvidado la contraseña?</a>
        <a href="#">Crea una cuenta nueva</a>
      </div>
    </form>

    <?php
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
          $email = $_POST['email'];
          $password = $_POST['password'];

          $connection = new mysqli('localhost', 'client', 'milt0n', 'SwipeItDB');

          if ($connection->connect_error) {
              die("Conexión fallida: " . $connection->connect_error);
          }

          $sql = "SELECT * FROM User WHERE email = ? AND password = SHA2(?, 512)";
          $stmt = $connection->prepare($sql);
          $stmt->bind_param("ss", $email, $password);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows > 0) {
              header("Location: discover.php");
              exit();
          } else {
              ?>
    <?php
          }

          $stmt->close();
          $connection->close();
      }
    ?>
  </div>
</body>

</html>