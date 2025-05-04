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
            </div>
            <div class="col">
            </div>
          </div>
        </div>
        <h2 style="text-align: center;">Estadística</h2>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Button with Icons</title>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Incluir Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container mt-5">
    <button id="btnVenta" class="btn btn-primary">
    <i class="fa fa-shopping-cart"></i> Venta
</button>
<button id="btnProductos" class="btn btn-secondary d-none">
    <i class="fa fa-box"></i> Productos Más Vendidos
</button>

        <div id="tablaProductos" class="mt-4 d-none">
            <h2>Productos Más Vendidos</h2>
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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <?php
        // Conexión a la base de datos
 // Conectar a la base de datos
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
                GROUP BY p.nombre_productos, c.nombre_cliente, c.cedula_cliente, c.telefono_cliente, v.fecha_venta
                ORDER BY total_vendido DESC";

        $result = $conn->query($sql);
        
        // Guardar resultados en un array
        $productos = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }
        }
        
        // Cerrar conexión
        $conn->close();
    ?>
    
   <script type="text/javascript">
    const productosData = <?php echo json_encode($productos); ?>;

    document.getElementById('btnVenta').addEventListener('click', function() {
        document.getElementById('btnProductos').classList.remove('d-none');
    });

    document.getElementById('btnProductos').addEventListener('click', function() {
        const tablaBody = document.getElementById('productosBody');
        tablaBody.innerHTML = ''; // Limpiar tabla antes de agregar nuevos datos

        // Datos para la gráfica
        const nombresProductos = [];
        const totalVendidos = [];

        // Usar un objeto para acumular las cantidades
        const acumulador = {};

        productosData.forEach(producto => {
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

        // Convertir el objeto acumulador a arrays para la gráfica
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

        document.getElementById('tablaProductos').classList.remove('d-none');
    });
</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>