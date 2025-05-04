<?php
// Conectar a la base de datos
$conn = new mysqli("localhost", "root", "", "sistema");

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Consultar las devoluciones
$sql = "
    SELECT 
        d.nombre_cliente,
        d.cedula_cliente,
        d.telefono_cliente,
        d.descripcion_producto,
        d.cantidad,
        d.precio_bs,
        d.precio_usd,
        d.total,
        d.fecha_venta,
        d.fecha_devolucion
    FROM 
        devoluciones d
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['nombre_cliente']}</td>
                <td>{$row['cedula_cliente']}</td>
                <td>{$row['telefono_cliente']}</td>
                <td>{$row['descripcion_producto']}</td>
                <td>{$row['cantidad']}</td>
                <td>{$row['precio_bs']}</td>
                <td>{$row['precio_usd']}</td>
                <td>{$row['total']}</td>
                <td>{$row['fecha_venta']}</td>
                <td>{$row['fecha_devolucion']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='10'>No hay devoluciones registradas.</td></tr>";
}

// Cerrar la conexión
$conn->close();
?>