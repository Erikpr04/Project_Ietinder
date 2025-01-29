$(document).ready(function () {
    $('#submit-button').on('click', function (event) {
        event.preventDefault(); // Evitar el envío tradicional del formulario

        // Deshabilitar el botón para evitar múltiples envíos
        $(this).prop('disabled', true).text('Enviando...');

        let email = $('#email input').val().trim();

        // Expresión regular para validar el email
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Limpiar mensajes anteriores
        $('.error-tag').remove();

        // Validación del emails
        if (email === '' || !emailRegex.test(email)) {
            createErrorTag('error', 'Por favor, introduce un correo válido.');
            $(this).prop('disabled', false).text('Recuperar contraseña');
            return;
        }

        // Si pasa la validación, proceder con AJAX
        $.ajax({
            url: '/rsc/check_user.php', // Ruta de tu script PHP que maneja la lógica
            type: 'POST',
            data: { email: email },
            dataType: 'json',
            success: function (response) {
                if (response.valid) {
                    createErrorTag('info', 'Correo enviado con éxito.');
                      // Redirigir después de 2 segundos (2000ms)
                      setTimeout(function () {
                        window.location.href = 'login.php';
                    }, 2000);
                    console.log({ email: email});
                } else {
                    createErrorTag('error', 'El correo ingresado no está registrado.');
                }
            },
            error: function () {
                createErrorTag('error', 'Error al enviar la solicitud.');
            },
            complete: function () {
                // Rehabilitar el botón tras el proceso
                $('#submit-button').prop('disabled', false).text('Recuperar contraseña');
            }
        });
    });
});
