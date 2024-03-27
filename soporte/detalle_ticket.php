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

// Consulta SQL para obtener los detalles del ticket y el nombre del usuario
$sql = "SELECT t.categoria, t.detalle, t.prioridad, t.resuelto, t.usuario_id, t.usuario_en_proceso_id, u.nombre as nombre_usuario, u2.nombre as nombre_usuario_en_proceso, t.in_proceso_timestamp, t.realizado_timestamp, t.fecha_creacion
        FROM tickets t
        LEFT JOIN usuarios u ON t.usuario_id = u.id
        LEFT JOIN usuarios u2 ON t.usuario_en_proceso_id = u2.id
        WHERE t.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

// Obtener el usuario_id de la sesión si está disponible
if (isset($_SESSION["usuario_id"])) {
    $usuario_id = $_SESSION["usuario_id"];
} else {
    // Si el usuario no está autenticado o no tiene una sesión válida, puedes manejar esto de acuerdo a tus requerimientos
    die("Acceso denegado");
}

// Procesar el formulario de ingreso de horas hombre si se ha enviado
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["horas"])) {
    $horas = floatval($_POST["horas"]);

    // Insertar las horas hombre en la tabla horas_hombre
    $sqlInsertHorasHombre = "INSERT INTO `horas_hombre` (ticket_id, horas, usuario_id) VALUES (?, ?, ?)";
    $stmtInsertHorasHombre = $conn->prepare($sqlInsertHorasHombre);
    $stmtInsertHorasHombre->bind_param("idi", $id, $horas, $usuario_id);

    if ($stmtInsertHorasHombre->execute()) {
        // Las horas hombre se registraron correctamente
        echo "Horas hombre registradas: " . $horas;
    } else {
        // Error al registrar las horas hombre
        echo "Error al registrar las horas hombre: " . $stmtInsertHorasHombre->error;
    }

    $stmtInsertHorasHombre->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Ticket</title>

    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Animación de entrada para la tarjeta de detalle */
        .card {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        /* Estilos adicionales */
        .list-group-item {
            border: none;
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-danger text-white">
                <h2 class="mb-0">Detalle del Ticket</h2>
            </div>
            <div class="card-body">
                <?php
                if ($result->num_rows > 0):
                    $ticket = $result->fetch_assoc();
                ?>

                <ul class="list-group list-group-flush">
                    <?php if (isset($_SESSION["admin"]) && $_SESSION["admin"] == 1): ?>
                    <li class="list-group-item">
                        <strong>Creado por:</strong> <?php echo $ticket["nombre_usuario"]; ?>
                    </li>
                    <?php endif; ?>
                    <li class="list-group-item">
                        <strong>Categoría:</strong> <?php echo $ticket["categoria"]; ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Detalle:</strong> <?php echo $ticket["detalle"]; ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Prioridad:</strong> <?php echo $ticket["prioridad"]; ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Estado:</strong>
                        <?php 
                        switch ($ticket["resuelto"]) {
                            case '0':
                                echo "<span class='badge badge-primary'>Nuevo</span>";
                                break;
                            case '2':
                                echo "<span class='badge badge-warning'>En Proceso</span>";
                                break;
                            case '1':
                                echo "<span class='badge badge-success'>Realizado</span>";
                                break;
                            default:
                                echo "<span class='badge badge-secondary'>" . $ticket["resuelto"] . "</span>";
                                break;
                        }
                        ?>
                    </li>
                    <?php if ($ticket["resuelto"] == 2): ?>
                    <li class="list-group-item">
                        <strong>En Proceso por:</strong> <?php echo $ticket["nombre_usuario_en_proceso"]; ?>
                    </li>
                    <?php endif; ?>
                   
                    <?php if ($ticket["resuelto"] == 1): ?>
                    <li class="list-group-item">
                        <strong>Tiempo empleado:</strong>
                        <?php
                        $fechaCreacion = new DateTime($ticket["in_proceso_timestamp"]);
                        $fechaRealizado = new DateTime($ticket["realizado_timestamp"]);
                        $diferencia = $fechaCreacion->diff($fechaRealizado);
                        $tiempoEmpleado = $diferencia->format("%d días, %h horas, %i minutos, %s segundos");
                        echo $tiempoEmpleado;
                        ?>
                    </li>
                    <?php endif; ?>
                </ul>
				<?php if (isset($_SESSION["admin"]) && $_SESSION["admin"] == 1): ?>
                <!-- Agrega el formulario de ingreso de horas hombre -->
                <div class="mt-3">
                    <form method="post" action="detalle_ticket.php?id=<?php echo $id; ?>">
                        <div class="form-group">
                            <label for="horas">Horas Hombre:</label>
                            <input type="number" class="form-control" id="horas" name="horas" step="0.01" required>
                        </div>
                        <button type="submit" class="btn btn-danger">Registrar Horas Hombre</button>
                    </form>
                </div>
				<?php endif; ?>
                <div class="mt-3">
                    <?php if (isset($_SESSION["admin"]) && $_SESSION["admin"] == 1): ?>
                    <?php if ($ticket["resuelto"] == 1): ?>
                    <a href="no_resolver_ticket.php?id=<?php echo $id; ?>" class="btn btn-warning">Marcar como nuevo</a>
                    <?php else: ?>
                    <a href="resolver_ticket.php?id=<?php echo $id; ?>" class="btn btn-success">Marcar como resuelto</a>
                    <?php endif; ?>
                    <a href="actualizar_estado_ticket.php?id=<?php echo $id; ?>" class="btn btn-warning ml-2">En Proceso</a>
                    <a href="#" id="imprimirTicketBtn" class="btn btn-danger">Imprimir Ticket</a>
                    <?php endif; ?>
                    <a href="index.php" class="btn btn-danger ml-2">Volver</a>
                </div>

                <?php
                else:
                    echo "<p class='alert alert-warning'>Ticket no encontrado.</p>";
                endif;

                $stmt->close();
                $conn->close();
                ?>
            </div>
        </div>
    </div>

    <!-- Script para activar la animación de entrada después de que la página se haya cargado -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const card = document.querySelector(".card");
            if (card) {
                setTimeout(function() {
                    card.style.opacity = "1";
                    card.style.transform = "translateY(0)";
                }, 100); // Retraso de 100ms para la animación de entrada
            }
        });

        // Script para imprimir el ticket al hacer clic en el botón
        document.getElementById("imprimirTicketBtn").addEventListener("click", function() {
            window.print(); // Abre la ventana de impresión del navegador
        });

        // Temporizador para el tiempo en proceso
    </script>
</body>

</html>
