<?php
session_start(); // Iniciar la sesión al principio para evitar errores de encabezado

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conectar a la base de datos
    $conexion = new mysqli("localhost", "root", "", "soporte");

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Obtener datos del formulario
    $correo = $_POST["correo"];
    $contrasena = $_POST["contrasena"];

    // Modificar la consulta SQL para recuperar también el valor de si el usuario es administrador y superadministrador
    $sql = "SELECT id, contrasena, admin, es_superadmin FROM usuarios WHERE correo = ?";
    
    if($stmt = $conexion->prepare($sql)) {
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();

        if ($usuario && password_verify($contrasena, $usuario["contrasena"])) {
            // Iniciar sesión y redirigir al usuario
            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION['loggedin'] = true; // Esto indica que el usuario ha iniciado sesión
            $_SESSION["admin"] = $usuario["admin"]; // Guarda el valor de administrador en la sesión
            $_SESSION['es_superadmin'] = $usuario["es_superadmin"];
            
            header("Location: index.php"); // Redirige a la página de inicio
            exit;
        } else {
            $error_message = "Correo o contraseña incorrectos. <a href='login.php'>Volver a intentar</a>";
        }

        $stmt->close();
    } else {
        $error_message = "Hubo un problema al intentar acceder a la base de datos. Por favor, inténtelo más tarde.";
    }

    $conexion->close();
}
?>

<?php
// ... (Mismo código PHP de arriba)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilos personalizados */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
        }

        .login-container {
            max-width: 400px;
            margin: 5% auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0px 0px 10px 0px #aaa;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>

        <?php
        if (isset($error_message)) {
            echo "<div class='alert alert-danger'>$error_message</div>";
        }
        ?>
    </div>
</body>
</html>



