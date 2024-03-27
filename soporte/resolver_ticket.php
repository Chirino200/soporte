<?php
session_start();

// Asegurarse de que el usuario es administrador
if (!isset($_SESSION["admin"]) || $_SESSION["admin"] != 1) {
    die("Acceso denegado");
}

if (isset($_GET["id"])) {
    $ticket_id = $_GET["id"];
    
    // Conectar a la base de datos
    $conexion = new mysqli("localhost", "root", "", "soporte");

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }
    
    // Marcar ticket como resuelto
    $sql = "UPDATE tickets SET resuelto = 1 WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $ticket_id);

    
    if ($stmt->execute()) {
        echo "Ticket marcado como resuelto.";
    } else {
        echo "Error al marcar el ticket.";
    }

    $stmt->close();
    $conexion->close();
	header("Location: ver_tickets.php");  // Redirige al usuario
    exit;  // Es importante llamar a exit después de redirigir para asegurarse de que el script no continúe ejecutando
} else {
    echo "ID del ticket no proporcionado.";
}

?>
