
<?php
header('Content-Type: application/json');
require 'conectar.php';

$data = json_decode(file_get_contents('php://input'), true);

$fecha_inicio = $data['fecha_inicio'];
$fecha_entrega = $data['fecha_entrega'];
$id_empleado = $data['id_empleado'];
$id_cliente = $data['id_cliente'];
$libros = $data['libros'];

$conexion->begin_transaction();

try {
    // Insertar el préstamo en la tabla `prestamo`
    $insertar_prestamo = $conexion->prepare("INSERT INTO prestamo (fecha_inicio, fecha_entrega, id_empleado, id_cliente) VALUES (?, ?, ?, ?)");
    $insertar_prestamo->bind_param("ssii", $fecha_inicio, $fecha_entrega, $id_empleado, $id_cliente);
    $insertar_prestamo->execute();
    $id_prestamo = $insertar_prestamo->insert_id;
    $insertar_prestamo->close();

    // Insertar los libros asociados al préstamo en la tabla `prestamo_libro`
    foreach ($libros as $libro) {
        $id_libro = $libro['idLibro'];
        $insertar_prestamo_libro = $conexion->prepare("INSERT INTO prestamo_libro (id_prestamo, id_libro) VALUES (?, ?)");
        $insertar_prestamo_libro->bind_param("ii", $id_prestamo, $id_libro);
        $insertar_prestamo_libro->execute();
        $insertar_prestamo_libro->close();
    }

    // Confirmar la transacción
    $conexion->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // En caso de error, revertir la transacción
    $conexion->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conexion->close();
?>

