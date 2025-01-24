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
    data.gender = $("#sexe").val();
    data.sexOrientation = $("#orientation").val();
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

document.addEventListener("DOMContentLoaded", () => {
    const menuButton = document.querySelector('.menu-button');
    const menu = document.getElementById('menu-content');

    function toggleMenu() {
        menu.classList.toggle('active');
        menuButton.classList.toggle('rotated');
    }

    if (menuButton) {
        menuButton.addEventListener('click', (event) => {
            toggleMenu();
        });
    } else {
        console.error("El botón del menú no se encontró.");
    }

    document.getElementById("logout-button").addEventListener("click", function() {
        // Eliminar la cookie user_id
        document.cookie = "user_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        
        // Redirigir a index.php
        window.location.href = "./index.php";
    });

    const deleteObject = document.getElementById("deleteInput");
    document.getElementById("deleteButton").addEventListener("click", function() {
        console.log(deleteObject.value);
        if (deleteObject.value=="BORRAR"){
        $.ajax({
            url: 'rsc/delete-account.php',
            method: 'POST',
            success: function (data) {

                console.log(data);


                // Eliminar la cookie user_id
                document.cookie = "user_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                
                // Redirigir a index.php
                window.location.href = "./index.php";
                
            },
            error: function(xhr, status, error) {
                console.error('AJAX request failed:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
                try {
                    const errorData = JSON.parse(xhr.responseText);
                    console.error('Server error:', errorData.error);
                } catch (e) {
                    console.error('Could not parse error response:', xhr.responseText);
                }
            }
        });
    }

    });

    const notification = document.getElementById("matchOverlay");
    
    
    document.getElementById("logout-button").addEventListener("click", function() {
        notification.style.visibility="visible";
    });
    document.getElementById("delete-account-button").addEventListener("click", function() {
        notification.style.visibility="visible";

    });

    document.getElementById("backButton").addEventListener("click", function() {
        notification.style.visibility="hidden";

    });



    
});
