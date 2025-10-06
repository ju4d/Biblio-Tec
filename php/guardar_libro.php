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
    if (isset($data['titulo'], $data['autor'], $data['edicion'], $data['precio'], $data['stock'], $data['id_genero'], $data['id_editorial'], $data['id_compra'])) {
        // Incluir archivo de conexión a la base de datos
        require 'conectar.php';

        // Obtener los datos del formulario
        $titulo = $data['titulo'];
        $autor = $data['autor'];
        $edicion = $data['edicion'];
        $precio = $data['precio'];
        $stock = $data['stock'];
        $id_genero = $data['id_genero'];
        $id_editorial = $data['id_editorial'];
        $id_compra = $data['id_compra'];
 
        // Verificar si ya existe un registro con el mismo título, autor, edición y editorial
        $sql_select = "SELECT id_libro, stock FROM libros WHERE titulo = ? AND autor = ? AND edicion = ? AND id_editorial = ?";
        $stmt_select = $conexion->prepare($sql_select);
        $stmt_select->bind_param("ssii", $titulo, $autor, $edicion, $id_editorial);
        $stmt_select->execute();
        $result = $stmt_select->get_result();

        if ($result->num_rows > 0) {
            // Si ya existe un registro, actualizar los campos precio y stock
            $row = $result->fetch_assoc();
            $id_libro = $row['id_libro'];
            $stock_actual = $row['stock'];
            $stock_total = $stock_actual + $stock;

            $sql_update = "UPDATE libros SET precio = ?, stock = ? WHERE id_libro = ?";
            $stmt_update = $conexion->prepare($sql_update);
            $stmt_update->bind_param("dii", $precio, $stock_total, $id_libro);
            
            if ($stmt_update->execute()) {

                // Actualizar la tabla compras_libros
                $sql_insert_compras_libros = "INSERT INTO compras_libros (id_compra, id_libro, cantidad) VALUES (?, ?, ?)";
                $stmt_insert_compras_libros = $conexion->prepare($sql_insert_compras_libros);
                $stmt_insert_compras_libros->bind_param("iii", $id_compra, $id_libro, $stock);
                if ($stmt_insert_compras_libros->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Registro existente actualizado con éxito en la tabla libros.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al actualizar el registro existente en la tabla libros: ' . $stmt_insert_compras_libros->error]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar el registro existente en la tabla libros: ' . $stmt_update->error]);
            }
        } else {
            // Si no existe un registro, insertar uno nuevo
            $sql_insert = "INSERT INTO libros (titulo, autor, edicion, precio, stock, id_genero, id_editorial) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conexion->prepare($sql_insert);
            $stmt_insert->bind_param("ssiddii", $titulo, $autor, $edicion, $precio, $stock, $id_genero, $id_editorial);
            if ($stmt_insert->execute()) {
                $id_libro = $conexion->insert_id;
                // Insertar en la tabla compras_libros
                $sql_insert_compras_libros = "INSERT INTO compras_libros (id_compra, id_libro, cantidad) VALUES (?, ?, ?)";
                $stmt_insert_compras_libros = $conexion->prepare($sql_insert_compras_libros);
                $stmt_insert_compras_libros->bind_param("iii", $id_compra, $id_libro, $stock);
                if ($stmt_insert_compras_libros->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Libro registrado con éxito en la tabla libros y en la tabla compras_libros. ID del libro: ' . $id_libro]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al insertar el registro en la tabla compras_libros: ' . $stmt_insert_compras_libros->error]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al registrar el libro en la base de datos: ' . $stmt_insert->error]);
            }
        }

        // Cerrar la conexión y liberar recursos
        $stmt_select->close();
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
