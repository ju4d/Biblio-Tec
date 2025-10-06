<?php
header('Content-Type: application/json');
require 'conectar.php';

// Habilitar el reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

$data = json_decode(file_get_contents('php://input'), true);
$metodo_pago = $data['metodo_pago'];
$id_cliente = $data['id_cliente'];
$libros = $data['libros'];

$conexion->begin_transaction();

try {
    // Insertar la venta en la tabla `venta`
    $insertar_venta = $conexion->prepare("INSERT INTO venta (fecha, metodo_pago, id_cliente) VALUES (NOW(), ?, ?)");
    $insertar_venta->bind_param("si", $metodo_pago, $id_cliente);
    $insertar_venta->execute();
    $folio_venta = $insertar_venta->insert_id;
    $insertar_venta->close();

    // Insertar los libros asociados a la venta en la tabla `venta_libro`
    foreach ($libros as $libro) {
        $id_libro = $libro['idLibro'];
        $cantidad = $libro['cantidad'];
        $insertar_venta_libro = $conexion->prepare("INSERT INTO venta_libro (folio_venta, id_libro, cantidad) VALUES (?, ?, ?)");
        $insertar_venta_libro->bind_param("iii", $folio_venta, $id_libro, $cantidad);
        $insertar_venta_libro->execute();
        $insertar_venta_libro->close();

        // Actualizar el stock del libro
        //$actualizar_stock = $conexion->prepare("UPDATE libros SET stock = stock - ? WHERE id_libro = ?");
        //$actualizar_stock->bind_param("ii", $cantidad, $id_libro);
        //$actualizar_stock->execute();
        //$actualizar_stock->close();
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
