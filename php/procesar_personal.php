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
    if (isset($data['nombre'], $data['telefono'], $data['cargo'],$data['salario'])) {
        // Incluir archivo de conexión a la base de datos
        require 'conectar.php';

        // Obtener los datos del formulario
        $nombre = $data['nombre'];
        $telefono = $data['telefono'];
        $cargo = $data['cargo'];
        $salario = $data['salario'];
        
        // Insertar en la tabla cliente
        $sql_personal = "INSERT INTO personal (nombre, telefono, cargo, salario) VALUES (?, ?, ?, ?)";
        $stmt_personal = $conexion->prepare($sql_personal);
        $stmt_personal->bind_param("sssd",  $nombre, $telefono, $cargo, $salario);

        // Ejecutar la consulta
        if ($stmt_personal->execute()) {
            echo json_encode(['success' => true, 'message' => 'personal registrado con éxito.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al registrar el personal en la base de datos: ' . $stmt_personal->error]);
        }

        // Cerrar la conexión y liberar recursos
        $stmt_personal->close();
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
