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
    if (isset($data['id']) || isset($data['correo'])) {
        // Incluir archivo de conexión a la base de datos
        require 'conectar.php';

        $id = $data['id'];
        $correo = $data['correo'];

        if (!empty($id)) {
            // Consultar por ID
            $sql = "SELECT * FROM cliente WHERE id_cliente = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id);
        } elseif (!empty($correo)) {
            // Consultar por correo
            $sql = "SELECT * FROM cliente WHERE correo = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("s", $correo);
        }

        // Ejecutar la consulta
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $cliente = $result->fetch_assoc();
                echo json_encode(['success' => true, 'data' => $cliente]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Cliente no encontrado.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al consultar la base de datos: ' . $stmt->error]);
        }

        // Cerrar la conexión y liberar recursos
        $stmt->close();
        $conexion->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Por favor, ingrese un ID o un correo electrónico para la consulta.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error: solicitud no válida.']);
}
?>
