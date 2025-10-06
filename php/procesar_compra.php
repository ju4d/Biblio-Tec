<?php
header('Content-Type: application/json');

// Habilitar el reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Leer el cuerpo de la solicitud
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    // Verificar que se recibieron todos los datos necesarios
    if (isset($data['total'], $data['metodo_pago'], $data['fecha'], $data['id_editorial'], $data['id_empleado'])) {
        // Incluir archivo de conexión a la base de datos
        require 'conectar.php';

        // Obtener los datos del formulario
        $total = $data['total'];
        $metodo_pago = $data['metodo_pago'];
        $fecha = $data['fecha'];
        $id_editorial = $data['id_editorial'];
        $id_empleado = $data['id_empleado'];

        // Preparar la consulta SQL para insertar los datos en la tabla compra
        $sql = "INSERT INTO compra (total, metodo_pago, fecha, id_editorial, id_empleado) VALUES (?, ?, ?, ?, ?)";

        // Preparar la sentencia
        $stmt = $conexion->prepare($sql);

        // Verificar si la preparación fue exitosa
        if ($stmt) {
            // Asociar parámetros
            $stmt->bind_param("dssii", $total, $metodo_pago, $fecha, $id_editorial, $id_empleado);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                // Si la consulta se ejecutó con éxito, devolver un mensaje de éxito
                $id_compra = $conexion->insert_id;
                // Si la consulta se ejecutó con éxito, devolver un mensaje de éxito con el ID
                echo json_encode(['success' => true, 'message' => 'Compra registrada con éxito. ID de la compra: ' . $id_compra]);
                
                //
           //     echo json_encode(['success' => true, 'message' => 'Compra registrada con éxito.']);
            } else {
                // Si hubo un error al ejecutar la consulta, devolver un mensaje de error con detalles
                echo json_encode(['success' => false, 'message' => 'Error al registrar la compra en la base de datos: ' . $stmt->error]);
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
