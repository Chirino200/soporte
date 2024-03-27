<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtén la dirección de correo electrónico proporcionada por el usuario
    $email = $_POST["email"];

    // Genera un token temporal (puedes usar una función más segura para esto)
    $token = bin2hex(random_bytes(32));

    // Almacena el token en la base de datos o en un archivo
    // En un escenario real, también deberías almacenar la fecha de expiración del token
    // y relacionarlo con la dirección de correo electrónico del usuario

    // Envía un correo electrónico al usuario con un enlace para restablecer la contraseña
    $subject = "Recuperación de Contraseña";
    $message = "Hola,\n\n";
    $message .= "Hemos recibido una solicitud para restablecer la contraseña de tu cuenta.\n";
    $message .= "Por favor, haz clic en el siguiente enlace para continuar:\n";
    $message .= "http://tu-sitio-web.com/restablecer_contrasena.php?token=" . $token . "\n\n";
    $message .= "Si no hiciste esta solicitud, puedes ignorar este correo electrónico.\n";
    $message .= "Este enlace de recuperación de contraseña expirará en 1 hora.\n";
    
    // Asegúrate de que los correos electrónicos se envíen en formato HTML o texto plano según tus preferencias
    $headers = "From: tu-correo@tudominio.com\r\n";
    $headers .= "Reply-To: tu-correo@tudominio.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Envía el correo electrónico
    mail($email, $subject, $message, $headers);

    // Redirige al usuario a una página de confirmación
    header("Location: confirmacion_recuperacion.php");
    exit;
}
?>
