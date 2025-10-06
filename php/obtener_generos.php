<?php
/*
// Incluir archivo de conexión a la base de datos
//require 'conectar.php';

// Consulta SQL para obtener los géneros
$sql_generos = "SELECT id_genero, nombre FROM genero";
$resultado = $conexion->query($sql_generos);

$generos = [];
if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $generos[] = $row;
    }
}

// Devolver los géneros como un arreglo JSON
echo json_encode($generos);

// Cerrar la conexión
$conexion->close();
?>
*/

// Conectar a la base de datos
require 'conectar.php';

// Consultar los géneros
$sql = "SELECT id_genero, nombre FROM genero";
$result = $conexion->query($sql);

// Comprobar si hay resultados
if ($result->num_rows > 0) {
    $generos = array();

    // Almacenar los resultados en un array
    while ($row = $result->fetch_assoc()) {
        $generos[] = $row;
    }

    // Devolver los géneros como JSON
    echo json_encode($generos);
} else {
    echo json_encode([]);
}

// Cerrar la conexión
$conexion->close();
?>
