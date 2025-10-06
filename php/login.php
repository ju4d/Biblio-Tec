<?php
// Conexión a la base de datos
$conexion = mysqli_connect("localhost", "usuario", "contraseña", "biblioteca");

// Verificar conexión
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Obtener datos del formulario
$email = $_POST['email'];
$contraseña = $_POST['contraseña'];

// Consulta SQL para verificar las credenciales
$consulta = "SELECT * FROM usuarios WHERE email='$email' AND contraseña='$contraseña'";
$resultado = mysqli_query($conexion, $consulta);

// Verificar si se encontró un usuario con las credenciales proporcionadas
if (mysqli_num_rows($resultado) == 1) {
    // Inicio de sesión exitoso
    session_start();
    $_SESSION['email'] = $email;
    header('Location: perfil.php'); // Redirigir a la página de perfil del usuario
} else {
    // Credenciales incorrectas
    echo "Correo electrónico o contraseña incorrectos";
}

// Cerrar conexión
mysqli_close($conexion);
?>
