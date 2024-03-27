<?php
session_start();  // Comenzar el uso de sesiones

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "soporte";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$mensaje = '';
$tipo_mensaje = 'alert-danger';  // Por defecto, el mensaje es un error

// Verificar si hemos recibido datos POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recopilar datos del formulario
    $categoria = $_POST['categoria'];
    $subcategoria = $_POST['subcategoria'];
    $detalle = $_POST['detalle'];
    $prioridad = $_POST['prioridad'];

    // Verificar si el usuario ha iniciado sesión
    if (isset($_SESSION['usuario_id'])) {
        $usuarioId = $_SESSION['usuario_id'];

        // Insertar el nuevo ticket en la base de datos usando sentencias preparadas
        $stmt = $conn->prepare("INSERT INTO tickets (categoria, subcategoria, detalle, prioridad, usuario_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $categoria, $subcategoria, $detalle, $prioridad, $usuarioId);

        if ($stmt->execute()) {
            $mensaje = "Ticket creado con éxito.";
            $tipo_mensaje = 'alert-success';
        } else {
            $mensaje = "Error al crear el ticket: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $mensaje = "Debes iniciar sesión para crear un ticket.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de Creación de Ticket</title>

    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="alert <?php echo $tipo_mensaje; ?>" role="alert">
        <?php echo $mensaje; ?>
    </div>
    <a href='index.php' class="btn btn-primary">Salir</a>
</div>

</body>
</html>
