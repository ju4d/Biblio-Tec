<?php
header('Content-Type: application/json');

// Habilitar el reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Leer el cuerpo de la solicitud
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    // Verificar que se recibió la fecha
    if (isset($data['fecha_ventas'])) {
        // Incluir archivo de conexión a la base de datos
        require 'conectar.php';

        $fecha = $data['fecha_ventas'];

        // Preparar la consulta SQL para buscar las ventas por fecha
        $sql = "SELECT folio_venta, fecha, metodo_pago, id_cliente
                FROM venta
                WHERE fecha = ?";

        // Preparar la sentencia
        $stmt = $conexion->prepare($sql);

        // Verificar si la preparación fue exitosa
        if ($stmt) {
            // Asociar parámetros
            $stmt->bind_param("s", $fecha);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                // Obtener los resultados
                $result = $stmt->get_result();
                $ventas = $result->fetch_all(MYSQLI_ASSOC);

                // Devolver los resultados como JSON
                echo json_encode(['success' => true, 'ventas' => $ventas]);
            } else {
                // Si hubo un error al ejecutar la consulta, devolver un mensaje de error con detalles
                echo json_encode(['success' => false, 'message' => 'Error al realizar la consulta en la base de datos: ' . $stmt->error]);
            }

            // Cerrar la conexión y liberar recursos
            $stmt->close();
        } else {
            // Si hubo un error al preparar la sentencia, devolver un mensaje de error con detalles
            echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $conexion->error]);
        }

        $conexion->close();
    } else {
        // Si faltan datos en el formulario, devolver un mensaje de error
        echo json_encode(['success' => false, 'message' => 'Faltan datos en el formulario. Por favor, completa todos los campos.']);
    }
} else {
    // Si la solicitud no es de tipo POST, devolver un mensaje de error
    echo json_encode(['success' => false, 'message' => 'Error: solicitud no válida.']);
}
?>
