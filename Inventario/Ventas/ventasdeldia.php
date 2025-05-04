
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
                        <h2 class="text-start">Ventas Registradas del dia</h2>
                    </div>
                    <div class="col">
                        <a href="http://localhost/inventario/index.php/ventas/crear" type="button" class="btn btn-primary">Registrar Venta</a>
                       <!-- <form action="" method="POST" style="display:inline;">
                            <input type="submit" name="registrar_ventas" value="Registrar Ventas del Día" class="btn btn-success">
                        </form>-->
                    </div>
                </div>

                <!-- Contador de Ventas -->
                <div class="row">
                    <div class="col">
                        <?php
                        // Conecta base de datos
                        $servername = "localhost";
                        $username = "root";
                        $password = "";
                        $dbname = "sistema";

                        //conexión
                        $conn = new mysqli($servername, $username, $password, $dbname);
                        
                        // Verificar conexión
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Query para contar las ventas del día
                        $fecha_actual = date('Y-m-d');
                        $sql_count = "SELECT COUNT(*) as total_ventas FROM ventas WHERE DATE(fecha_venta) = '$fecha_actual'";
                        $result_count = $conn->query($sql_count);

                        if ($result_count) {
                            $row_count = $result_count->fetch_assoc();
                            echo "<h4>Total de Ventas del Día: " . htmlspecialchars($row_count['total_ventas']) . "</h4>";
                        } else {
                            echo "<h4>Error al contar las ventas</h4>";
                        }
                        ?>
                    </div>
                </div>

            </div>

            <!-- Tabla de Ventas del Día -->
            <table id="tabla-ventas" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Venta</th>
                        <th>Nombre Cliente</th>
                        <th>Cédula</th>
                        <th>Teléfono</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Fecha</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                   // Query para recuperar todas las ventas del día
$sql_ventas_dia = "
SELECT 
    v.id_venta,
    c.nombre_cliente,
    c.cedula_cliente,
    c.telefono_cliente,
    p.nombre_productos,
    v.cantidad_venta,
    v.precio_venta,
    v.fecha_venta,
    v.total_venta 
FROM 
    ventas v 
LEFT JOIN 
    productos p ON v.producto_venta = p.id_productos 
LEFT JOIN 
    clientes c ON v.id_cliente = c.id_cliente  -- Unir con la tabla clientes
WHERE 
    DATE(v.fecha_venta) = '$fecha_actual'"; 

$result_ventas_dia = $conn->query($sql_ventas_dia);

if ($result_ventas_dia && $result_ventas_dia->num_rows > 0) {
    while ($row = $result_ventas_dia->fetch_assoc()) {
        ?>
        <tr>
            <td><?php echo htmlspecialchars($row["id_venta"]); ?></td>
            <td><?php echo htmlspecialchars($row["nombre_cliente"]); ?></td>
            <td><?php echo htmlspecialchars($row["cedula_cliente"]); ?></td>
            <td><?php echo htmlspecialchars($row["telefono_cliente"]); ?></td>
            <td><?php echo htmlspecialchars($row["nombre_productos"]); ?></td>
            <td><?php echo htmlspecialchars($row["cantidad_venta"]); ?></td>
            <td><?php echo htmlspecialchars($row["precio_venta"]); ?></td>
            <td><?php echo htmlspecialchars($row["fecha_venta"]); ?></td>
            <td><?php echo htmlspecialchars($row["total_venta"]); ?></td>
        </tr>
        <?php

                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="9">No hay ventas registradas para el día de hoy.</td> <!-- Cambiado a 9 para que coincida con el número de columnas -->
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