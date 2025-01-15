$(function(){

    //variables globales para el mapa
    let marker;
    
    // inicializar el mapa
    initMap();
    // asignación de eventos click
    $("#buttonSave").off().click(function(event){
        event.preventDefault();  // Prevenir que el formulario se envíe y recargue la página
        saveData(); 
    });
})

// función de creación mapa google api
function initMap() {
    
    const location = coordinates;
    // crear el mapa
    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 16,
        center: location,
    });
    // añadir la chincheta
    marker = new google.maps.Marker({
        position: location,
        map: map,
    });
    // evento click para cambiar la chincheta
    map.addListener('click', function(event) {
        const newLocation = event.latLng;
        marker.setPosition(newLocation);
        coordinates = {
            lat: newLocation.lat(),
            lng: newLocation.lng()
        };
        console.log('Nuevas coordenadas:', coordinates);
    });
}

function saveData(){
    const data = {};
    // Obtener los valores de los campos
    data.name = $("#name").val();
    data.lastName = $("#lastName").val();
    data.alias = $("#alias").val();
    data.birthDate = $("#birthDate").val();
    data.gender = $("input[name='gender']:checked").val();
    data.sexOrientation = $("input[name='sexOrientation']:checked").val();
    data.latitude = coordinates.lat;
    data.longitude = coordinates.lng;
    console.log(data);

    // llamada ajax
    $.ajax({
        url: "/rsc/saveProfileData.php",
        method: "POST",
        data: { data: JSON.stringify(data) },
        success: function(response) {
            console.log("Datos guardados correctamente:", response);
        },
        error: function(xhr, status, error) {
            console.error("Error al guardar los datos:", error);
        }
    });

}