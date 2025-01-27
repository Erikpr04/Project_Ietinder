$(document).ready(function () {
    $('#submit-button').on('click', function (event) {
        event.preventDefault(); // Evitar el envío tradicional del formulario

        // Deshabilitar el botón para evitar múltiples envíos
        $(this).prop('disabled', true).text('Enviando...');

        let password = $('#password-recuperar input').val().trim();
        let confirmPassword = $('#password2-recuperar input').val().trim();
        let instruccionesPass = $('.forgot-instrucciones-pass');

        // Expresión regular para validar la contraseña (mínimo 8 caracteres, una mayúscula, una minúscula y un número)
        let passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

        // Limpiar mensajes anteriores
        $('.error-tag').remove();
        instruccionesPass.hide();

        // Validación de la contraseña
        if (!passwordRegex.test(password)) {
            createErrorTag('warning', 'La contraseña debe tener al menos 8 caracteres y contener al menos un número, una minúscula y una mayúscula');
            instruccionesPass.show(); // Mostrar instrucciones si la contraseña no es válida
            $(this).prop('disabled', false).text('Recuperar contraseña');
            return;
        }

        // Verificar que las contraseñas coincidan
        if (password !== confirmPassword) {
            createErrorTag('error', 'Las contraseñas no coinciden.');
            $(this).prop('disabled', false).text('Recuperar contraseña');
            return;
        }

        // Si pasa todas las validaciones, proceder con AJAX para actualizar la contraseña
        $.ajax({
            url: window.location.href, // Envía los datos al mismo archivo PHP
            type: 'POST',
            data: { password: password, password2: confirmPassword },
            dataType: 'json',
            success: function (response) {
                if (response.valid) {
                    createErrorTag('info', 'Contraseña restablecida correctamente. Redirigiendo...');
                    
                    // Redirigir después de 2 segundos (2000ms)
                    setTimeout(function () {
                        window.location.href = '/login.php';
                    }, 2000);
                    
                } else {
                    createErrorTag('error', response.message || 'Error al restablecer la contraseña.');
                }
            },
            error: function () {
                createErrorTag('error', 'Error al procesar la solicitud.');
            },
            complete: function () {
                // Rehabilitar el botón tras el proceso
                $('#submit-button').prop('disabled', false).text('Recuperar contraseña');
            }
        });
    });
});
