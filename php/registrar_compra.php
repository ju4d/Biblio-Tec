<?php
// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Establecer la conexión con la base de datos
    $conexion = new mysqli("localhost", "root", "", "biblioteca");

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Obtener los datos del formulario
    $titulo_libro = $_POST['nombre_libro'][0]; // Solo toma el primer libro por simplicidad
    $autor_libro = $_POST['autor_libro'][0];
    $editorial_libro = $_POST['editorial_libro'][0];
    $genero = $_POST['genero'][0];
    $edicion = $_POST['edicion'];
    $precio = $_POST['precio'];
    $stock = $_POST['cantidad'];

    // Preparar la consulta SQL para insertar los datos en la tabla
    $sql = "INSERT INTO libros (editorial, titulo, autor, genero, edicion, precio, stock) 
    VALUES ('$editorial_libro', '$titulo_libro', '$autor_libro', '$genero', '$edicion', '$precio', '$stock')";

    // Ejecutar la consulta
    if ($conexion->query($sql) === TRUE) {
        echo "Datos guardados correctamente";
    } else {
        echo "Error al guardar los datos: " . $conexion->error;
    }

    // Cerrar la conexión
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Biblioteca</h1>
    
</body>
</html>

