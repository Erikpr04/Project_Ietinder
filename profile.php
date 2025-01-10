<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu perfil</title>
</head>

<body>

    <main>
        <form method="post">
            <legend>Datos personales</legend>
            
            <label for="name">nombre:</label>
            <input type="text" id="name" name="name"/> <br/><br/>

            <label for="lastName">Apellidos:</label>
            <input type="text" id="lastName" name="lastName"/> <br/><br/>

            <label for="alias">Alias:</label>
            <input type="text" id="alias" name="alias"/> <br/><br/>

            <label for="birthDate">Fecha de cumpleaños:</label>
            <input type="date" id="birthDate" name="birthDate"/> <br/><br/>

            <label for="location">Localización:</label> <br/><br/>

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

            <input type="submit" value="Guardar Cambios"/>

        </form>
    </main>
    
</body>

</html>