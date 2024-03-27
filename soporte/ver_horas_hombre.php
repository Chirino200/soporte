<?php
session_start();

// Verifica si el usuario ha iniciado sesión y es superadmin
if (!(isset($_SESSION["es_superadmin"]) && $_SESSION["es_superadmin"])) {
    header("Location: index.php"); // Redirige a la página principal si no es superadmin
    exit;
}

// Realiza la conexión a la base de datos (ajusta los datos de conexión)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "soporte";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta SQL para obtener los administradores que tienen horas hombre registradas y el total de horas de cada administrador
$sql = "SELECT u.id, u.nombre as nombre_usuario, u.precio_hora, SUM(hh.horas) as total_horas
        FROM usuarios u
        LEFT JOIN horas_hombre hh ON u.id = hh.usuario_id
        WHERE u.admin = 1
        GROUP BY u.id";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Horas Hombre de Administradores</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Enlaza tu archivo CSS personalizado aquí -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Agrega Bootstrap para estilos básicos -->
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Administradores con Horas Hombre Registradas</h1>
    
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Precio por Hora</th>
                    <th>Total de Horas</th>
                    <th>Total de Dinero</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row["nombre_usuario"]; ?></td>
                        <td><?php echo $row["precio_hora"]; ?> CLP</td>
                        <td><?php echo $row["total_horas"]; ?></td>
                        <td>
                            <?php
                            $precioHora = $row["precio_hora"];
                            $totalHoras = $row["total_horas"];
                            $totalDinero = $totalHoras * $precioHora;
                            echo $totalDinero . " CLP";
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se encontraron administradores con horas hombre registradas.</p>
    <?php endif; ?>

    <a href="index.php" class="btn btn-danger">Volver</a>
</div>

</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$conn->close();
?>
