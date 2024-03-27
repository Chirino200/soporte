<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

// Realiza la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "soporte";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el ID de usuario de la sesión
$usuarioId = $_SESSION["usuario_id"];

// Consulta SQL para obtener el nombre de usuario
$sqlNombreUsuario = "SELECT nombre FROM usuarios WHERE id = ?";
$stmtNombreUsuario = $conn->prepare($sqlNombreUsuario);
$stmtNombreUsuario->bind_param("i", $usuarioId);
$stmtNombreUsuario->execute();
$resultNombreUsuario = $stmtNombreUsuario->get_result();

if ($resultNombreUsuario->num_rows > 0) {
    $rowNombreUsuario = $resultNombreUsuario->fetch_assoc();
    $_SESSION["nombre_usuario"] = $rowNombreUsuario["nombre"];
}

// Consulta SQL para obtener el número total de tickets
$sqlTotalTickets = "SELECT COUNT(*) AS total_tickets FROM tickets";
$resultTotalTickets = $conn->query($sqlTotalTickets);
$totalTickets = ($resultTotalTickets->num_rows > 0) ? $resultTotalTickets->fetch_assoc()["total_tickets"] : 0;

// Consulta SQL para obtener el número de tickets abiertos
$sqlTicketsAbiertos = "SELECT COUNT(*) AS tickets_abiertos FROM tickets WHERE resuelto = '1'";
$resultTicketsAbiertos = $conn->query($sqlTicketsAbiertos);
$ticketsAbiertos = ($resultTicketsAbiertos->num_rows > 0) ? $resultTicketsAbiertos->fetch_assoc()["tickets_abiertos"] : 0;

// Consulta SQL para obtener el número de tickets nuevos
$sqlTicketsNuevos = "SELECT COUNT(*) AS tickets_nuevos FROM tickets WHERE resuelto = '0'";
$resultTicketsNuevos = $conn->query($sqlTicketsNuevos);
$ticketsNuevos = ($resultTicketsNuevos->num_rows > 0) ? $resultTicketsNuevos->fetch_assoc()["tickets_nuevos"] : 0;

// Consulta SQL para obtener el número de tickets en proceso
$sqlTicketsEnProceso = "SELECT COUNT(*) AS tickets_en_proceso FROM tickets WHERE resuelto = '2'";
$resultTicketsEnProceso = $conn->query($sqlTicketsEnProceso);
$ticketsEnProceso = ($resultTicketsEnProceso->num_rows > 0) ? $resultTicketsEnProceso->fetch_assoc()["tickets_en_proceso"] : 0;

// Consulta SQL para obtener los tickets del usuario actual con categoría y subcategoría
$sqlMisTickets = "SELECT t.id, t.prioridad, t.resuelto, t.usuario_id, t.usuario_en_proceso_id, t.categoria, t.subcategoria, u.nombre as nombre_usuario, u2.nombre as nombre_usuario_en_proceso
                  FROM tickets t
                  LEFT JOIN usuarios u ON t.usuario_id = u.id
                  LEFT JOIN usuarios u2 ON t.usuario_en_proceso_id = u2.id
                  WHERE t.usuario_id = ?
                  ORDER BY t.prioridad";

$stmt = $conn->prepare($sqlMisTickets);
$stmt->bind_param("i", $usuarioId);
$stmt->execute();
$resultMisTickets = $stmt->get_result();
$esAdmin = isset($_SESSION["admin"]) && $_SESSION["admin"];

// Función para obtener la clase CSS según la prioridad
function getPriorityClass($prioridad) {
    switch($prioridad) {
        case 'Baja':
            return 'text-success';
        case 'Media':
            return 'text-warning';
        case 'Alta':
            return 'text-danger';
        case 'Crítica':
            return 'text-dark';
        default:
            return '';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soporte</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Tu hoja de estilos personalizada -->
    <link href="css/theme.css" rel="stylesheet" type="text/css">
    
    <!-- Estilos para ocupar toda la pantalla y un diseño más formal -->
    <style>
        html, body {
            height: 100%;
            color: #333; /* Color más oscuro para el texto */
            background-color: #f4f4f4; /* Fondo más claro para una apariencia limpia */
        }

        .container-full {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .container-content {
            flex: 1;
            overflow-y: auto;
        }

        .user-info-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0 20px;
            padding: 20px 0;
            background-color: #e9ecef; /* Fondo claro para la cabecera */
            border-bottom: 1px solid #dee2e6; /* Borde para separar la sección */
        }

        .user-info {
            text-align: right;
        }

        .btn-logout {
            margin-top: 10px;
            background-color: #dc3545; /* Rojo Bootstrap para botón de cerrar sesión */
            color: white; /* Texto blanco para contraste */
        }

        /* Estilización de botones para ser más formales */
        .btn {
            border-radius: 2px; /* Menos curvatura para un aspecto más formal */
            box-shadow: none; /* Eliminar sombra para limpieza */
        }

        /* Estilización de la tabla */
        .table {
            border-collapse: collapse; /* Bordes conectados para un look limpio */
        }

        .table thead th {
            background-color: #343a40; /* Fondo oscuro para el encabezado de la tabla */
            color: #fff; /* Texto blanco para el encabezado */
        }

        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2; /* Alternar colores para filas para mejor legibilidad */
        }

        .badge {
            padding: .5em .75em; /* Ajustar el relleno para los badges */
        }

        /* Colores específicos para los estados de los tickets */
        .badge-primary {
            background-color: #007bff;
        }

        .badge-warning {
            background-color: #ffc107;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-secondary {
            background-color: #6c757d;
        }

        /* Colores y estilos para botones de acción */
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

    </style>
</head>
<body>

<div class="container-full">
    <div class="user-info-container">
        <h1 class="mb-0">Bienvenido al Soporte</h1>
        <div class="user-info">
            <p>Bienvenido <?php echo isset($_SESSION["nombre_usuario"]) ? $_SESSION["nombre_usuario"] : 'Nombre de Usuario no disponible'; ?></p>
            <p>
                Eres un 
                <?php 
                if (isset($_SESSION["es_superadmin"]) && $_SESSION["es_superadmin"]) {
                    echo "Superadmin";
                } elseif (isset($_SESSION["admin"]) && $_SESSION["admin"]) {
                    echo "Admin";
                } else {
                    echo "Usuario";
                }
                ?>
            </p>
        
            <a href="cerrar_sesion.php" class="btn btn-danger btn-logout">Cerrar sesión</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card-body">
                <hr>
                <h2 class="mb-4 text-center">Opciones de Usuario</h2>
                <!-- Otras Opciones de Usuario -->
                <div class="text-left mb-4">
                    <ul class="list-unstyled">
                        <li><a href="crear_ticket.php" class="btn btn-danger mb-2">Crear Ticket</a></li> 
                        <?php if ($esAdmin): ?>
                            <li><a href="ver_tickets.php" class="btn btn-danger mb-2">Ver tickets</a></li>
                        <?php endif; ?>
                        <?php if (isset($_SESSION["es_superadmin"]) && $_SESSION["es_superadmin"]) : ?>                        
                            <li><a href="pagina_admin.php" class="btn btn-danger mb-2">Agregar Administrador</a></li>                        
                        <?php endif; ?>                  
                        <?php if (isset($_SESSION["es_superadmin"]) && $_SESSION["es_superadmin"]) : ?>                
                            <li><a href="ver_horas_hombre.php" class="btn btn-danger mb-2">Ver Horas Hombre</a></li>
                            <li><a href="administrar_precios.php" class="btn btn-danger">Precios Horas Hombre</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <!-- Tickets del Usuario -->
            <h2 class="mb-4 text-center">Mis Tickets</h2>
            <?php if ($resultMisTickets->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Categoría</th>
                                <th>Subcategoría</th>
                                <th>Prioridad</th>
                                <th>Estado</th>
                                <th>Usuario</th>
                                <th>En Proceso por</th>
                                <th>Acción</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $ticketsNuevos = [];
                            $ticketsEnProceso = [];
                            $ticketsRealizados = [];

                            while ($row = $resultMisTickets->fetch_assoc()) {
                                if ($row["resuelto"] == '0') {
                                    $ticketsNuevos[] = $row;
                                } elseif ($row["resuelto"] == '2') {
                                    $ticketsEnProceso[] = $row;
                                } elseif ($row["resuelto"] == '1') {
                                    $ticketsRealizados[] = $row;
                                }
                            }

                            // Ordenar los tickets: Nuevos, En Proceso, Realizados
                            $sortedTickets = array_merge($ticketsNuevos, $ticketsEnProceso, $ticketsRealizados);

                            foreach ($sortedTickets as $row) :
                            ?>
                                <tr class="ticket-entry" id="ticket-<?php echo $row["id"]; ?>">
                                    <td><?php echo isset($row["categoria"]) ? $row["categoria"] : ''; ?></td>
                                    <td><?php echo isset($row["subcategoria"]) ? $row["subcategoria"] : ''; ?></td>
                                    <td class="<?php echo getPriorityClass($row["prioridad"]); ?>"><?php echo $row["prioridad"]; ?></td>
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
                                    <td><?php echo $row["nombre_usuario"]; ?></td>
                                    <td>
                                        <?php
                                        if ($row["usuario_en_proceso_id"] !== null) {
                                            echo $row["nombre_usuario_en_proceso"];
                                        } else {
                                            echo "No asignado";
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href='detalle_ticket.php?id=<?php echo $row["id"]; ?>' class="btn btn-danger">Ver Detalle</a>
                                    </td>
                                    <td>
                                        <a href='borrar_ticket.php?id=<?php echo $row["id"]; ?>' class="btn btn-danger"
                                        onclick="return confirm('¿Estás seguro de que deseas borrar este ticket?');">Borrar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class='alert alert-warning text-center'>No tienes tickets registrados.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$conn->close();
?>
