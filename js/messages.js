$.ajax({
    url: '/rsc/getAllMatches.php',
    method: 'GET',
    dataType: 'json',
    success: function(data) {
        createProfilesDependsOfConversationStarted(data);
    },
    error: function(xhr, status, error) {
        console.error('Hubo un error al obtener los datos: ' + error);
    }
});

// filtra y crea en función de si ha empezado o no la conversación
function createProfilesDependsOfConversationStarted(data){
    for (let i = 0; i < data.length; i++) {
        const profile = data[i];

        // crear contenedor
        const newMatchedProfile = $("<div></div>").attr('id', 'profile' + profile.idConversation);

        // obtener solo la primera foto
        const mediaPaths = profile.media_paths;
        const pathsArray = mediaPaths.split(',');

        // crear la foto y el nombre
        const image = $('<img>').attr('src',pathsArray[0]).attr('alt', 'Imagen de '+profile.name);
        const name = $("<p></p>").text(profile.name);

        // filtrar
        if(profile.started ===0){
            $('#matchedProfiles').append(newMatchedProfile);
            $('#profile'+profile.idConversation).append(image,name);
        }
        else{
            $('#messagedProfiles').append(newMatchedProfile);
            $('#profile'+profile.idConversation).append(image,name);
        }

    };
}