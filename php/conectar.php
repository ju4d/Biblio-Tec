<?php
// Datos de la base de datos
$servername = "localhost"; // o la dirección IP del servidor de base de datos
$username = "root"; // tu nombre de usuario para la base de datos
$password = ""; // tu contraseña para la base de datos
$dbname = "biblioteca"; // el nombre de tu base de datos

// Crear conexión
$conexion = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Establecer el conjunto de caracteres a UTF-8
if (!$conexion->set_charset("utf8")) {
    printf("Error al cargar el conjunto de caracteres utf8: %s\n", $conexion->error);
    exit();
}
?>
