<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    header('Location: login.php');
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "soporte";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$id = $_GET['id'];

$stmt = $conn->prepare("UPDATE tickets SET resuelto = 0 WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    header('Location: detalle_ticket.php?id=' . $id . '&status=updated');
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
