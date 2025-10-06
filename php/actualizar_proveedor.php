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
    if (isset($data['id'],$data['nombre'], $data['telefono'], $data['direccion'])) {
        // Incluir archivo de conexión a la base de datos
        require 'conectar.php';

        // Obtener los datos del formulario
        $id = $data['id'];
        $nombre = $data['nombre'];
        $telefono = $data['telefono'];
        $direccion = $data['direccion'];

        // Insertar en la tabla cliente
        $sql_proveedor = "UPDATE proveedor SET telefono = ?, direccion = ?, nombre = ? WHERE id_editorial = ?";
        $stmt_proveedor = $conexion->prepare($sql_proveedor);
        $stmt_proveedor->bind_param("sssi",  $telefono, $direccion, $nombre, $id);

        // Ejecutar la consulta
        if ($stmt_proveedor->execute()) {

            echo json_encode(['success' => true, 'message' => 'proveedor registrado con éxito.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al registrar el cliente en la base de datos: ' . $stmt_proveedor->error]);
        }

        // Cerrar la conexión y liberar recursos
        $stmt_proveedor->close();
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