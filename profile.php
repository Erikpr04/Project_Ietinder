<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- api maps
    <script async defer 
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDlpD9X2jl0jwfH92yjVCIw2y_ecoVmWRA&callback=initMap"> 
    </script>
    <style> #map { height: 100%; } html, body { height: 100%; margin: 0; padding: 0; } </style>  -->
    
    <script src="profile.js"></script>

    <title>Tu perfil</title>
</head>

<body>

    <main>
        <form method="post">
            <legend>Datos personales</legend> <br/><br/>
            
            <label for="name">nombre:</label>
            <input type="text" id="name" name="name"/> <br/><br/>

            <label for="lastName">Apellidos:</label>
            <input type="text" id="lastName" name="lastName"/> <br/><br/>

            <label for="alias">Alias:</label>
            <input type="text" id="alias" name="alias"/> <br/><br/>

            <label for="birthDate">Fecha de cumpleaños:</label>
            <input type="date" id="birthDate" name="birthDate"/> <br/><br/>

            <label for="location">Localización:</label> <br/><br/>

            <iframe
                width="450"
                height="250"
                frameborder="0" style="border:0"
                referrerpolicy="no-referrer-when-downgrade"
                src="https://www.google.com/maps/embed/v1/view?key=AIzaSyDlpD9X2jl0jwfH92yjVCIw2y_ecoVmWRA
                    &center=41.357273232584475, 2.0780836830809464
                    &zoom=15"
                allowfullscreen>
            </iframe>
            <br/><br/>

            <label for="sexe">Sexo:</label>
            <input type="radio" name="gender" id="male" value="male">
            <label for="male">Home</label>
            <input type="radio" name="gender" id="women" value="women">
            <label for="women">Dona</label>
            <input type="radio" name="gender" id="nonBinary" value="nonBinary">
            <label for="nonBinary">No binaria</label> <br/><br/>

            <label for="sexe">Horienzación sexual:</label>
            <input type="radio" name="sexOrientation" id="heterosexual" value="heterosexual">
            <label for="heterosexual">Heterosexual</label>
            <input type="radio" name="sexOrientation" id="homosexual" value="homosexual">
            <label for="homosexual">Homoseuxal</label>
            <input type="radio" name="sexOrientation" id="bisexual" value="bisexual">
            <label for="bisexual">Bisexual</label> <br/><br/>

            <label for="mail">Email:</label>
            <input type="email" id="mail" name="mail" disabled style="cursor:not-allowed"> <br/><br/>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" disabled style="cursor:not-allowed"> <br/><br/>

            <input type="submit"  onclick="return false" value="Guardar Cambios"/>

        </form>
    </main>
    
</body>

</html>