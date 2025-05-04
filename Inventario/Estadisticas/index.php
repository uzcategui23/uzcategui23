<!DOCTYPE html>
<html lang="es">
<?php include './Commons/head.php'; ?>
<body>
    <?php include './Commons/nav.php'; ?>
    <main>
    <div id="tabla-container_prov">
            <div class="container text-center">
                <h2 style="text-align: center;">Estadística</h2>

                <div class="row mb-4">
                    <div class="col">
                        <button id="btnVenta" class="btn btn-primary">
                            <i class="fa fa-shopping-cart"></i> Venta
                        </button>
                        <div class="col">
                        <button id="btnProducto" class="btn btn-secondary">
                            Producto
                        </button>
                    </div>
                    </div>
                </div>

                <!-- Formulario y tabla ocultos inicialmente -->
                <div id="busquedaContainer" class="d-none">
                    <div class="row mb-4">
                        <div class="col">
                            <input type="date" id="start-date" class="form-control" required>
                        </div>
                        <div class="col">
                            <input type="date" id="end-date" class="form-control" required>
                        </div>
                        <div class="col">
                            <button id="btnBuscar" class="btn btn-secondary">Buscar Ventas</button>
                        </div>
                    </div>

                    <button id="btnReporte" class="btn btn-success mt-3">Generar Reporte PDF</button>

                    <div id="tablaProductos" class="mt-4 d-none">
                        <h2>Resultados de Ventas</h2>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre Cliente</th>
                                    <th>Cédula Cliente</th>
                                    <th>Teléfono Cliente</th>
                                    <th>Total Vendido</th>
                                    <th>Total Precio</th>
                                    <th>Fecha Venta</th>
                                    <th>Nombre Producto</th>
                                </tr>
                            </thead>
                            <tbody id="productosBody">
                                <!-- Los datos se llenarán aquí mediante AJAX -->
                            </tbody>
                        </table>

                        <!-- Canvas para la gráfica -->
                        <canvas id="graficaProductos" width="400" height="200"></canvas>
                    </div>
                </div>

            </div>
             <!-- Sección para productos y stock -->
             <div id="stockContainer" class="d-none">
                        
<!-- Botones para filtrar stock -->
<div class="row mb-4">
    <div class="col">
        <button id="btnStockAlto" class="btn btn-info">Stock Alto</button>
    </div>
    <div class="col">
        <button id="btnStockBajo" class="btn btn-warning">Stock Bajo</button>
    </div>
</div>
<a href="http://localhost/inventario/Estadisticas/generar_reporte_productos.php" class="btn btn-primary">Generar Reporte de Productos</a>
<br>
<br>
<!-- Campo de búsqueda -->
<div class="form-group">
    <input type="text" id="searchProduct" class="form-control" placeholder="Buscar producto por nombre">
</div>

                        <h2>Datos de Productos</h2>
<table class="table table-striped" id="tablaStock">
    <thead>
        <tr>
            <th>Nombre Producto</th>
            <th>Código</th>
            <th>Modelo</th>
            <th>Marca</th>
            <th>Descripción</th>
            <th>Cantidad Disponible</th>
            <th>Precio (Moneda Local)</th>
            <th>Precio (Dólares)</th>
            <th>Precio Total</th>
            <th>Fecha de Registro</th>
        </tr>
    </thead>
    <tbody id="stockBody">
        <!-- Los datos de stock se llenarán aquí -->
    </tbody>
</table>
                    </div>
                </div>

            </div>

            <!-- Incluir librerías -->
            <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Agregar esta línea -->

            <?php
            // Conexión a la base de datos
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "sistema";

            $conn = new mysqli($servername, $username, $password, $dbname);

            // Verificar conexión
            if ($conn->connect_error) {
                die("Error de conexión: " . $conn->connect_error);
            }

            // Consulta SQL para obtener productos más vendidos
            $sql = "SELECT c.nombre_cliente, c.cedula_cliente, c.telefono_cliente,
                           SUM(v.cantidad_venta) AS total_vendido,
                           SUM(v.precio_venta) AS total_precio,
                           v.fecha_venta,
                           p.nombre_productos 
                    FROM ventas v 
                    JOIN productos p ON v.producto_venta = p.id_productos  
                    JOIN clientes c ON v.id_cliente = c.id_cliente
                    GROUP BY p.nombre_productos, c.nombre_cliente,
                             c.cedula_cliente,
                             c.telefono_cliente,
                             v.fecha_venta
                    ORDER BY total_vendido DESC";

            $result = $conn->query($sql);

            // Guardar resultados en un array
            $productos = [];
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $productos[] = $row;
                }
            }

// Consulta SQL para obtener datos de productos
$sqlStock = "SELECT 
                nombre_productos, 
                codigo_productos, 
                modelo_productos, 
                marca_productos, 
                descripcion_productos, 
                cantidad_disponible, 
                precio_productos, 
                precio_dolares, 
                precio_total, 
                fecha_registro 
             FROM productos";

$resultStock = $conn->query($sqlStock);
$stockProductos = [];

if ($resultStock->num_rows > 0) {
    while ($row = $resultStock->fetch_assoc()) {
        $stockProductos[] = $row;
    }
}
?>

<script type="text/javascript">
                const productosData = <?php echo json_encode($productos); ?>;
                const stockData = <?php echo json_encode($stockProductos); ?>;

                document.getElementById('btnVenta').addEventListener('click', function() {
                    document.getElementById('busquedaContainer').classList.remove('d-none');
                    document.getElementById('tablaProductos').classList.remove('d-none'); // Mostrar la tabla también
                });

                document.getElementById('btnProducto').addEventListener('click', function() {
                    document.getElementById('stockContainer').classList.toggle('d-none');
                });

                document.getElementById('btnBuscar').addEventListener('click', function() {
                    const startDate = document.getElementById('start-date').value;
                    const endDate = document.getElementById('end-date').value;

                    const filteredData = productosData.filter(producto => {
                        const fechaVenta = new Date(producto.fecha_venta);
                        return fechaVenta >= new Date(startDate) && fechaVenta <= new Date(endDate);
                    });

                    const tablaBody = document.getElementById('productosBody');
                    tablaBody.innerHTML = ''; // Limpiar tabla antes de agregar nuevos datos

                    if (filteredData.length === 0) {
                        tablaBody.innerHTML = '<tr><td colspan="7" class="text-center">No se encontraron resultados.</td></tr>';
                    } else {
                        const nombresProductos = [];
                        const totalVendidos = [];
                        
                        const acumulador = {};

                        filteredData.forEach(producto => {
                            const row = `<tr>
                                            <td>${producto.nombre_cliente}</td>
                                            <td>${producto.cedula_cliente}</td>
                                            <td>${producto.telefono_cliente}</td>
                                            <td>${producto.total_vendido}</td>
                                            <td>${producto.total_precio}</td>
                                            <td>${producto.fecha_venta}</td>
                                            <td>${producto.nombre_productos}</td>
                                         </tr>`;
                            tablaBody.innerHTML += row;

                            // Acumular los totales por producto
                            if (!acumulador[producto.nombre_productos]) {
                                acumulador[producto.nombre_productos] = 0;
                            }
                            acumulador[producto.nombre_productos] += parseInt(producto.total_vendido);
                        });

                        for (const [nombre, total] of Object.entries(acumulador)) {
                            nombresProductos.push(nombre);
                            totalVendidos.push(total);
                        }

                        // Crear gráfica
                        const ctx = document.getElementById('graficaProductos').getContext('2d');
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: nombresProductos,
                                datasets: [{
                                    label: 'Total Vendido',
                                    data: totalVendidos,
                                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    }

                    document.getElementById('tablaProductos').classList.remove('d-none');
                });

              
        document.getElementById('btnStockAlto').addEventListener('click', function() {
        const stockBody = document.getElementById('stockBody');
        stockBody.innerHTML = ''; // Limpiar tabla antes de agregar nuevos datos
        const productosAltos = stockData.filter(producto => producto.cantidad_disponible > 10); // Cambia el número según tu criterio

                    if (productosAltos.length === 0) {
            stockBody.innerHTML = '<tr><td colspan="10" class="text-center">No se encontraron productos con stock alto.</td></tr>';
        } else {
            productosAltos.forEach(producto => {
                const row = `<tr>
                                <td>${producto.nombre_productos}</td>
                                <td>${producto.codigo_productos}</td>
                                <td>${producto.modelo_productos}</td>
                                <td>${producto.marca_productos}</td>
                                <td>${producto.descripcion_productos}</td>
                                <td>${producto.cantidad_disponible}</td>
                                <td>${producto.precio_productos}</td>
                                <td>${producto.precio_dolares}</td>
                                <td>${producto.precio_total}</td>
                                <td>${producto.fecha_registro}</td>
                             </tr>`;
                stockBody.innerHTML += row;
            });
        }
    });

              // Función para mostrar productos con stock bajo
    document.getElementById('btnStockBajo').addEventListener('click', function() {
        const stockBody = document.getElementById('stockBody');
        stockBody.innerHTML = ''; // Limpiar tabla antes de agregar nuevos datos
        const productosBajos = stockData.filter(producto => producto.cantidad_disponible <= 10); // Cambia el número según tu criterio

        if (productosBajos.length === 0) {
            stockBody.innerHTML = '<tr><td colspan="10" class="text-center">No se encontraron productos con stock bajo.</td></tr>';
        } else {
            productosBajos.forEach(producto => {
                const row = `<tr>
                                <td>${producto.nombre_productos}</td>
                                <td>${producto.codigo_productos}</td>
                                <td>${producto.modelo_productos}</td>
                                <td>${producto.marca_productos}</td>
                                <td>${producto.descripcion_productos}</td>
                                <td>${producto.cantidad_disponible}</td>
                                <td>${producto.precio_productos}</td>
                                <td>${producto.precio_dolares}</td>
                                <td>${producto.precio_total}</td>
                                <td>${producto.fecha_registro}</td>
                             </tr>`;
                stockBody.innerHTML += row;
            });
        }
    });

    // Función para buscar productos por nombre
    document.getElementById('searchProduct').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const stockBody = document.getElementById('stockBody');
        stockBody.innerHTML = ''; // Limpiar tabla antes de agregar nuevos datos

        const filteredStock = stockData.filter(producto => 
            producto.nombre_productos.toLowerCase().includes(searchTerm)
        );

        if (filteredStock.length === 0) {
            stockBody.innerHTML = '<tr><td colspan="10" class="text-center">No se encontraron productos.</td></tr>';
        } else {
            filteredStock.forEach(producto => {
                const row = `<tr>
                                <td>${producto.nombre_productos}</td>
                                <td>${producto.codigo_productos}</td>
                                <td>${producto.modelo_productos}</td>
                                <td>${producto.marca_productos}</td>
                                <td>${producto.descripcion_productos}</td>
                                <td>${producto.cantidad_disponible}</td>
                                <td>${producto.precio_productos}</td>
                                <td>${producto.precio_dolares}</td>
                                <td>${producto.precio_total}</td>
                                <td>${producto.fecha_registro}</td>
                             </tr>`;
                             stockBody.innerHTML += row;
            });
        }
    });

                document.getElementById('btnReporte').addEventListener('click', function() {
                    const startDate = document.getElementById('start-date').value;
                    const endDate = document.getElementById('end-date').value;

                    // Verifica que las fechas no estén vacías
                    if (startDate && endDate) {
                        window.location.href = 'http://localhost/inventario/Estadisticas/generar_reporte.php?start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);
                    } else {
                        alert("Por favor, selecciona ambas fechas.");
                    }
                });
            </script>
        </div> <!-- Fin del contenedor principal -->
    </main>
</body>
</html>