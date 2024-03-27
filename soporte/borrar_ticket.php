<?php
session_start();

// Comprobar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); // Redirige al usuario a la página de inicio de sesión
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "soporte";

// Obtener el ID del ticket a borrar desde la URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    header('Location: index.php'); // Redirige si no se proporciona un ID válido
    exit;
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Eliminar registros relacionados en la tabla horas_hombre
$stmtDeleteHorasHombre = $conn->prepare("DELETE FROM horas_hombre WHERE ticket_id = ?");
$stmtDeleteHorasHombre->bind_param("i", $id);

if ($stmtDeleteHorasHombre->execute()) {
    // Ahora puedes eliminar el ticket
    $stmt = $conn->prepare("DELETE FROM tickets WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Borrado exitoso, redirige de nuevo a la página de "Ver Tickets"
        header('Location: index.php');
    } else {
        echo "Error al borrar el ticket: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Error al borrar los registros relacionados en horas_hombre: " . $stmtDeleteHorasHombre->error;
}

$stmtDeleteHorasHombre->close();
$conn->close();
?>
