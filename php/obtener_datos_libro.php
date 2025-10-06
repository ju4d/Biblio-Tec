<?php
// Establecer la conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "biblioteca");

// Verificar si hubo errores en la conexión
if ($conexion->connect_error) {
    die("Error en la conexión: " . $conexion->connect_error);
}

// Obtener el ID del libro enviado desde el cliente
$id_libro = $_POST['id_libro'];
    
// Consultar los datos del libro en la base de datos
$sql = "SELECT ID_libro, titulo, autor, precio FROM libros WHERE ID_libro = $id_libro";
$resultado = $conexion->query($sql);

// Verificar si se encontró el libro
if ($resultado->num_rows > 0) {
    // El libro existe, devolver los datos del libro
    $datos_libro = $resultado->fetch_assoc();
    echo json_encode(array(
        'success' => true,
        'data' => $datos_libro
    ));
} else {
    // El libro no existe
    echo json_encode(array(
        'success' => false,
        'message' => 'El libro no existe en la base de datos'
    ));
}

// Cerrar la conexión a la base de datos
$conexion->close();
?>
