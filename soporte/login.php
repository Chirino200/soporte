<!DOCTYPE html>
<html>
<head>
    <title>Iniciar Sesión</title>
    <!-- Añadir estilos de Bootstrap para un diseño más atractivo -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Iniciar Sesión</h1>
        <form method="post" action="procesar_login.php">
            <div class="form-group">
                <label for="correo">Correo Electrónico:</label>
                <input type="email" class="form-control" id="correo" name="correo" required>
            </div>

            <div class="form-group">
                <label for="contrasena">Contraseña:</label>
                <input type="password" class="form-control" id="contrasena" name="contrasena" required>
            </div>

            <input type="submit" class="btn btn-primary" value="Iniciar Sesión">
        </form>
        <p class="mt-3">
            ¿No tienes una cuenta? <a href="registro.php">Registrarse</a>
        </p>
        <p>
            <a href="recuperar_contrasena.php">Olvidé mi contraseña</a>
        </p>
    </div>

    <!-- Opcional: añadir scripts de Bootstrap para funcionalidades adicionales -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

