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

// Obtener los datos del formulario
$usuarioId = $_POST["usuario"];
$precioHora = $_POST["precio_hora"];

// Validar y actualizar el precio de la hora hombre en la base de datos
if (!empty($usuarioId) && is_numeric($usuarioId) && !empty($precioHora) && is_numeric($precioHora)) {
    // Actualizar el precio en la base de datos (reemplaza con tu consulta SQL)
    $sqlActualizarPrecio = "UPDATE usuarios SET precio_hora = ? WHERE id = ?";
    $stmt = $conn->prepare($sqlActualizarPrecio);
    $stmt->bind_param("di", $precioHora, $usuarioId);

    if ($stmt->execute()) {
        // Precio actualizado correctamente
        header("Location: administrar_precios.php?mensaje=Precio actualizado correctamente");
        exit;
    } else {
        // Error al actualizar el precio
        header("Location: administrar_precios.php?error=Error al actualizar el precio");
        exit;
    }
} else {
    // Datos no válidos
    header("Location: administrar_precios.php?error=Datos no válidos");
    exit;
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
