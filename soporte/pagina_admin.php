<?php
session_start();

// Verificar si el usuario es superadministrador
if (!isset($_SESSION['es_superadmin']) || !$_SESSION['es_superadmin']) {
    header("Location: index.php");
    exit;
}

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["correo"])) {
        $correo = $_POST["correo"];

        // Conectar a la base de datos
        $conexion = new mysqli("localhost", "root", "", "soporte");

        // Verificar la conexión
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        // Verificar que el usuario exista y no sea ya un administrador
        $sql = "SELECT id FROM usuarios WHERE correo = ? AND admin = 0";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $correo);

        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                // Actualizar el usuario a administrador
                $stmt->close();
                $sql = "UPDATE usuarios SET admin = 1 WHERE correo = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("s", $correo);

                if ($stmt->execute()) {
                    $mensaje = "Usuario promovido a administrador correctamente.";
                } else {
                    $mensaje = "Error al promover al usuario.";
                }
            } else {
                $mensaje = "El usuario no existe o ya es un administrador.";
            }
        } else {
            $mensaje = "Error al verificar el usuario.";
        }

        $stmt->close();
        $conexion->close();
    } else {
        $mensaje = "No se proporcionó una dirección de correo electrónico.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Administrador</title>
    <!-- Agrega los enlaces a los estilos de Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Agregar Administrador</h1>

    <?php
    // Verificar si se ha enviado el formulario y mostrar el mensaje de éxito/error si es necesario
    if (isset($mensaje)) {
        echo '<div class="alert alert-info">' . $mensaje . '</div>';
    }
    ?>

    <form method="POST">
        <div class="form-group">
            <label for="correo">Correo Electrónico del Nuevo Administrador:</label>
            <input type="email" class="form-control" id="correo" name="correo" required>
        </div>
        <button type="submit" class="btn btn-danger">Agregar Administrador</button>
    </form>
    
    <a href="index.php" class="btn btn-danger mt-3">Volver</a>
</div>

</body>
</html>
