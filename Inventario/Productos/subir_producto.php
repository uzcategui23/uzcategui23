<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sistema";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_producto = $_POST['nombre_producto'];
    $codigo_producto = $_POST['codigo_producto'];
    
    // Manejar la carga de imagen
    if (isset($_FILES['imagen_producto']) && $_FILES['imagen_producto']['error'] == 0) {
        $ruta_imagen = 'imagenes/' . basename($_FILES['imagen_producto']['name']); // Ruta donde se guardará la imagen
        
        // Mover el archivo subido a la carpeta deseada
        if (move_uploaded_file($_FILES['imagen_producto']['tmp_name'], $ruta_imagen)) {
            // Insertar en la base de datos
            $sql = "INSERT INTO productos (nombre_productos, codigo_productos, imagen_producto) VALUES ('$nombre_producto', '$codigo_producto', '$ruta_imagen')";
            if ($conn->query($sql) === TRUE) {
                echo "Producto agregado exitosamente.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Error al subir la imagen.";
        }
    } else {
        echo "No se ha subido ninguna imagen.";
    }
}

$conn->close();
?>
