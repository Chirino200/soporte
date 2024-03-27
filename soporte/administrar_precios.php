<?php
session_start();

// Verificar si el usuario es superadmin
if (!isset($_SESSION["es_superadmin"]) || !$_SESSION["es_superadmin"]) {
    header("Location: acceso_denegado.php"); // Redireccionar si no es superadmin
    exit;
}

// Conexión a la base de datos (reemplaza con tus datos)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "soporte";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta SQL para obtener la lista de usuarios admin
$sqlUsuarios = "SELECT id, nombre FROM usuarios WHERE admin = 1";
$resultUsuarios = $conn->query($sqlUsuarios);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Precios de Hora Hombre</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Administrar Precios de Hora Hombre</h1>
        
        <form action="procesar_precios.php" method="post">
            <div class="form-group">
                <label for="usuario">Seleccionar Admin:</label>
                <select class="form-control" name="usuario" id="usuario">
                    <?php
                    while ($row = $resultUsuarios->fetch_assoc()) {
                        echo "<option value='" . $row["id"] . "'>" . $row["nombre"] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="precio_hora">Precio por Hora:</label>
                <input type="text" class="form-control" name="precio_hora" id="precio_hora">
            </div>
            <button type="submit" class="btn btn-danger">Guardar Precio</button>
            <div>
                <a href="index.php" class="btn btn-danger mt-3">Volver</a>
            </div>
        </form>
    </div>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$conn->close();
?>
