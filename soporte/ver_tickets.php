<?php
session_start();  // Comenzar el uso de sesiones

// Función para obtener la clase CSS según la prioridad
function getPriorityClass($prioridad) {
    switch($prioridad) {
        case 'Baja':
            return 'bg-success text-white';
        case 'Media':
            return 'bg-warning';
        case 'Alta':
            return 'bg-danger text-white';
        case 'Crítica':
            return 'bg-dark text-white';
        default:
            return '';
    }
}

// Comprobar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');  // Redirige al usuario a la página de inicio de sesión
    exit;  // Termina la ejecución del script
}

$esAdmin = isset($_SESSION["admin"]) && $_SESSION["admin"] == 1;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "soporte";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$usuarioId = $_SESSION["usuario_id"];  // Obtener el ID del usuario actual

// Consulta SQL para obtener los tickets del usuario actual (o todos los tickets si es admin)
if ($esAdmin) {
    $sql = "SELECT tickets.id, tickets.categoria, tickets.subcategoria, tickets.detalle, tickets.prioridad, tickets.resuelto, fecha_creacion, usuarios.nombre AS creado_por, 
                   usuarios_en_proceso.nombre AS en_proceso_por
            FROM tickets
            LEFT JOIN usuarios ON tickets.usuario_id = usuarios.id
            LEFT JOIN usuarios AS usuarios_en_proceso ON tickets.usuario_en_proceso_id = usuarios_en_proceso.id
            ORDER BY 
                CASE
                    WHEN tickets.resuelto = 0 THEN 1
                    WHEN tickets.resuelto = 2 THEN 2
                    WHEN tickets.resuelto = 1 THEN 3
                    ELSE 4
                END,
                CASE
                    WHEN tickets.prioridad = 'Crítica' THEN 1
                    WHEN tickets.prioridad = 'Alta' THEN 2
                    WHEN tickets.prioridad = 'Media' THEN 3
                    WHEN tickets.prioridad = 'Baja' THEN 4
                    ELSE 5
                END";
} else {
    $sql = "SELECT tickets.id, tickets.categoria, tickets.subcategoria, tickets.detalle, tickets.prioridad, tickets.resuelto, fecha_creacion, usuarios.nombre AS creado_por, 
               usuarios_en_proceso.nombre AS en_proceso_por
        FROM tickets
        LEFT JOIN usuarios ON tickets.usuario_id = usuarios.id
        LEFT JOIN usuarios AS usuarios_en_proceso ON tickets.usuario_en_proceso_id = usuarios_en_proceso.id
        WHERE tickets.usuario_id = $usuarioId
        ORDER BY 
            CASE
                WHEN tickets.resuelto = 0 THEN 1
                WHEN tickets.resuelto = 2 THEN 2
                WHEN tickets.resuelto = 1 THEN 3
                ELSE 4
            END,
            CASE
                WHEN tickets.prioridad = 'Crítica' THEN 1
                WHEN tickets.prioridad = 'Alta' THEN 2
                WHEN tickets.prioridad = 'Media' THEN 3
                WHEN tickets.prioridad = 'Baja' THEN 4
                ELSE 5
            END";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets Registrados</title>
    <!-- Agrega los enlaces a los estilos de Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Agrega estilos para la animación de entrada de los tickets */
        .ticket-entry {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        /* Agrega estilos para la animación de salida de los tickets */
        .ticket-exit {
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }
        
        /* Redefine el espacio entre filas */
        .table td, .table th {
            padding: 0.5rem 0.75rem;
            vertical-align: middle;
        }
        
        /* Hace que el texto de la columna "Detalle" sea más corto */
        .ticket-detail {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Tickets Registrados</h1>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <?php if ($esAdmin): ?>
                        <th>Creado por</th>
                    <?php endif; ?>
                    <th>Categoría</th>
                    <th>Subcategoría</th> <!-- Nueva columna de subcategoría -->
                    <th>Prioridad</th>
                    <th>Fecha de Creación</th>
                    <th>Estado</th>
                    <th>En Proceso por</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>

            <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="ticket-entry" id="ticket-<?php echo $row["id"]; ?>">
                    <?php if ($esAdmin): ?>
                        <td><?php echo $row["creado_por"]; ?></td>
                    <?php endif; ?>
                    <td><?php echo $row["categoria"]; ?></td>
                    <td><?php echo $row["subcategoria"]; ?></td> <!-- Mostrar subcategoría -->
                    <td class="<?php echo getPriorityClass($row["prioridad"]); ?>"><?php echo $row["prioridad"]; ?></td>
                    <td><?php echo $row["fecha_creacion"]; ?></td>
                    <td>
                        <?php 
                        switch ($row["resuelto"]) {
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
                                echo "<span class='badge badge-secondary'>" . $row["resuelto"] . "</span>";
                                break;
                        }
                        ?>
                    </td>
                    <td><?php echo $esAdmin ? $row["en_proceso_por"] : ''; ?></td>
                    <td>
                        <a href='detalle_ticket.php?id=<?php echo $row["id"]; ?>' class="btn btn-info">Ver Detalle</a>
                        <?php if ($_SESSION["usuario_id"] == $row["id"] || $esAdmin): ?>
                            <a href='borrar_ticket.php?id=<?php echo $row["id"]; ?>' class="btn btn-danger"
                                onclick="return confirm('¿Estás seguro de que deseas borrar este ticket?'); eliminarTicket('ticket-<?php echo $row["id"]; ?>')">Borrar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>

            </tbody>
        </table>
    <?php else: ?>
        <p class='alert alert-warning'>No hay tickets registrados.</p>
    <?php endif; ?>

    <a href="index.php" class="btn btn-danger mt-3">Volver</a>
</div>

<!-- Scripts de Bootstrap (opcionales) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    // Función para eliminar un ticket con animación de salida
    function eliminarTicket(ticketId) {
        const ticket = document.getElementById(ticketId);
        if (ticket) {
            // Aplicar animación de salida
            ticket.classList.add("ticket-exit");
            setTimeout(() => {
                // Eliminar el elemento del DOM después de la animación
                ticket.remove();
            }, 500); // Tiempo de duración de la animación (ms)
        }
    }

    // Función para activar las animaciones cuando se cargue la página
    document.addEventListener("DOMContentLoaded", function() {
        const tickets = document.querySelectorAll(".ticket-entry");
        tickets.forEach(function(ticket, index) {
            setTimeout(function() {
                ticket.style.opacity = "1";
                ticket.style.transform = "translateY(0)";
            }, index * 100); // Retraso de animación de 100ms entre cada ticket
        });
    });
</script>

</body>
</html>
