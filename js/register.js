// Definir map y marker como variables globales
let map;
let marker;

document.addEventListener('DOMContentLoaded', function() {
    let latitude = parseFloat(document.getElementById('latitude').value);
    let longitude = parseFloat(document.getElementById('longitude').value);

    if (isNaN(latitude) || isNaN(longitude)) {
        console.error("Error: Coordenadas iniciales no son válidas.");
        return;
    }

    // Inicializar el mapa
    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: latitude, lng: longitude },
        zoom: 12
    });

    // Crear un marcador inicial
    marker = new google.maps.Marker({
        position: { lat: latitude, lng: longitude },
        map: map,
        draggable: true // Permitir arrastrar el marcador
    });

    // Evento para actualizar coordenadas al mover el marcador
    google.maps.event.addListener(marker, 'dragend', function(event) {
        updateCoordinates(event.latLng.lat(), event.latLng.lng());
    });

    // Permitir clics en el mapa para mover el marcador
    google.maps.event.addListener(map, 'click', function(event) {
        marker.setPosition(event.latLng);
        updateCoordinates(event.latLng.lat(), event.latLng.lng());
    });

    // Asignar eventos a botones
    document.getElementById('buttonSave').addEventListener('click', function(event) {
        event.preventDefault();
        saveData();
    });
});

// Función para buscar las coordenadas de una ciudad ingresada
function searchCoordinates() {
    const city = document.getElementById('cityInput').value;
    const geocoder = new google.maps.Geocoder();

    geocoder.geocode({ 'address': city }, function(results, status) {
        if (status === 'OK' && results[0]) {
            const location = results[0].geometry.location;
            updateCoordinates(location.lat(), location.lng());
            map.setCenter(location);
            marker.setPosition(location);
        } else {
            console.log('No se encontraron coordenadas para esta ciudad.');
        }
    });
}

// Función para actualizar los inputs ocultos con las coordenadas seleccionadas
function updateCoordinates(lat, lng) {
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
    console.log("Ubicación actualizada: Lat:", lat, "Lng:", lng);
}



function saveData() {
    // Crear un objeto FormData para incluir todos los datos del formulario
    const formData = new FormData(document.getElementById('registrationForm'));

    console.log("Datos a enviar:", formData);

    // 🔹 Validaciones antes de enviar los datos
    if (!formData.get("password") || !formData.get("password2")) {
        createErrorTag("error", "Las contraseñas no pueden estar vacías.");
        return;
    }

    if (formData.get("password") !== formData.get("password2")) {
        createErrorTag("error", "Las contraseñas no coinciden.");
        return;
    }

    for (const [key, value] of formData.entries()) {
        if (!value) {
            createErrorTag("error", "Por favor, completa todos los campos.");
            return;
        }
    }

    if (!formData.get("email").endsWith("@iesesteveterradas.cat")) {
        createErrorTag("error", "El correo electrónico debe tener el dominio @iesesteveterradas.cat.");
        return;
    }

    // Enviar los datos al servidor
    $.ajax({
        url: "/rsc/process_register.php",
        method: "POST",
        data: formData,
        processData: false, // Evitar que jQuery procese los datos
        contentType: false, // Evitar que jQuery configure el encabezado Content-Type
        dataType: "json",
        success: function(response) {
            console.log("Respuesta del servidor:", response);
            if (response.success) {
                createErrorTag("warning", "Registro exitoso: Tienes 48 horas para activar tu cuenta.");
                setTimeout(function() {
                    window.location.href = "login.php"; // Redirigir a la página de login
                }, 3000); // 3000 ms = 3 segundos
            } else {
                createErrorTag("error", response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error al registrar:", xhr.responseText);
            createErrorTag("error", "Hubo un error al enviar los datos. Inténtalo de nuevo.");
        }
    });
}




//errores

function sendLog(action) {
    console.log("Action to send:", action);
    return fetch('./rsc/log.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({ action: action }), 
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .catch(error => {
        console.error("Error in fetch:", error);
    });
}

function createErrorTag(errorType, text) {
    // Crear el contenedor del error
    const errorTag = $('<div></div>').addClass('error-tag');
    
    const icon = $('<i></i>');
    let iconClass = '';

    // Asignar la clase y el ícono según el tipo de error
    switch (errorType) {
        case 'warning':
            iconClass = 'fa-solid fa-exclamation-triangle';
            errorTag.addClass('warning');
            break;
        case 'error':
            iconClass = 'fa-solid fa-times-circle';
            errorTag.addClass('error');
            break;
        case 'info':
            iconClass = 'fa-solid fa-info-circle';
            errorTag.addClass('info');
            break;
        default:
            iconClass = 'fa-solid fa-question-circle';
            errorTag.addClass('unknown');
    }

    icon.addClass(iconClass);
    const errorText = $('<span></span>').text(text);
    errorTag.append(icon).append(errorText);
    $('body').append(errorTag);

    setTimeout(() => {
        errorTag.addClass('show');
    }, 50); 


    // Desaparecer el error después de 3 segundos
    setTimeout(() => {
        errorTag.addClass('hide');
        setTimeout(() => {
            errorTag.remove();
        }, 500);
    }, 4000);
}