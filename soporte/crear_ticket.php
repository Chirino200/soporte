<!DOCTYPE html>
<?php
session_start(); // Comenzar el uso de sesiones

// Comprobar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); // Redirige al usuario a la página de inicio de sesión
    exit;
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Ticket de Soporte</title>

    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos para un diseño más formal -->
    <style>
        body {
            font-family: 'Segoe UI', 'Arial', sans-serif; /* Fuente más profesional */
            background-color: #f8f9fa; /* Fondo más claro para una apariencia limpia */
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .ticket-container {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 5px; /* Menos curvatura para un aspecto más formal */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra más sutil */
            max-width: 600px;
            width: 100%; /* Se adapta al ancho del dispositivo */
            transition: all 0.3s ease; /* Transición suave para todas las propiedades */
        }

        h1 {
            color: #333; /* Color más oscuro para el texto */
            margin-bottom: 25px;
        }

        .form-control, .btn {
            border-radius: 0.25rem; /* Menos curvatura para un aspecto más formal */
        }

        .btn-primary {
            background-color: #0056b3; /* Color azul más oscuro para un aspecto más profesional */
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #004085; /* Color azul más oscuro al pasar el mouse */
        }

        .btn-danger {
            background-color: #c82333; /* Color rojo más oscuro para un aspecto más profesional */
            color: #fff;
        }

        .btn-danger:hover {
            background-color: #bd2130; /* Color rojo más oscuro al pasar el mouse */
        }
    </style>
</head>
<body>


<div class="container ticket-container">
    <h1>Crear Ticket de Soporte</h1>

    <form method="post" action="procesar_ticket.php" enctype="multipart/form-data">
        <div class="form-group">
            <label for="categoria">Categoría:</label>
            <select class="form-control" id="categoria" name="categoria" required>
                <option value="hardware">Hardware</option>
                <option value="software">Software</option>
                <option value="nueva_instalacion">Nueva Instalación</option>
            </select>
        </div>

        <div class="form-group">
            <label for="subcategoria">Subcategoría:</label>
            <select class="form-control" id="subcategoria" name="subcategoria" required>
                <!-- Opciones para la subcategoría de Hardware -->
                <option value="computador" class="hardware">Computador</option>
                <option value="impresora" class="hardware">Impresora</option>
                <option value="reloj_control" class="hardware">Reloj Control</option>
				<option value="Celular" class="hardware">Celular</option>
                
                <!-- Opciones para la subcategoría de Software -->
                <option value="office" class="software">Office</option>
                <option value="windows" class="software">Windows</option>
                
                <!-- opciones para nueva_instalacion -->
                <option value="Computador" class="nueva_instalacion">Computador</option>
				<option value="reloj_control" class="nueva_instalacion">Reloj control</option>
				<option value="impresora" class="nueva_instalacion">Impresora</option>
				<option value="Celular" class="nueva_instalacion">Celular</option>
            </select>
        </div>

        <div class="form-group">
            <label for="detalle">Detalle del Problema:</label>
            <textarea class="form-control" id="detalle" name="detalle" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="prioridad">Prioridad:</label>
            <select class="form-control" id="prioridad" name="prioridad" required>
                <option value="Baja">Baja</option>
                <option value="Media">Media</option>
                <option value="Alta">Alta</option>
                <option value="Crítica">Crítica</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary mb-2">Enviar Ticket</button>
    </form>

    <div class="mt-3">
        <!-- Formulario separado para el botón "Salir" -->
        <form method="get" action="index.php">
            <button type="submit" class="btn btn-danger">Salir</button>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Agregar clase "show" para mostrar el formulario con animación
    const ticketContainer = document.querySelector(".ticket-container");
    ticketContainer.classList.add("show");

    // Mostrar u ocultar opciones de subcategoría según la categoría seleccionada
    const categoriaSelect = document.getElementById("categoria");
    const subcategoriaSelect = document.getElementById("subcategoria");
    const subcategoriaOptions = subcategoriaSelect.querySelectorAll("option");

    categoriaSelect.addEventListener("change", function () {
        const selectedCategoria = categoriaSelect.value;
        subcategoriaOptions.forEach(function (option) {
            // Mostrar opciones que coincidan con la categoría seleccionada
            if (option.classList.contains(selectedCategoria)) {
                option.style.display = "block";
            } else {
                option.style.display = "none";
            }
        });
    });
});
</script>
</body>
</html>
