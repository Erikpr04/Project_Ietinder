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

// filtra y crea en función de si ha empezado o no la conversación
function createProfilesDependsOfConversationStarted(data,isEmpty){

    // no hay ningún match aún
    if(isEmpty===1){

        //buscar el contenedor y ponerle que los items se alineen al centro
        $('#matchedProfiles').addClass('centerContentInContainer');
        $('#messagedProfiles').addClass('centerContentInContainer');
        // crear contenedor
        const noMatchedInfo = $('<div></div>').html('Hay gente esperando hablar contigo.<br>Devuélveles el like para empezar a charlar')
                                              .addClass('centerTextMessage');
        const noMessagesInfo = $('<div></div>').html('No hay ninguna conversación,<br>descubre gente nueva y haz match')
                                               .addClass('centerTextMessage');

        $('#matchedProfiles').append(noMatchedInfo);
        $('#messagedProfiles').append(noMessagesInfo);

        return;
    }

    // si hay matches
    
    // booleano de comprobación si hay perfiles
    let hasMatches = false;
    let hasMessages = false;

    for (let i = 0; i < data.length; i++) {
        const profile = data[i];
        console.log(data);

        
        // crear contenedor
        const newMatchedProfile = $("<div></div>").attr('id', 'profile' + profile.idConversation).addClass('cardProfileMessage');

        // crear la foto y el nombre
        const image = $('<img>').attr('src',profile.media_path).attr('alt', 'Imagen de '+profile.other_user_name);
        const name = $("<p></p>").text(profile.other_user_name);

        // filtrar
        if(profile.started ===0){
            hasMatches = true;
            $('#matchedProfiles').append(newMatchedProfile);
            $('#profile'+profile.idConversation).append(image,name);
        }
        else{
            hasMessages = true;

            //buscar el último mensaje
            $.ajax({
                url: '/rsc/getLastMessage.php',
                method: 'POST',
                data: { idUsuario: profile.user2_id },
                success: function(lastMessageInfo) {

                    const lastMessage = $("<p></p>").text(lastMessageInfo.slice(1, -1));
                    // contenedor que almecena los datos relacionados
                    const sectionContainer = $("<section></section>").append(name, lastMessage);

                    $('#messagedProfiles').append(newMatchedProfile);
                    $('#profile' + profile.idConversation).append(image,sectionContainer);

                },
                error: function(xhr, status, error) {
                    console.error('Hubo un error al obtener el último mensaje: ' + error);
                }
            }); 

        }

    };

    if(!hasMatches){
        $('#matchedProfiles').addClass('centerContentInContainer');
        const noMatchedInfo = $('<div></div>').html('Hay gente esperando hablar contigo.<br>Devuélveles el like para empezar a charlar')
        .addClass('centerTextMessage');
        $('#matchedProfiles').append(noMatchedInfo);
    }

    if (!hasMessages) {
        $('#messagedProfiles').addClass('centerContentInContainer');
        const noMessagesInfo = $('<div></div>').html('No hay ninguna conversación,<br>descubre gente nueva y haz match')
                                               .addClass('centerTextMessage');
        $('#messagedProfiles').append(noMessagesInfo);
    }
    
}