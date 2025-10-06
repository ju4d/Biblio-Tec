<?php
header('Content-Type: application/json');
require 'conectar.php';

// Habilitar el reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

$data = json_decode(file_get_contents('php://input'), true);
$id_cliente = $data['id_cliente'];
$libros = $data['libros'];
$multa = isset($data['multa']) ? $data['multa'] : null;

// Log para registrar la solicitud recibida
error_log('Solicitud de devolución recibida para el cliente ID: ' . $id_cliente);

// Iniciar la transacción
$conexion->begin_transaction();

try {
    // Buscar los id_prestamo asociados al id_cliente proporcionado
    $stmt = $conexion->prepare("SELECT id_prestamo FROM prestamo WHERE id_cliente = ? AND status = 0");
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $prestamosActualizados = 0;

    while ($row = $result->fetch_assoc()) {
        $id_prestamo = $row['id_prestamo'];
        error_log('Procesando préstamo ID: ' . $id_prestamo);

        // Actualizar el estado de los registros en prestamo_libro
        foreach ($libros as $libro) {
            $id_libro = isset($libro['idLibro']) ? $libro['idLibro'] : null;
            if ($id_libro) {
                $stmt = $conexion->prepare("UPDATE prestamo_libro SET status = 1 WHERE id_prestamo = ? AND id_libro = ?");
                $stmt->bind_param("ii", $id_prestamo, $id_libro);
                $stmt->execute();
                $affectedRows = $stmt->affected_rows;
                $stmt->close();

                error_log("Libro ID: $id_libro en préstamo ID: $id_prestamo actualizado, filas afectadas: $affectedRows");

                if ($affectedRows > 0) {
                    $prestamosActualizados++;
                }
            }
        }
    }

    if ($prestamosActualizados > 0) {
        // Si hay multa, registrar la multa en la base de datos
        if ($multa && isset($multa['monto']) && isset($multa['concepto']) && isset($multa['status'])) {
            $monto = $multa['monto'];
            $concepto = $multa['concepto'];
            $status = $multa['status'];

            $stmt = $conexion->prepare("INSERT INTO multa (monto, id_cliente, status, concepto) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("diis", $monto, $id_cliente, $status, $concepto);
            $stmt->execute();
            $stmt->close();

            error_log('Multa registrada para el cliente ID: ' . $id_cliente . ' con monto: ' . $monto);
        }

        // Confirmar la transacción
        $conexion->commit();

        // Log para registrar el éxito de la operación
        error_log('Devolución registrada con éxito para el cliente ID: ' . $id_cliente);
        echo json_encode(['success' => true]);
    } else {
        // Si no se actualizó nada, revertir la transacción
        $conexion->rollback();

        error_log('No se encontraron registros para actualizar para el cliente ID: ' . $id_cliente);
        echo json_encode(['success' => false, 'error' => 'No se encontraron registros para actualizar.']);
    }

} catch (Exception $e) {
    // En caso de error, revertir la transacción
    $conexion->rollback();

    // Log para registrar el error
    error_log('Error al registrar la devolución para el cliente ID ' . $id_cliente . ': ' . $e->getMessage());

    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// Cerrar la conexión
$conexion->close();
?>
