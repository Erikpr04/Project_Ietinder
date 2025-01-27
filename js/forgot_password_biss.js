$(document).ready(function () {
    $('#submit-button').on('click', function (event) {
        event.preventDefault(); // Evitar el envío tradicional del formulario

        // Deshabilitar el botón para evitar múltiples envíos
        $(this).prop('disabled', true).text('Enviando...');

        let email = $('#email input').val().trim();
        let password = $('#password-recuperar input').val().trim();
        console.log(password);
        let confirmPassword = $('#password2-recuperar input').val().trim();
        console.log(confirmPassword);
        let instruccionesPass = $('.forgot-instrucciones-pass');

        // Expresión regular para validar el email
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        // Expresión regular para validar la contraseña (mínimo 8 caracteres, una mayúscula, una minúscula y un número)
        let passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

        // Limpiar mensajes anteriores
        $('.error-tag').remove();
        instruccionesPass.hide();

        // Validación del email
        if (email === '' || !emailRegex.test(email)) {
            createErrorTag('error', 'Por favor, introduce un correo válido.');
            $(this).prop('disabled', false).text('Recuperar contraseña');
            return;
        }

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
        

        // Si pasa todas las validaciones, proceder con AJAX
        $.ajax({
            url: '/rsc/check_user.php', // Ruta de tu script PHP que maneja la lógica
            type: 'POST',
            data: { email: email, password: password },
            dataType: 'json',
            success: function (response) {
                if (response.valid) {
                    createErrorTag('info', 'Correo enviado con éxito.');
                    
                    // Redirigir después de 2 segundos (2000ms)
                    setTimeout(function () {
                        window.location.href = 'login.php';
                    }, 2000);
                    console.log({ email: email, password: password });
                    
                } else {
                    if (response.message === 'Usuario no encontrado') {
                        createErrorTag('info', 'El correo ingresado no está registrado.');
                    } else {
                        createErrorTag('error', response.message || 'Error al enviar el correo.');
                    }
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