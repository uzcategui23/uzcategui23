<!DOCTYPE html>
<html lang="es">
<?php include './Commons/head.php'; ?>
<body>
    <?php include './Commons/nav.php'; ?>
    <main>
        <div id="tabla-container_prov">
            <div class="container text-center">
                <div class="row">
                    <div class="col">
                        <h2 class="text-start">Ventas Registradas</h2>
                    </div>
                    <div class="col">
                        <a href="http://localhost/inventario/index.php/ventas/crear" type="button" class="btn btn-primary">registrar venta</a>
                        <form action="" method="POST" style="display:inline;">
                        <a href="http://localhost/inventario/index.php/ventas/ventashoy" type="button" class="btn btn-success">Ventas del Día</a>
                        <a href="http://localhost/inventario/index.php/ventas/devoluciones" type="button" class="btn btn-success">Devolución</a>

                      </div>
                </div>
                <!-- Contador de Ventas -->
                <div class="row">
                    <div class="col">
                        <?php
                        // Conectar a la base de datos
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

                        // Query para contar las ventas
                        $sql_count = "SELECT COUNT(*) as total_ventas FROM ventas";
                        $result_count = $conn->query($sql_count);

                        if ($result_count) {
                            $row_count = $result_count->fetch_assoc();
                            echo "<h4>Total de Ventas: " . htmlspecialchars($row_count['total_ventas']) . "</h4>";
                        } else {
                            echo "<h4>Error al contar las ventas</h4>";
                        }
                        
                        // Cerrar conexión
                        $conn->close();
                        ?>
                    </div>
                </div>
            </div>

            <table id="tabla-ventas" class="table table-striped">
                <thead>
                    <tr>
                        <th>Nombre y Apellido del Cliente</th>
                        <th>Cédula</th>
                        <th>Teléfono</th>
                        <th>Producto</th> 
                        <th>Cantidad</th>
                        <th>$$$</th>
                        <th>Total</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                     <!-- Aquí se llenarán los datos -->
                     <?php
                    // Conectar a la base de datos
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

                    // Query para recuperar ventas con clientes y productos
$sql = "SELECT c.nombre_cliente, c.cedula_cliente, c.telefono_cliente, 
v.cantidad_venta, v.precio_venta, v.precio_dolar,v.total_venta, v.fecha_venta, 
 p.nombre_productos 
FROM ventas v 
JOIN productos p ON v.producto_venta = p.id_productos  
JOIN clientes c ON v.id_cliente = c.id_cliente";       

$result = $conn->query($sql);

if (!$result) {
die("Error: " . $conn->error);
}

if ($result->num_rows > 0) {
while ($row = $result->fetch_assoc()) {
?>
<tr>
<td><?php echo htmlspecialchars($row["nombre_cliente"]); ?></td>
<td><?php echo htmlspecialchars($row["cedula_cliente"]); ?></td>
<td><?php echo htmlspecialchars($row["telefono_cliente"]); ?></td>

<!-- Mostrar el nombre del producto -->
<td><?php echo htmlspecialchars($row["nombre_productos"]); ?></td>
<td><?php echo htmlspecialchars($row["cantidad_venta"]); ?></td>
<td><?php echo htmlspecialchars($row["precio_dolar"]); ?></td>
<td><?php echo htmlspecialchars($row["total_venta"]); ?></td>
<td><?php echo htmlspecialchars($row["fecha_venta"]); ?></td>

<td>
                                    <!-- Agregar botones de acción aquí si es necesario -->
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="8">No hay ventas registradas</td> <!-- Cambiado a 8 para que coincida con el número de columnas -->
                        </tr>
                        <?php
                    }
                    // Cerrar conexión
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include './Commons/footer.php'; ?>
</body>
</html>