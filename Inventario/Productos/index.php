<?php
// Conectar a la base de datos
$conn = new mysqli("localhost", "root", "", "sistema");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Manejo de eliminación de producto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_producto'])) {
    $id_productos = $_POST['id_productos'];
    $sql_eliminar_producto = "DELETE FROM productos WHERE id_productos='$id_productos'";

    if ($conn->query($sql_eliminar_producto) === TRUE) {
        $_SESSION['mensaje'] = "Producto eliminado exitosamente.";
    } else {
        $_SESSION['mensaje'] = "Error al eliminar producto: " . $conn->error;
    }

    header("Location: " . $_SERVER['PHP_SELF']); // Redirigir a la misma página
    exit();
}
// Obtener la tasa de cambio correspondiente a la fecha actual
$sql_tasa = "SELECT tasa FROM tasas_dolar WHERE fecha = CURDATE() LIMIT 1";
$result_tasa = $conn->query($sql_tasa);
$tasa_dolar = '0.00'; // Valor por defecto
if ($result_tasa && $result_tasa->num_rows > 0) {
    $row_tasa = $result_tasa->fetch_assoc();
    $tasa_dolar = htmlspecialchars($row_tasa['tasa']);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_cambios'])) {
    $id_productos = $_POST['id_productos'];
    $nombre_productos = $_POST['nombre_productos'];
    $codigo_productos = $_POST['codigo_productos'];
    $descripcion_productos = $_POST['descripcion_productos'];
    $cantidad_disponible = $_POST['cantidad_disponible'];
    $precio_dolares = $_POST['precio_dolares'];
    $precio_total = $_POST['precio_total'];
    $precio_productos = $_POST['precio_productos']; // Asegúrate de capturar este valor

    // Actualizar el producto
    $sql_actualizar_producto = "UPDATE productos SET 
        nombre_productos='$nombre_productos', 
        codigo_productos='$codigo_productos', 
        descripcion_productos='$descripcion_productos', 
        cantidad_disponible='$cantidad_disponible', 
        precio_dolares='$precio_dolares', 
        precio_total='$precio_total', 
        precio_productos='$precio_productos', 
        fecha_registro=CURDATE() 
        WHERE id_productos='$id_productos'";

    if ($conn->query($sql_actualizar_producto) === TRUE) {
        $_SESSION['mensaje'] = "Producto actualizado exitosamente.";
    } else {
        $_SESSION['mensaje'] = "Error al actualizar producto: " . $conn->error;
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
$result = $conn->query("SELECT * FROM productos");

// Mostrar mensajes de éxito o error
if (isset($_SESSION['mensaje'])) {
    $mensaje = "<div class='alert alert-info'>" . $_SESSION['mensaje'] . "</div>";
    unset($_SESSION['mensaje']); // Limpiar el mensaje después de mostrarlo
}

// Obtener productos registrados con sus proveedores
$sql = "
    SELECT 
        p.id_productos,  
        p.imagen_producto,  
        p.nombre_productos, 
        p.codigo_productos, 
        p.modelo_productos, 
        p.marca_productos, 
        p.descripcion_productos, 
        p.cantidad_disponible, 
        p.precio_productos,
        p.precio_dolares,
        p.precio_total,
        pr.nombre_proveedor,
        pr.documento_proveedor,
        pr.telefono_proveedor,
        pr.direccion_proveedor
    FROM productos p
    LEFT JOIN proveedores pr ON p.ID_PROVEEDOR = pr.id_proveedor";

    $result = $conn->query($sql);

    // Obtener la tasa de cambio más reciente
    $sql_tasa = "SELECT tasa FROM tasas_dolar ORDER BY fecha DESC LIMIT 1";
    $result_tasa = $conn->query($sql_tasa);
    $tasa_dolar = '0.00'; // Valor por defecto
    if ($result_tasa && $result_tasa->num_rows > 0) {
        $row_tasa = $result_tasa->fetch_assoc();
        $tasa_dolar = htmlspecialchars($row_tasa['tasa']);
    }
    ?>        

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Módulo Inventario</title>
<head>
    <?php include './Commons/head.php'; ?>
</head>
<body>
    <?php include './Commons/nav.php'; ?>
    <main>
        <div id="tabla-container_prov">
            <div class="container text-center">
                <div class="row">
                    <div class="col">
                        <h2 class="text-start">Productos Registrados</h2>
                    </div>
                    <div class="col">
                        <a href="http://localhost/inventario/index.php/productos/crear" type="button" class="btn btn-primary">Registrar</a>
                        <a href="http://localhost/inventario/index.php/productos/tasa" type="button" class="btn btn-custom">
    <i class="fa fa-dollar"></i> Tasa del Dolar </a>    
  </div>
                </div>
            </div>
            <style>
        .btn-custom {
            background-color: #007bff; /* Cambia este color según tus necesidades */
            color: white; /* Color del texto */
        }
        .btn-custom:hover {
            background-color: #0056b3; /* Color al pasar el mouse */
        }  </style>
            <?php if (isset($mensaje)) echo $mensaje; ?>

            <div style="overflow-x: auto;">
                 <!-- Campo de búsqueda -->
    <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Buscar productos..." class="form-control mb-3">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Código</th>
                <th>Marca</th>
                <th>Aplicación</th>
                <th>Descripción</th>
                <th>Stock</th>
                <th>Tasa</th>
                <th>$$$</th>
                <th>Iva</th>
                <th>40%</th>
                <th>Bs</th>
                <th>Proveedor</th>
                <th>Cédula/RIF</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
    <?php
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Calcular el IVA y el nuevo precio con un aumento del 40%
            $precioDolares = htmlspecialchars($row["precio_dolares"] ?? '');
            $iva = $precioDolares * 0.16; // IVA del 16%
            $precioConIVA = $precioDolares + $iva; // Precio total con IVA
            $nuevoPrecioConAumento = $precioConIVA * 1.40; // Aumento del 40%

            ?>
            <tr>
                <td>
                    <?php
                    // Mostrar imagen
                    $nombre_imagen = htmlspecialchars($row['imagen_producto'] ?? '');
                    $ruta_imagen = "http://localhost/inventario/" . $nombre_imagen;
                    if (!empty($nombre_imagen) && @getimagesize($ruta_imagen)) {
                        echo "<img src='$ruta_imagen' width='50' height='50' alt='Imagen del producto'>";
                    } else {
                        echo "<p>No hay imagen</p>";
                    }
                    ?>
                </td>
                <td><?php echo htmlspecialchars($row["nombre_productos"] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row["codigo_productos"] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row["marca_productos"] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row["modelo_productos"] ?? ''); ?></td>                    
                <td><?php echo htmlspecialchars($row["descripcion_productos"] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row["cantidad_disponible"] ?? ''); ?></td>
                <td><?php echo $tasa_dolar; ?></td> 
                <td><?php echo $precioDolares; ?></td>
                <td><?php echo number_format($iva, 2); ?></td> <!-- Mostrar IVA -->
                <td><?php echo number_format($nuevoPrecioConAumento, 2); ?></td> <!-- Mostrar precio con aumento -->
                        <td><?php echo htmlspecialchars($row["precio_productos"] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row["nombre_proveedor"] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row["documento_proveedor"] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row["telefono_proveedor"] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row["direccion_proveedor"] ?? ''); ?></td>
                        <td>
                        <button class="btn btn-warning" data-toggle="modal" data-target="#editModal<?php echo $row['id_productos']; ?>">Editar</button>
<!-- Modal para editar -->
<div class="modal fade" id="editModal<?php echo $row['id_productos']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <input type="hidden" name="id_productos" value="<?php echo $row['id_productos']; ?>">
                    
                    <!-- Campos para editar -->
                    <div class="form-group">
                        <label for="nombre_productos_<?php echo $row['id_productos']; ?>">Nombre</label>
                        <input type="text" class="form-control" id="nombre_productos_<?php echo $row['id_productos']; ?>" name="nombre_productos" value="<?php echo htmlspecialchars($row['nombre_productos']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="codigo_productos_<?php echo $row['id_productos']; ?>">Código</label>
                        <input type="text" class="form-control" id="codigo_productos_<?php echo $row['id_productos']; ?>" name="codigo_productos" value="<?php echo htmlspecialchars($row['codigo_productos']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="descripcion_productos_<?php echo $row['id_productos']; ?>">Descripción</label>
                        <textarea class="form-control" id="descripcion_productos_<?php echo $row['id_productos']; ?>" name="descripcion_productos" required><?php echo htmlspecialchars($row['descripcion_productos']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="cantidad_disponible_<?php echo $row['id_productos']; ?>">Cantidad Disponible</label>
                        <input type="number" class="form-control" id="cantidad_disponible_<?php echo $row['id_productos']; ?>" name="cantidad_disponible" value="<?php echo htmlspecialchars($row['cantidad_disponible']); ?>" required min="0">
                    </div>

                    <div class="form-group">
                        <label for="tasa_dolar_<?php echo $row['id_productos']; ?>">Tasa de Dólar</label>
                        <input type="number" class="form-control" id="tasa_dolar_<?php echo $row['id_productos']; ?>" name="tasa_dolar" step="0.01" required oninput="calcularPrecioEnBolivares(<?php echo $row['id_productos']; ?>)">
                    </div>

                    
                    <div class="form-group">
                        <label for="precio_dolares_<?php echo $row['id_productos']; ?>">Precio ($)</label>
                        <input type="number" class="form-control" id="precio_dolares_<?php echo $row['id_productos']; ?>" name="precio_dolares" value="<?php echo htmlspecialchars($row['precio_dolares']); ?>" step="0.01" required oninput="calcularPrecioConIVA(<?php echo $row['id_productos']; ?>)">
                    </div>

                    <div class="form-group">
                        <label for="iva_<?php echo $row['id_productos']; ?>">IVA ($)</label>
                        <input type="text" class="form-control" id="iva_<?php echo $row['id_productos']; ?>" value="0" readonly>
                    </div>

                    <div class="form-group">
                        <label for="precio_total_<?php echo $row['id_productos']; ?>">Precio Total (con IVA)</label>
                        <input type="text" class="form-control" id="precio_total_<?php echo $row['id_productos']; ?>" value="0" readonly>
                    </div>

                    <div class="form-group">
                        <label for="nuevo_precio_con_aumento_<?php echo $row['id_productos']; ?>"> Precio dólar con Aumento (40%):</label>
                        <input type="text" id="nuevo_precio_con_aumento_<?php echo $row['id_productos']; ?>" name="nuevo_precio_con_aumento" readonly>
                    </div>

                    <div class="form-group">
    <label for="precio_productos_<?php echo $row['id_productos']; ?>">Precio en Bolívares con IVA (16%) y porcentaje (40%):</label>
    <input type="number" id="precio_productos_<?php echo $row['id_productos']; ?>" name="precio_productos" step="0.01" readonly>
</div>

                    <input type="submit" name="guardar_cambios" value="Guardar cambios" class="btn btn-primary">                </form>
            </div>
        </div>
    </div>
</div>

                            <script>
function calcularPrecioConIVA(productId) {
    const precioBaseDolares = parseFloat(document.getElementById(`precio_dolares_${productId}`).value);
    const tasaIVA = 16; // Tasa de IVA en porcentaje
    const aumentoAdicional = 40; // Aumento adicional del 40%

    if (!isNaN(precioBaseDolares) && precioBaseDolares >= 0) {
        const decimalIVA = tasaIVA / 100;
        const montoIVA = precioBaseDolares * decimalIVA;
        const precioTotalConIVA = precioBaseDolares + montoIVA;

        // Calcular el nuevo precio con aumento adicional
        const decimalAumento = aumentoAdicional / 100;
        const nuevoPrecioConAumento = precioTotalConIVA + (precioTotalConIVA * decimalAumento);

        document.getElementById(`iva_${productId}`).value = montoIVA.toFixed(2);
        document.getElementById(`precio_total_${productId}`).value = precioTotalConIVA.toFixed(2);
        document.getElementById(`nuevo_precio_con_aumento_${productId}`).value = nuevoPrecioConAumento.toFixed(2); // Mostrar el precio con aumento

        // Llamar a la función para calcular el precio en bolívares
        calcularPrecioEnBolivares(nuevoPrecioConAumento, productId);
    } else {
        // Limpiar los campos si el valor es inválido
        document.getElementById(`iva_${productId}`).value = '';
        document.getElementById(`precio_total_${productId}`).value = '';
        document.getElementById(`nuevo_precio_con_aumento_${productId}`).value = '';
        document.getElementById(`precio_productos_${productId}`).value = '';
    }
}

function calcularPrecioEnBolivares(nuevoPrecioConAumento, productId) {
    const tasaDolar = parseFloat(document.getElementById(`tasa_dolar_${productId}`).value);

    if (!isNaN(tasaDolar) && tasaDolar > 0) {
        // Conversión a bolívares
        const precioEnBolivares = nuevoPrecioConAumento * tasaDolar;

        // Actualizar el campo correspondiente
        document.getElementById(`precio_productos_${productId}`).value = precioEnBolivares.toFixed(2);
    } else {
        document.getElementById(`precio_productos_${productId}`).value = '';
    }
}


</script>
</script>
<script>
// Función para filtrar la tabla
function filterTable() {
    var input, filter, table, tr, td, i, j, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.querySelector(".table");
    tr = table.getElementsByTagName("tr");

    // Iterar sobre todas las filas de la tabla (excepto la primera que es el encabezado)
    for (i = 1; i < tr.length; i++) {
        tr[i].style.display = "none"; // Ocultar la fila por defecto
        td = tr[i].getElementsByTagName("td");
        for (j = 0; j < td.length; j++) {
            if (td[j]) {
                txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = ""; // Mostrar la fila si coincide
                    break;
                }
            }
        }
    }
}
</script>

                            <button class="btn btn-danger" data-toggle="modal" data-target="#deleteModal<?php echo $row['id_productos']; ?>">Eliminar</button>
                            <!-- Modal para eliminar -->
                            <div class="modal fade" id="deleteModal<?php echo $row['id_productos']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel">Eliminar Producto</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            ¿Estás seguro de que deseas eliminar este producto?
                                        </div>
                                        <div class="modal-footer">
                                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" style="display:inline;">
                                                <input type="hidden" name="id_productos" value="<?php echo $row['id_productos']; ?>">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                <button type="submit" name="eliminar_producto" class="btn btn-danger">Confirmar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="13">No existen productos registrados.</td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include './Commons/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Ocultar el mensaje después de 5 segundos
        setTimeout(function() {
            const mensajeDiv = document.querySelector('.alert');
            if (mensajeDiv) {
                mensajeDiv.style.display = 'none';
            }
        }, 5000);
    </script>
</body>
</html>