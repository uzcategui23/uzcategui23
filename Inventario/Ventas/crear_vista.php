
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include './Commons/head.php'; ?>
    <?php include './Commons/nav.php'; ?>
    <?php include './Commons/conexion.php'; ?>
    <link rel="stylesheet" type="text/css" href="http://localhost/inventario/Ventas/estilo_venta.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    
    <script type="text/javascript">
    function actualizar() {
        location.reload(true);
    }
</script>
</head>
<body>
    <h2 style="text-align: center;">Agregar Ventas</h2>
    
<?php
// Conectar a la base de datos
$conn = new mysqli("localhost", "root", "", "sistema");

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Registrar venta
if (isset($_POST['registro_venta'])) {
    $id_cliente = $_POST['cliente_id'] ?? null;              
    $id_productos = $_POST['producto_venta'] ?? null; 
    $cantidad_venta = $_POST['cantidad_venta'] ?? []; 
    $precio_venta = $_POST['precio_venta'] ?? []; 
    $precio_dolar = $_POST['precio_dolar'] ?? []; 
    $fecha_venta = $_POST['fecha_venta'];

    // Inicializa el total de la venta
    $total_venta = 0;

    // Calcula el total para cada producto
    foreach ($cantidad_venta as $index => $cantidad) {
        if (isset($precio_dolar[$index])) {
            $total_venta += $cantidad * $precio_dolar[$index];
        }
    }
    if (!$id_cliente) {
        echo "<div class='mensaje error'>Error: Debes seleccionar un cliente.</div>";
    } elseif (!$id_productos) {
        echo "<div class='mensaje error'>Error: Debes seleccionar un producto.</div>";
    } elseif (array_sum($cantidad_venta) <= 0) {
        echo "<div class='mensaje error'>Error: La cantidad debe ser mayor que cero.</div>";
    } else {
        // Verificación del ID del cliente
        $sql_verificar_cliente = "SELECT * FROM clientes WHERE id_cliente = '$id_cliente'";
        $result_verificar_cliente = $conn->query($sql_verificar_cliente);

        if ($result_verificar_cliente->num_rows == 0) {
            echo "<div class='mensaje error'>Error: El cliente seleccionado no existe.</div>";
        } else {
            // Verificación del ID del producto y procesamiento de la venta
            foreach ($id_productos as $index => $producto_id) {
                $sql_verificar_producto = "SELECT * FROM productos WHERE id_productos = '$producto_id'";
                $result_verificar_producto = $conn->query($sql_verificar_producto);
                
                if ($result_verificar_producto->num_rows == 0) {
                    echo "<div class='mensaje error'>Error: El producto seleccionado no existe.</div>";
                    continue;
                }
                
                $producto = $result_verificar_producto->fetch_assoc();
                $cantidad_disponible = $producto['cantidad_disponible'];

                if (isset($cantidad_venta[$index]) && isset($precio_dolar[$index]) && ($cantidad_venta[$index] > $cantidad_disponible)) {
                    echo "<div class='mensaje error'>Error: No hay suficiente stock disponible para el producto ID: {$producto_id}.</div>";
                    continue;
                }

                // Inserción en la base de datos para cada producto
$cantidad = $cantidad_venta[$index] ?? 0; // Default to 0 if not set
$precio = $precio_venta[$index] ?? 0; // Default to 0 if not set
$dolar = $precio_dolar[$index] ?? 0; // Default to 0 if not set

// Se eliminó la verificación
$sql_venta = "INSERT INTO ventas (id_cliente, producto_venta, cantidad_venta, precio_venta, precio_dolar, fecha_venta, total_venta) 
              VALUES ('$id_cliente', '$producto_id', '{$cantidad}', '{$precio}', '{$dolar}', '$fecha_venta', '$total_venta')";

if ($conn->query($sql_venta) === TRUE) {
    // Actualizar el stock del producto
    $nueva_cantidad = $cantidad_disponible - $cantidad;
    $sql_actualizar_stock = "UPDATE productos SET cantidad_disponible = '$nueva_cantidad' WHERE id_productos = '$producto_id'";
    
    if ($conn->query($sql_actualizar_stock) === FALSE) {
        echo "<div class='mensaje error'>Error al actualizar el stock para el producto ID: {$producto_id}. Error: " . $conn->error . "</div>";
    }

    // Verificar si se ha alcanzado el stock mínimo
    if ($nueva_cantidad == $producto['stock_minimo']) {
        echo "<script>alert('¡Alerta! El stock mínimo registrado para el producto ID: {$producto_id} ha sido alcanzado.');</script>";
    }
} else {
    echo "<div class='mensaje error'>Error al registrar la venta para el producto ID: {$producto_id}. Error: " . $conn->error . "</div>";
}

echo "<div class='mensaje exito'>Venta registrada exitosamente.</div>";
echo "<script>actualizar();</script>";
            }
        }
    }
}

?>
<?php
 // Registrar cliente con AJAX
if (isset($_POST['registro_cliente_modal'])) {
    $nombre_cliente = $_POST['nombre_cliente'];
    $cedula_cliente = $_POST['cedula_cliente'];
    $telefono_cliente = $_POST['telefono_cliente'];

    $sql_verificar_cliente = "SELECT * FROM clientes WHERE cedula_cliente = '$cedula_cliente'";
    $result_verificar = $conn->query($sql_verificar_cliente);

    if ($result_verificar->num_rows > 0) {
        echo "<div class='mensaje error'>Error: El cliente con cédula $cedula_cliente ya está registrado.</div>";
    } else {
        $sql_clientes = "INSERT INTO clientes (nombre_cliente, cedula_cliente, telefono_cliente) VALUES ('$nombre_cliente', '$cedula_cliente', '$telefono_cliente')";

        if ($conn->query($sql_clientes) === TRUE) {
            echo "<div class='mensaje exito'>Cliente registrado exitosamente.</div>";
        } else {
            echo "<div class='mensaje error'>Error al registrar Cliente: " . $conn->error . "</div>";
        }
    }
}

    // Editar cliente con AJAX
if (isset($_POST['editar_cliente'])) {
    $id_cliente_editar = $_POST['id_cliente_editar'];
    $nombre_cliente_editar = $_POST['nombre_cliente_editar'];
    $cedula_cliente_editar = $_POST['cedula_cliente_editar'];
    $telefono_cliente_editar = $_POST['telefono_cliente_editar'];

    $sql_actualizar = "UPDATE clientes SET nombre_cliente='$nombre_cliente_editar', cedula_cliente='$cedula_cliente_editar', telefono_cliente='$telefono_cliente_editar' WHERE id_cliente='$id_cliente_editar'";

    if ($conn->query($sql_actualizar) === TRUE) {
        echo "<div class='mensaje exito'>Cliente actualizado exitosamente.</div>";
    } else {
          echo "<div class='mensaje error'>Error al actualizar Cliente: " .$conn->error . "</div>";
    }
}

// Eliminar cliente con AJAX
if (isset($_POST['eliminar_cliente'])) {
    $id_cliente_eliminar = $_POST['id_cliente_eliminar'];

    // Intentar eliminar el cliente
    try {
        // Consulta para eliminar el cliente
        $sql_eliminar = "DELETE FROM clientes WHERE id_cliente='$id_cliente_eliminar'";
        
        if ($conn->query($sql_eliminar) === TRUE) {
            echo "<div class='mensaje exito'>Cliente eliminado exitosamente.</div>";
        }
    } catch (mysqli_sql_exception $e) {
        // Verificar si el error es por clave foránea
        if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
            echo "<div class='mensaje error'>No se puede eliminar el cliente porque tiene ventas asociadas. Por favor, elimina las ventas primero.</div>";
        } else {
            // Mostrar el mensaje de error completo
           echo "Error al eliminar Cliente: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>   <style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 20px;
}
.container {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 20px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
.left {
    flex: 1;
    margin-right: 20px;
    padding: 20px;
}
.right {
    flex: 1;
}
h2 {
    text-align: center;
    color: #4B6F8E;
}
h3 {
    color: #333;
}
p {
    line-height: 1.6;
    font-size: 16px;
    color: #555;
    margin: 5px 0;
}
label {
    display: block;
    margin-top: 10px;
}
input, select {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
input[type="submit"] {
    background-color: #4B6F8E;
    color: white;
    cursor: pointer;
    border: none;
    margin-top: 20px;
}
.instructions {
    background-color: #e9f5ff;
    border-left: 5px solid #4B6F8E;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
}
img {
    width: 150px;
    height: auto;
    border-radius: 10px;
    margin-left: 20px;
}
#inventario-form input {
    width: 500px; /* Ancho específico */
    padding: 5px; /* Ajustar padding si es necesario */
    margin-bottom: 10px; /* Espaciado reducido */
}
</style>
<script type="text/javascript">
function toggleText() {
    const moreText = document.querySelector('.more-text');
    moreText.style.display = moreText.style.display === 'none' ? 'block' : 'none';
}
function actualizar() {
    location.reload(true);
}
</script>
<div class="container">
<!-- Sección de texto e imagen a la izquierda -->
<div class="left">
    <div class="instructions">
    <h3>Bienvenido al módulo de registro de ventas!</h3>
<img src="http://localhost/inventario/Ventas/counter-309880_1920.png" alt="Descripción de la imagen" style="width: 150px; height: auto; border-radius: 10px; margin-top: 10px;">
        <h4>Selección de Cliente:</h4>
        <p>En el formulario, podrá seleccionar un cliente existente o buscarlo ingresando su nombre o cédula.</p>
        <h4>Selección de Producto:</h4>
        <p>seleccione un producto del listado. También tiene la opción de buscar el producto mediante su nombre o código.</p>
        <h4>Detalles del Producto:</h4>
        <p>Al seleccionar un producto, se mostrarán automáticamente:</p>
        <ul>
            <li>El precio en bolívares registrado.</li>
            <li>El precio en dólares del producto.</li>
        </ul>
        <p>Ingrese la cantidad del producto que desea vender. El sistema calculará automáticamente el valor total basado en la cantidad seleccionada.</p>
        <h4>Agregar Más Productos:</h4>
        <p>Si desea incluir otro producto en la venta, haga clic en el botón "Agregar otro producto". Esto le permitirá repetir el proceso de selección y agregar más artículos a la venta.</p>
        <h4>Total General y Fecha:</h4>
        <p>El sistema calculará automáticamente el total general de la venta, sumando todos los productos seleccionados. La fecha de la venta se registrará automáticamente con la fecha actual.</p>
    </div>
</div>

<!-- Formulario a la derecha -->
<div class="right">
    <form action="" method="POST" id="inventario-form">
        <label for="cliente">Cliente:</label>
        <select id="cliente" name="cliente_id" required>
            <option value="">Seleccione un cliente</option>
                    <?php
                    // Conectar a la base de datos
                    $conn = new mysqli("localhost", "root", "", "sistema");

                    // Verificar conexión
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Obtener todos los clientes
                    $sql = "SELECT id_cliente, nombre_cliente, cedula_cliente, telefono_cliente FROM clientes";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {         
                            echo "<option value='{$row['id_cliente']}'>{$row['nombre_cliente']} - {$row['cedula_cliente']} - {$row['telefono_cliente']}</option>";
                        }
                    }
                    $conn->close();
                    ?>
                </select>

                <button type="button" id="btnOpciones" style="background-color:#4B6F8E; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer;">Opciones</button>
                <div id="opciones" style="display: none; margin-top: 10px;">
                    <button type="button" id="btnAgregarCliente" style="background-color: #4B6F8E; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer;">Agregar Cliente</button>
                    <button type="button" id="btnEditarCliente" style="background-color: #4B6F8E; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer;">Editar Cliente</button>
                    <button type="button" id="btnEliminarCliente" style="background-color: #4B6F8E; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer;">Eliminar Cliente</button>
                </div>

                <script>
                $(document).ready(function() {
                    // Inicializar Select2
                    $('#cliente').select2({
                        placeholder: 'Buscar cliente...',
                        allowClear: true
                    });

                    // Filtrar clientes basado en la búsqueda
                    $('#search_cliente').on('keyup', function() {
                        var searchTerm = $(this).val().toLowerCase();
                        $('#cliente option').each(function() {
                            var text = $(this).text().toLowerCase();
                            $(this).toggle(text.includes(searchTerm));
                        });
                        $('#cliente').select2('open'); // Abrir el select después de filtrar
                    });

                    // Mostrar/Ocultar opciones al hacer clic en el botón
                    $('#btnOpciones').on('click', function() {
                        $('#opciones').toggle();
                    });
                });
                </script>

                <br>
              

                <?php
                date_default_timezone_set("America/Caracas");
                $hora_fecha = date("Y-m-d");
                ?>
<div id="productos">
    <div class="producto">
        <label for="producto_venta">Producto:</label>
        <select id="producto_venta" name="producto_venta[]" required onchange="cargarDetallesProducto(this)">
            <option value="">Seleccione un producto</option>
            <?php
            // Conectar a la base de datos y obtener productos
            $enlace = mysqli_connect("localhost", "root", "", "sistema");
            $consultaProductos = "SELECT id_productos, nombre_productos, codigo_productos, modelo_productos, marca_productos, precio_dolares, precio_total, cantidad_disponible FROM productos";
            $resultadoProductos = mysqli_query($enlace, $consultaProductos);
            while ($producto = mysqli_fetch_assoc($resultadoProductos)) {
                // Calcular IVA y nuevo precio con aumento
                $precioDolares = (float)$producto["precio_dolares"];
                $iva = round($precioDolares * 0.16, 2);
                $precioConIVA = round($precioDolares + $iva, 2);
                $nuevoPrecioConAumento = round($precioConIVA * 1.40, 2);

                echo "<option value='{$producto['id_productos']}' 
                             data-precio-bolivares='{$producto['precio_total']}' 
                             data-precio-total='{$producto['precio_total']}' 
                             data-precio-dolar-aumento='{$nuevoPrecioConAumento}'>
                        {$producto['nombre_productos']} - {$producto['codigo_productos']} - {$producto['modelo_productos']} - {$producto['marca_productos']} - {$nuevoPrecioConAumento} - {$producto['cantidad_disponible']}
                      </option>";
            }
            mysqli_close($enlace);
            ?>
        </select>

        <script>
            $(document).ready(function() {
                // Inicializar Select2
                $('#producto_venta').select2({
                    placeholder: 'Buscar producto...',
                    allowClear: true
                });
            });
        </script>
<script>
    function cargarDetallesProducto(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];

        const precioBolivares = selectedOption.getAttribute('data-precio-bolivares');
        const nuevoPrecioConAumento = parseFloat(selectedOption.getAttribute('data-precio-dolar-aumento'));

        // Actualizar los campos correspondientes y formatear a dos decimales
        document.querySelector('input[name="precio_dolar[]"]').value = nuevoPrecioConAumento.toFixed(2); // Precio en dólares con aumento
        document.querySelector('input[name="total_venta[]"]').value = (nuevoPrecioConAumento * parseFloat(document.querySelector('input[name="cantidad_venta[]"]').value || 0)).toFixed(2); // Calcular total basado en cantidad
    }
    
</script>
        <label for="precio_dolar">Precio en Dólares:</label>
        <input type="number" name="precio_dolar[]" step="0.01" id="precio_dolar">

        <label for="cantidad_venta">Cantidad:</label>
        <input type="number" name="cantidad_venta[]" placeholder="Cantidad" oninput="calcularTotal(this)">

        <label for="total_venta">Total:</label>
        <input type="number" name="total_venta[]" required step="0.01" readonly title="Este campo muestra el total de la venta">
    </div>
</div>

<button type="button" onclick="agregarProducto()">Agregar otro producto</button>
    
    <div id="cantidad_total_container">
    <label for="cantidad_total">Cantidad Total de Productos:</label>
    <input type="number" id="cantidad_total" value="0" readonly  title="Este campo muestra el numero de ventas" >

    <label for="total_general">Total General:</label>
    <input type="number" id="total_general" value="0" readonly  title="Este campo muestra el total de todos los productos" >
</div>


    <label for="fecha_venta">Fecha:</label>
    <input type="date" id="fecha_venta" name="fecha_venta" value="<?php echo $hora_fecha; ?>" readonly>

    <input type="submit" name="registro_venta" value="Agregar venta">
</form>

<script>
function agregarProducto() {
    const productosDiv = document.getElementById('productos');
    const nuevoProducto = document.createElement('div');
    nuevoProducto.classList.add('producto');

    nuevoProducto.innerHTML = `
        <label for="producto_venta">Producto:</label>
        <select name="producto_venta[]" required onchange="cargarDetallesProducto(this)">
            <option value="">Seleccione un producto</option>
            <?php
           // Conectar a la base de datos y obtener productos
           $enlace = mysqli_connect("localhost", "root", "", "sistema");
           $consultaProductos = "SELECT id_productos, nombre_productos, codigo_productos, modelo_productos, marca_productos, precio_dolares, precio_total, cantidad_disponible FROM productos";
           $resultadoProductos = mysqli_query($enlace, $consultaProductos);
           while ($producto = mysqli_fetch_assoc($resultadoProductos)) {
            // Calcular IVA y nuevo precio con aumento
            $precioDolares = (float)$producto["precio_dolares"];
            $iva = round($precioDolares * 0.16, 2);
            $precioConIVA = round($precioDolares + $iva, 2);
            $nuevoPrecioConAumento = round($precioConIVA * 1.40, 2);

            echo "<option value='{$producto['id_productos']}' 
                         data-precio-bolivares='{$producto['precio_total']}' 
                         data-precio-total='{$producto['precio_total']}' 
                         data-precio-dolar-aumento='{$nuevoPrecioConAumento}'>
                    {$producto['nombre_productos']} - {$producto['codigo_productos']} - {$producto['modelo_productos']} - {$producto['marca_productos']} - {$nuevoPrecioConAumento} - {$producto['cantidad_disponible']}
                  </option>";
        }
        mysqli_close($enlace);
        ?>

        <label for="precio_dolar">Precio en Dolares:</label>
        <input type="number" name="precio_dolar[]"  placeholder="Precio en Dolares" required step="0.01">

          <label for="cantidad_venta">Cantidad:</label>
        <input type="number" name="cantidad_venta[]" placeholder="Cantidad" oninput="calcularTotal(this)">

        <label for="total_venta">Total:</label>
        <input type="number" name="total_venta[]" required step="0.01" readonly>
    `;

    productosDiv.appendChild(nuevoProducto);
    actualizarCantidadTotal();
}

function cargarDetallesProducto(select) {
    const productoDiv = select.closest('.producto');
    const precioBolivares = select.options[select.selectedIndex].getAttribute('data-precio-bolivares');

    productoDiv.querySelector('input[name="precio_venta[]"]').value = precioBolivares;

    const cantidad = productoDiv.querySelector('input[name="cantidad_venta[]"]').value;
    if (cantidad) {
        calcularTotal(productoDiv.querySelector('input[name="cantidad_venta[]"]'));
    }
}

function calcularTotal(input) {
    const productoDiv = input.closest('.producto');
    const cantidad = productoDiv.querySelector('input[name="cantidad_venta[]"]').value;
    const precioDolar = productoDiv.querySelector('input[name="precio_dolar[]"]').value;

    const total = cantidad * precioDolar;
    productoDiv.querySelector('input[name="total_venta[]"]').value = total.toFixed(2);
    
    actualizarCantidadTotal();
}

function actualizarCantidadTotal() {
    const cantidadInputs = document.querySelectorAll('input[name="cantidad_venta[]"]');
    let totalCantidad = 0;

    cantidadInputs.forEach(input => {
        const cantidad = parseInt(input.value) || 0; // Convertir a número o 0 si está vacío
        totalCantidad += cantidad;
    });

    document.getElementById('cantidad_total').value = totalCantidad;
 // Actualizar el total general
 actualizarTotalGeneral();
}

function actualizarTotalGeneral() {
    const totalInputs = document.querySelectorAll('input[name="total_venta[]"]');
    let totalGeneral = 0;

    totalInputs.forEach(input => {
        const totalVenta = parseFloat(input.value) || 0; // Convertir a número o 0 si está vacío
        totalGeneral += totalVenta;
    });

    document.getElementById('total_general').value = totalGeneral.toFixed(2); // Mostrar el total general
}
</script>
    <script>
        function filterClients() {
            const input = document.getElementById('search_cliente');
            const filter = input.value.toLowerCase();
            const select = document.getElementById('cliente');
            const options = select.options;

            for (let i = 1; i < options.length; i++) { // Comenzar desde 1 para omitir la opción predeterminada
                const txtValue = options[i].text.toLowerCase();
                options[i].style.display = txtValue.includes(filter) ? "" : "none"; // Mostrar u ocultar opción
            }
        }
    </script>

    <!-- Modal para agregar cliente -->
    <div id="modalCliente" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Registrar Cliente</h3>
            <script>
                function validarFormulario() {
                    var cedula = document.getElementById("cedula_cliente").value;
                    var telefono = document.getElementById("telefono_cliente").value;

                    // Validar cédula
                    if (!validarNumeros(cedula)) {
                        alert("¡La cédula debe contener solo números!");
                        return false;
                    }

                    // Validar teléfono
                    if (!validarNumeros(telefono)) {
                        alert("¡El teléfono debe contener solo números!");
                        return false;
                    }

                    return true; // Si todo es válido, permitir el envío del formulario
                }

                function validarNumeros(valor) {
                    var regex = /^\d+$/; // Asegura que solo contenga dígitos
                    return regex.test(valor);
                }
            </script>
            <form action="" method="POST" onsubmit="return validarFormulario()">
                <label for="nombre_cliente">Nombre y Apellido del Cliente:</label>
                <input type="text" id="nombre_cliente" name="nombre_cliente" required>

                <label for="cedula_cliente">Cédula del Cliente:</label>
                <input type="text" id="cedula_cliente" name="cedula_cliente" required>

                <label for="telefono_cliente">Teléfono del Cliente:</label>
                <input type="text" id="telefono_cliente" name="telefono_cliente" required>
                
                <input type="submit" name="registro_cliente_modal" value="Agregar Cliente">
            </form>
        </div>
    </div>

    <!-- Modal para editar cliente -->
    <div id="modalEditarCliente" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('modalEditarCliente').style.display='none';">&times;</span>
            <h3>Editar Cliente</h3>
            <form id="formEditarCliente" action="" method="POST">
                <input type="hidden" id="id_cliente_editar" name="id_cliente_editar">
                <label for="nombre_cliente_editar">Nombre y Apellido del Cliente:</label>
                <input type="text" id="nombre_cliente_editar" name="nombre_cliente_editar" required>

                <label for="cedula_cliente_editar">Cédula del Cliente:</label>
                <input type="text" id="cedula_cliente_editar" name="cedula_cliente_editar" required>

                <label for="telefono_cliente_editar">Teléfono del Cliente:</label>
                <input type="text" id="telefono_cliente_editar" name="telefono_cliente_editar" required>
                
                <input type="submit" name="editar_cliente" value="Guardar Cambios">
            </form>
        </div>
    </div>

    <!-- Modal para eliminar cliente -->
    <div id="modalEliminarCliente" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('modalEliminarCliente').style.display='none';">&times;</span>
            <h3>Eliminar Cliente</h3>
            <p>¿Estás seguro de que deseas eliminar a <strong id="nombre_cliente_eliminar"></strong>?</p>
            <form id="formEliminarCliente" action="" method="POST">
                <input type="hidden" id="id_cliente_eliminar" name="id_cliente_eliminar">
                <input type="submit" name="eliminar_cliente" value="Eliminar Cliente">
            </form>
        </div>
    </div>

    <script>
        // Obtener el modal
        var modal = document.getElementById("modalCliente");
        var modalEditar = document.getElementById("modalEditarCliente");
        var modalEliminar = document.getElementById("modalEliminarCliente");

        // Obtener el botón que abre el modal para agregar cliente
        var btnAgregar = document.getElementById("btnAgregarCliente");

        // Cuando el usuario hace clic en el botón, abre el modal
        btnAgregar.onclick = function() {
            modal.style.display = "block";
        }

        // Obtener el botón que abre el modal para editar cliente
        $('#btnEditarCliente').on('click', function() {
            var selectedOption = $('#cliente option:selected');
            if (selectedOption.val() !== "") {
                var idCliente = selectedOption.val();
                var nombreCliente = selectedOption.text().split(' - ')[0];
                var cedulaCliente = selectedOption.text().split(' - ')[1];
                var telefonoCliente = selectedOption.text().split(' - ')[2];

                $('#id_cliente_editar').val(idCliente);
                $('#nombre_cliente_editar').val(nombreCliente);
                $('#cedula_cliente_editar').val(cedulaCliente);
                $('#telefono_cliente_editar').val(telefonoCliente);

                modalEditar.style.display = "block";
            } else {
                alert("Por favor, selecciona un cliente para editar.");
            }
        });

        // Obtener el botón que abre el modal para eliminar cliente
        $('#btnEliminarCliente').on('click', function() {
            var selectedOption = $('#cliente option:selected');
            if (selectedOption.val() !== "") {
                var idCliente = selectedOption.val();
                var nombreCliente = selectedOption.text().split(' - ')[0];

                $('#id_cliente_eliminar').val(idCliente);
                $('#nombre_cliente_eliminar').text(nombreCliente);

                modalEliminar.style.display = "block";
            } else {
                alert("Por favor, selecciona un cliente para eliminar.");
            }
        });

        // Cerrar modales al hacer clic en la "x"
        var spans = document.getElementsByClassName("close");
        for (var i = 0; i < spans.length; i++) {
            spans[i].onclick = function() {
                modal.style.display = "none";
                modalEditar.style.display = "none";
                modalEliminar.style.display = "none";
            }
        }

        // Cerrar modales al hacer clic fuera de ellos
        window.onclick = function(event) {
            if (event.target == modal || event.target == modalEditar || event.target == modalEliminar) {
                modal.style.display = "none";
                modalEditar.style.display = "none";
                modalEliminar.style.display = "none";
            }
        }
    
    </script>
    