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
    if (isset($data['id'],$data['nombre'], $data['telefono'], $data['correo'], $data['vigencia'], $data['fecha_inicio'], $data['generos'])) {
        // Incluir archivo de conexión a la base de datos
        require 'conectar.php';

        // Obtener los datos del formulario
        $id = $data['id'];
        $nombre = $data['nombre'];
        $telefono = $data['telefono'];
        $correo = $data['correo'];
        $vigencia = $data['vigencia'];
        $fechaInicio = $data['fecha_inicio'];
        $generos = $data['generos'];

        // Insertar en la tabla cliente
        $sql_cliente = "UPDATE cliente SET telefono = ?, correo = ?, vigencia = ?, inicio = ?, nombre = ? WHERE id_cliente = ?";
        $stmt_cliente = $conexion->prepare($sql_cliente);
        $stmt_cliente->bind_param("sssssi",  $telefono, $correo, $vigencia, $fechaInicio, $nombre, $id);

        // Ejecutar la consulta
        if ($stmt_cliente->execute()) {

            $sql_delete_generos = "DELETE FROM clientes_generos WHERE id_cliente = ?";
            $stmt_delete_generos = $conexion->prepare($sql_delete_generos);
            $stmt_delete_generos->bind_param('i', $id);
            $stmt_delete_generos->execute();

            // Insertar en la tabla clientes_generos
            foreach ($generos as $idGenero) {
                $sql_clientes_generos = "INSERT INTO clientes_generos (id_cliente, id_genero) VALUES (?, ?)";
                $stmt_clientes_generos = $conexion->prepare($sql_clientes_generos);
                $stmt_clientes_generos->bind_param("ii", $id, $idGenero);
                $stmt_clientes_generos->execute();
            }

            echo json_encode(['success' => true, 'message' => 'Cliente registrado con éxito.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al registrar el cliente en la base de datos: ' . $stmt_cliente->error]);
        }

        // Cerrar la conexión y liberar recursos
        $stmt_cliente->close();
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
