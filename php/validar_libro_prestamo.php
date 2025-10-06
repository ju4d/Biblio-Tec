<?php
header('Content-Type: application/json');
require 'conectar.php';

$id_libro = $_POST['id_libro'];

$validar_libro = $conexion->prepare("SELECT COUNT(*) FROM libros WHERE id_libro = ?");
$validar_libro->bind_param("i", $id_libro);
$validar_libro->execute();
$validar_libro->bind_result($libro_existe);
$validar_libro->fetch();
$validar_libro->close();

if ($libro_existe > 0) {
    echo json_encode(['existe' => true]);
} else {
    echo json_encode(['existe' => false]);
}
?>
