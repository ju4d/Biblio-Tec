<?php
header('Content-Type: application/json');

$conexion = new mysqli('localhost', 'root', '', 'biblioteca');

if ($conexion->connect_error) {
    die(json_encode(array('success' => false, 'message' => "Conexión fallida: " . $conexion->connect_error)));
}

$id_cliente = isset($_GET['id_cliente']) ? intval($_GET['id_cliente']) : 0;

if ($id_cliente <= 0) {
    echo json_encode(array('success' => false, 'message' => 'ID del cliente no válido.'));
    exit();
}

$query =    "SELECT libros.id_libro, libros.titulo, libros.autor, libros.edicion, prestamo.fecha_entrega 
            FROM libros 
            JOIN prestamo_libro ON libros.id_libro = prestamo_libro.id_libro 
            JOIN prestamo ON prestamo_libro.id_prestamo = prestamo.id_prestamo 
            JOIN cliente ON prestamo.id_cliente = cliente.id_cliente 
            WHERE cliente.id_cliente = ? AND prestamo_libro.status = 0";
$stmt = $conexion->prepare($query);
if ($stmt) {
    $stmt->bind_param('i', $id_cliente);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $libros = array();
    while ($fila = $resultado->fetch_assoc()) {
        $libros[] = $fila;
    }

    if (count($libros) > 0) {
        echo json_encode(array('success' => true, 'libros' => $libros));
    } else {
        echo json_encode(array('success' => false, 'message' => 'No se encontraron libros prestados para este cliente.'));
    }

    $stmt->close();
} else {
    echo json_encode(array('success' => false, 'message' => 'Error en la preparación de la consulta.'));
}

$conexion->close();
?>
