<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conectar a la base de datos
    $conn = new mysqli("localhost", "root", "", "sistema");

    // Verificar conexión
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Obtener datos del formulario
    $id_producto = $_POST['id_producto'];
    $nombre_cliente = $_POST['nombre_cliente'];
    $cedula_cliente = $_POST['cedula_cliente'];
    $telefono_cliente = $_POST['telefono_cliente'];
    $descripcion_producto = $_POST['descripcion_producto'];
    $cantidad = $_POST['cantidad_venta'];
    $precio_bs = $_POST['precio_bs'];
    $precio_usd = $_POST['precio_usd'];
    $total = $_POST['total_venta'];
    $fecha_venta = $_POST['fecha_venta'];
    $fecha_devolucion = date('Y-m-d'); // Fecha actual

    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare("INSERT INTO devoluciones (id_producto, nombre_cliente, cedula_cliente, telefono_cliente, descripcion_producto, cantidad, precio_bs, precio_usd, total, fecha_venta, fecha_devolucion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssisdssss", $id_producto, $nombre_cliente, $cedula_cliente, $telefono_cliente, $descripcion_producto, $cantidad, $precio_bs, $precio_usd, $total, $fecha_venta, $fecha_devolucion);

    if ($stmt->execute()) {
        echo "Devolución registrada exitosamente.";
    } else {
        echo "Error al registrar la devolución: " . $stmt->error;
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();
}
?>
