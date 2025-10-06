<?php
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir archivo de conexión a la base de datos
require 'conectar.php';

// Array para almacenar las opciones
$options = [];

// Obtener las opciones de la tabla proveedor
$sql_editoriales = "SELECT id_editorial, nombre FROM proveedor";
$result_editoriales = $conexion->query($sql_editoriales);
if ($result_editoriales) {
    while ($row = $result_editoriales->fetch_assoc()) {
        $options['editoriales'][] = $row;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error al obtener las editoriales: ' . $conexion->error]);
    exit;
}

// Obtener las opciones de la tabla genero
$sql_generos = "SELECT id_genero, nombre FROM genero";
$result_generos = $conexion->query($sql_generos);
if ($result_generos) {
    while ($row = $result_generos->fetch_assoc()) {
        $options['generos'][] = $row;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error al obtener los géneros: ' . $conexion->error]);
    exit;
}

// Cerrar la conexión
$conexion->close();

// Devolver las opciones como JSON
echo json_encode(['success' => true, 'options' => $options]);
?>
