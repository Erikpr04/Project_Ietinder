<?php
// Simulación de datos
$name = "Juan Pérez";
$validationLink = "http://localhost:8080/rsc/verify.php?token=123456";

// Aquí puedes modificar la estructura y diseño sin necesidad de enviar el email
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista Previa - Correo de Validación</title>
   
    
</head>

<body style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; font-family: Arial, sans-serif; margin: 0; padding: 0; background-size: 400% 400%; color: #333;">

<div style="text-align: center; width: 100%; max-width: 460px; padding: 20px; background:rgb(255, 169, 56); border-radius: 10px; box-sizing: border-box;">
                <h2 style="font-family: Arial, sans-serif; font-size: 4rem; color: black;">S<span style="color:rgb(255, 0, 0);">w</span>ipeIt</h2>
                <p style="font-size: 1.2rem; color: black;">¡El <span style="color:rgb(255, 0, 0);">amor</span> está a un swipe!</p>
                
                <p style="color: black;">Hola ' . htmlspecialchars($data['name']) . ',</p>
                <p style="color: black;">Gracias por registrarte en SwipeIt. Para completar el registro y activar tu cuenta, haz clic en el siguiente botón:</p>
                <p><a href="' . $validationLink . '" style="background-color: #4CAF50; color: black; padding: 14px 20px; text-align: center; text-decoration: none; display: inline-block; font-size: 1.2rem; border-radius: 5px;">Validar mi cuenta</a></p>
                <p style="color: black;">Si no has realizado este registro, puedes ignorar este correo.</p>
</div>
            
</body>
</html>
