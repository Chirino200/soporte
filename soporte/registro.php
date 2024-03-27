<!DOCTYPE html>
<html>
<head>
    <title>Registro de Usuario</title>
    <!-- Añadir estilos de Bootstrap para un diseño más atractivo -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Registro de Usuario</h1>
        <form method="post" action="procesar_registro.php">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="correo">Correo Electrónico:</label>
                <input type="email" class="form-control" id="correo" name="correo" required>
            </div>

            <div class="form-group">
                <label for="contrasena">Contraseña:</label>
                <input type="password" class="form-control" id="contrasena" name="contrasena" required pattern=".{8,}" title="8 caracteres como mínimo">
            </div>

            <div class="form-group">
                <label for="confirmar_contrasena">Confirmar Contraseña:</label>
                <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" required pattern=".{8,}" title="8 caracteres como mínimo">
            </div>

            <input type="submit" class="btn btn-primary" value="Registrarse">
            <a href="login.php" class="btn btn-link">Volver</a>
        </form>
    </div>

    <!-- Opcional: añadir scripts de Bootstrap para funcionalidades adicionales -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
