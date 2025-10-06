<?php
//echo "Solicitud recibida";
// Establecer la conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "biblioteca");

// Verificar si hubo errores en la conexión
if ($conexion->connect_error) {
    die("Error en la conexión: " . $conexion->connect_error);
}

// Obtener el ID del libro enviado desde el cliente
$id_libro = $_POST['id_libro'];

// Consultar si el libro existe en la base de datos
$sql = "SELECT * FROM libros WHERE id_libro = $id_libro";
$resultado = $conexion->query($sql);

// Verificar si se encontró el libro
if ($resultado->num_rows > 0) {
    // El libro existe
    echo "existe";
} else {
    // El libro no existe
   echo "no_existe";
}

// Cerrar la conexión a la base de datos
$conexion->close();
?>
