function sendLog(action) {
    console.log("Action to send:", action);
    fetch('log.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({ action: action }), 
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