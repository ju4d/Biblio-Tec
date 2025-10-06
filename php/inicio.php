<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: login.php'); // Redirigir a la página de inicio de sesión si el usuario no ha iniciado sesión
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
</head>
<body>
    <h1>Bienvenido <?php echo $_SESSION['email']; ?></h1>
    <a href="logout.php">Cerrar sesión</a>
</body>
</html>
