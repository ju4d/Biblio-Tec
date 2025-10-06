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
    if (isset($data['id_cliente'], $data['monto'], $data['concepto'], $data['status'])) {
        // Incluir archivo de conexión a la base de datos
        require 'conectar.php';

        // Obtener los datos del formulario
        $id_cliente = $data['id_cliente'];
        $monto = $data['monto'];
        $concepto = $data['concepto'];
        $status = $data['status'];
        
        // Preparar la consulta SQL para insertar los datos en la tabla compra
        $sql = "INSERT INTO multa (monto, id_cliente, status, concepto, ) VALUES (?, ?, ?, ?)";

        // Preparar la sentencia
        $stmt = $conexion->prepare($sql);

        // Verificar si la preparación fue exitosa
        if ($stmt) {
            // Asociar parámetros
            $stmt->bind_param("diis", $monto, $id_cliente, $status, $concepto, );

            // Ejecutar la consulta
            if ($stmt->execute()) {
                // Si la consulta se ejecutó con éxito, devolver un mensaje de éxito
                $id_multa = $conexion->insert_id;
                // Si la consulta se ejecutó con éxito, devolver un mensaje de éxito con el ID
                echo json_encode(['success' => true, 'message' => 'Compra registrada con éxito. ID de la compra: ' . $id_multa]);
                
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
