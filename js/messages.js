$.ajax({
    url: 'rsc/getAllMatches.php',
    method: 'GET',
    dataType: 'json',
    success: function(data) {
        createProfilesDependsOfConversationStarted(data, data.length === 0 ? 1 : 0);
    },
    error: function(xhr, status, error) {
        console.error('Hubo un error al obtener los datos: ' + error);
    }
});

// Filtra y crea en función de si ha empezado o no la conversación
function createProfilesDependsOfConversationStarted(data, isEmpty) {
    // No hay ningún match aún
    if (isEmpty === 1) {
        $('#matchedProfiles').addClass('centerContentInContainer');
        $('#messagedProfiles').addClass('centerContentInContainer');

        const noMatchedInfo = $('<div></div>').html('Hay gente esperando hablar contigo.<br>Devuélveles el like para empezar a charlar')
                                              .addClass('centerTextMessage');
        const noMessagesInfo = $('<div></div>').html('No hay ninguna conversación,<br>descubre gente nueva y haz match')
                                               .addClass('centerTextMessage');

        $('#matchedProfiles').append(noMatchedInfo);
        $('#messagedProfiles').append(noMessagesInfo);
        return;
    }

    let hasMatches = false;
    let hasMessages = false;

    for (let i = 0; i < data.length; i++) {
        const profile = data[i];

        // Crear contenedor para el perfil
        const newMatchedProfile = $("<div></div>").attr('id', 'profile' + profile.idConversation).addClass('cardProfileMessage');

        // Crear la foto y el nombre del usuario
        const image = $('<img>').attr('src', profile.media_path).attr('alt', 'Imagen de ' + profile.other_user_name);
        const name = $("<p></p>").text(profile.other_user_name).addClass('profileName');

        // Filtrar por si la conversación ha comenzado
        if (profile.started === 0) {
            hasMatches = true;
            $('#matchedProfiles').append(newMatchedProfile);
            $('#profile' + profile.idConversation).append(image, name);
        } else {
            hasMessages = true;

            // Buscar el último mensaje
            $.ajax({
                url: '/rsc/getLastMessage.php',
                method: 'POST',
                data: { idUsuario: profile.user2_id },
                success: function(lastMessageInfo) {
                    if (lastMessageInfo.error) {
                        console.error(lastMessageInfo.error);
                        return;
                    }

                    // Obtener el nombre del usuario con el que estamos conversando
                    const otherUserName = profile.other_user_name;

                    // Crear el mensaje con el nombre del usuario y el contenido
                    const lastMessage = $("<p class='lastMessage'></p>").text(otherUserName + ": " + lastMessageInfo.content);

                    // Crear un contenedor para el nombre y el último mensaje
                    const sectionContainer = $("<section></section>").append(name, lastMessage);

                    // Añadir el perfil al contenedor
                    $('#messagedProfiles').append(newMatchedProfile);
                    $('#profile' + profile.idConversation).append(image, sectionContainer);
                },
                error: function(xhr, status, error) {
                    console.error('Hubo un error al obtener el último mensaje: ' + error);
                }
            });
        }
    }

    // Si no hay matches
    if (!hasMatches) {
        $('#matchedProfiles').addClass('centerContentInContainer');
        const noMatchedInfo = $('<div></div>').html('Hay gente esperando hablar contigo.<br>Devuélveles el like para empezar a charlar')
        .addClass('centerTextMessage');
        $('#matchedProfiles').append(noMatchedInfo);
    }

    // Si no hay mensajes
    if (!hasMessages) {
        $('#messagedProfiles').addClass('centerContentInContainer');
        const noMessagesInfo = $('<div></div>').html('No hay ninguna conversación,<br>descubre gente nueva y haz match')
                                               .addClass('centerTextMessage');
        $('#messagedProfiles').append(noMessagesInfo);
    }
}
