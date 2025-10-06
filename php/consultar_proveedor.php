<?php
header('Content-Type: application/json');

// Habilitar el reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Leer el cuerpo de la solicitud
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    // Verificar que se recibió al menos uno de los parámetros
    if (isset($data['id']) || isset($data['nombre'])) {
        // Incluir archivo de conexión a la base de datos
        require 'conectar.php';

        $id = isset($data['id']) ? $data['id'] : null;
        $nombre = isset($data['nombre']) ? $data['nombre'] : null;

        if (!empty($id)) {
            // Consultar por ID
            $sql = "SELECT * FROM proveedor WHERE id_editorial = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id);
        } elseif (!empty($nombre)) {
            // Consultar por nombre
            $sql = "SELECT * FROM proveedor WHERE nombre = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("s", $nombre);
        } else {
            // Si ambos parámetros están vacíos, devolver un mensaje de error
            echo json_encode(['success' => false, 'message' => 'Por favor, ingrese un ID o un nombre para la consulta.']);
            exit;
        }

        // Ejecutar la consulta
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $proveedor = $result->fetch_assoc();
                echo json_encode(['success' => true, 'data' => $proveedor]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Proveedor no encontrado.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al consultar la base de datos: ' . $stmt->error]);
        }

        // Cerrar la conexión y liberar recursos
        $stmt->close();
        $conexion->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Por favor, ingrese un ID o un nombre para la consulta.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error: solicitud no válida.']);
}
?>
