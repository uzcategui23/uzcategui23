<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include './Commons/head.php' ?>
    <?php include './Commons/nav.php' ?>

    <title>Buscador</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
        }
        h2 {
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="date"],
        select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        button {
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        img {
            width: 50px; /* Ajusta el tamaño según sea necesario */
            height: auto; /* Mantiene la proporción */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Buscador</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label for="tipo">Buscar en:</label>
                <select id="tipo" name="tipo" required>
                    <option value="">Seleccione una opción</option>
                    <option value="ventas">Ventas</option>
                    <option value="productos">Productos</option>
                </select>
            </div>

            <div class="form-group" id="busqueda-ventas" style="display: none;">
                <label for="fecha_venta">Fecha de Venta:</label>
                <input type="date" id="fecha_venta" name="fecha_venta">
            </div>

            <div class="form-group" id="busqueda-productos" style="display: none;">
                <label for="nombre_producto">Nombre del Producto:</label>
                <input type="text" id="nombre_producto" name="nombre_producto">

                <label for="codigo_producto">Código del Producto:</label>
                <input type="text" id="codigo_producto" name="codigo_producto">
            </div>
            
            <button type="submit">Buscar</button>
        </form>

        <!-- Resultados -->
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Conectar a la base de datos
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "sistema";

            $conn = new mysqli($servername, $username, $password, $dbname);

            // Verificar conexión
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            if ($_POST['tipo'] == 'ventas' && !empty($_POST['fecha_venta'])) {
                // Buscar ventas por fecha
                $fecha_venta = $_POST['fecha_venta'];
                $sql = "SELECT v.*, c.nombre_cliente, c.cedula_cliente, c.telefono_cliente 
                        FROM ventas v 
                        JOIN clientes c ON v.id_cliente = c.id_cliente 
                        WHERE v.fecha_venta = '$fecha_venta'";
                $result = $conn->query($sql);
        
                echo "<h3>Resultados de Ventas:</h3>";
                if ($result->num_rows > 0) {
                    echo "<table><tr><th>Nombre Cliente</th><th>Cédula</th><th>Teléfono</th><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Total</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['nombre_cliente']}</td>
                                <td>{$row['cedula_cliente']}</td>
                                <td>{$row['telefono_cliente']}</td>
                                <td>{$row['producto_venta']}</td>
                                <td>{$row['cantidad_venta']}</td>
                                <td>{$row['precio_venta']}</td>
                                <td>{$row['total_venta']}</td>
                              </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No se encontraron ventas para esa fecha.</p>";
                }
            } 
            elseif ($_POST['tipo'] == 'productos') {
                $nombre_producto = $_POST['nombre_producto'];
                $codigo_producto = $_POST['codigo_producto'];
                
                // Construir la consulta
                $sql = "SELECT * FROM productos WHERE 1=1";
                
                if (!empty($nombre_producto)) {
                    $sql .= " AND nombre_productos LIKE '%$nombre_producto%'";
                }
                
                if (!empty($codigo_producto)) {
                    $sql .= " AND codigo_productos LIKE '%$codigo_producto%'";
                }
                
                $result = $conn->query($sql);
            
                echo "<h3>Resultados de Productos:</h3>";
                if ($result->num_rows > 0) {
                    echo "<table><tr><th>ID</th><th>Imagen</th><th>Nombre Producto</th><th>Código</th><th>Modelo</th><th>Marca</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        // Suponiendo que 'imagen_producto' solo contiene el nombre del archivo
                        $nombre_imagen = htmlspecialchars($row['imagen_producto']);
                        $ruta_imagen = "http://localhost/inventario/" . $nombre_imagen; // Construir la ruta completa
                
                        echo "<tr>
                                <td>{$row['id_productos']}</td>
                                <td>";
                        
                        // Mostrar la imagen o un mensaje si no hay imagen
                        if (!empty($nombre_imagen)) {
                            echo "<img src='$ruta_imagen' width='50' height='50' alt=''>";
                        } else {
                            echo "<p>No hay imagen disponible</p>";
                        }
                
                        echo "</td>
                              <td>{$row['nombre_productos']}</td>
                              <td>{$row['codigo_productos']}</td>
                              <td>{$row['modelo_productos']}</td>
                              <td>{$row['marca_productos']}</td>
                              </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No se encontraron productos con ese nombre o código.</p>";
                }
            }

            // Cerrar conexión
            $conn->close();
        }
        ?>
    </div>

    <script>
        document.getElementById('tipo').addEventListener('change', function() {
            var tipoSeleccionado = this.value;

            document.getElementById('busqueda-ventas').style.display = (tipoSeleccionado === 'ventas') ? 'block' : 'none';
            document.getElementById('busqueda-productos').style.display = (tipoSeleccionado === 'productos') ? 'block' : 'none';
        });
    </script>
</body>
</html>
