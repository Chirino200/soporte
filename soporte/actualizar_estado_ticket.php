<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "soporte";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$id = $_GET['id'];

// Obtener el usuario_id de la sesión si está disponible
if (isset($_SESSION["usuario_id"])) {
    $usuario_id = $_SESSION["usuario_id"];
} else {
    // Si el usuario no está autenticado o no tiene una sesión válida, puedes manejar esto de acuerdo a tus requerimientos
    die("Acceso denegado");
}

// Obtener el timestamp actual en segundos
$timestamp = time(); // Esto obtiene el timestamp actual en segundos

// Convertir el timestamp a una cadena de fecha y hora
$fechaHora = date("Y-m-d H:i:s", $timestamp);

// Query SQL para actualizar el ticket y establecer in_proceso_timestamp y usuario_en_proceso_id
$sql = "UPDATE tickets SET resuelto = 2, in_proceso_timestamp = ?, usuario_en_proceso_id = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $fechaHora, $usuario_id, $id);

if ($stmt->execute()) {
    // Actualización exitosa
    header("Location: detalle_ticket.php?id=" . $id); // Redirige de nuevo a la página de detalles
} else {
    // Manejar errores si la actualización falla
    echo "Error al actualizar el ticket: " . $stmt->error;
}

// Cerrar la conexión y el statement
$stmt->close();
$conn->close();
?>
