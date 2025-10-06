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
    if (isset($data['criterio'], $data['busqueda'])) {
        // Incluir archivo de conexión a la base de datos
        require 'conectar.php';

        // Obtener los datos del formulario
        $criterio = $data['criterio'];
        $busqueda = $data['busqueda'];

        // Determinar el campo de búsqueda según el criterio
        $campo = ($criterio === 'nombre') ? 'nombre' : 'estatus';

        // Preparar la consulta SQL para buscar en la tabla mobiliario
        $sql = "SELECT * FROM mobiliario WHERE $campo LIKE ?";
        
        // Preparar la sentencia
        $stmt = $conexion->prepare($sql);

        // Verificar si la preparación fue exitosa
        if ($stmt) {
            // Asociar parámetros
            $busqueda_param = '%' . $busqueda . '%';
            $stmt->bind_param("s", $busqueda_param);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                // Obtener los resultados
                $result = $stmt->get_result();
                $mobiliario = $result->fetch_all(MYSQLI_ASSOC);

                // Devolver los resultados como JSON
                echo json_encode(['success' => true, 'mobiliario' => $mobiliario]);
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
    } else if (isset($data['id'])) {
        require 'conectar.php';

        // Obtener los datos del formulario
        $id = $data['id'];

        $sql = "SELECT * FROM mobiliario WHERE id = ?";

        // Preparar la sentencia
        $stmt = $conexion->prepare($sql);

        // Verificar si la preparación fue exitosa
        if ($stmt) {
            // Asociar parámetros
            $stmt->bind_param("i", $id);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                // Obtener los resultados
                $result = $stmt->get_result();
                $mobiliario = $result->fetch_all(MYSQLI_ASSOC);

                // Devolver los resultados como JSON
                echo json_encode(['success' => true, 'mobiliario' => $mobiliario]);
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
