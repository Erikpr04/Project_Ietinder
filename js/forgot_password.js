$('#submit-button').on('click', function (event) {
    event.preventDefault();
    $('#submit-button').on('click', function (event) {
        event.preventDefault();
        let email = $('#email input').val().trim(); 
        let name = $('#nombre input').val().trim();  // Cambié '#name' por '#nombre'
        let lastName = $('#Apellidos input').val().trim(); // Cambié '#last_name' por '#Apellidos'

        // Validar los campos
        if (email !== '' && name !== '') {
            $.ajax({
                url: '/rsc/check_user.php',
                type: 'POST',
                data: { email: email, name: name, last_name: lastName },
                dataType: 'json',
                success: function (response) {
                    console.log(response); // Verificar la respuesta del servidor
                    if (response.valid) {
                        createErrorTag('info', 'El mail de recuperación ha sido enviado.');
                    } else {
                        createErrorTag('error', 'Los datos introducidos no son correctos.');
                    }
                },
                error: function () {
                    createErrorTag('error', 'Error de conexión con el servidor.');
                }
            });
        }
    });
});



$(document).ready(function () {
    // Obtener el token de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get("token");

    if (token) {
        // Hacer petición AJAX para verificar el token
        $.ajax({
            url: "/rsc/validate_token.php",
            type: "GET",
            data: { token: token },
            dataType: "json",
            success: function (response) {
                if (response.valid) {
                    // Si el token es válido, cambiar el formulario
                    $(".forgot-instrucciones-title").text("Restablecer contraseña");
                    $(".forgot-instrucciones-text").text("Introduce tu nueva contraseña.");
                    
                    $(".input-container").html(`
                        <div class="data-container password-container">
                            <div class="input-field" id="password">
                                <input type="password" name="password" maxlength="100" required>
                                <label>Introduce tu nueva contraseña</label>
                            </div>
                        </div>

                        <div class="data-container password-container">
                            <div class="input-field" id="confirm-password">
                                <input type="password" name="confirm_password" maxlength="100" required>
                                <label>Confirma tu nueva contraseña</label>
                            </div>
                        </div>
                    `);

                    // Cambiar el botón de submit
                    $("button[type='submit']").text("Cambiar contraseña");

                    // Cambiar el formulario para enviarlo a reset_password.php
                    $("form").attr("action", "reset_password.php");
                    $("form").append(`<input type="hidden" name="token" value="${token}">`);
                } else {
                    alert("Token inválido o expirado.");
                }
            },
            error: function () {
                alert("Error al verificar el token.");
            }
        });
    }
});

