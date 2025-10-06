<?php
header('Content-Type: application/json');
require 'conectar.php'; // Asegúrate de tener este archivo para la conexión a la base de datos

$id_cliente = $_POST['id_cliente'];
$id_empleado = $_POST['id_empleado'];

$validar_cliente = $conexion->prepare("SELECT COUNT(*) FROM cliente WHERE id_cliente = ?");
$validar_cliente->bind_param("i", $id_cliente);
$validar_cliente->execute();
$validar_cliente->bind_result($cliente_existe);
$validar_cliente->fetch();
$validar_cliente->close();

$validar_empleado = $conexion->prepare("SELECT COUNT(*) FROM personal WHERE id_personal = ?");
$validar_empleado->bind_param("i", $id_empleado);
$validar_empleado->execute();
$validar_empleado->bind_result($empleado_existe);
$validar_empleado->fetch();
$validar_empleado->close();

if ($cliente_existe > 0 && $empleado_existe > 0) {
    echo json_encode(['existe' => true]);
} else {
    echo json_encode(['existe' => false]);
}
?>
