
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include './Commons/head.php'; ?>
    <link rel="stylesheet" type="text/css" href="http://localhost/inventario/Productos/estilo.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <title>Registrar Productos</title>
</head>
<body>
<?php include './Commons/nav.php'; ?>
<?php
    // Conectar a la base de datos
    $conn = new mysqli("localhost", "root", "", "sistema");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Inicializar mensaje
    $mensaje = '';

    // Registrar producto
    if (isset($_POST['registro_producto'])) {
        $id_proveedor = $_POST['proveedor_id'];
        $nombre_productos = $_POST['nombre_productos'];
        $codigo_productos = $_POST['codigo_productos'];
        $modelo_productos = $_POST['modelo_productos'];
        $marca_productos = $_POST['marca_productos'];
        $descripcion_productos = $_POST['descripcion_productos'];
        $cantidad_disponible = $_POST['cantidad_disponible'];
        $stock_minimo = $_POST['stock_minimo'];
        $precio_productos = $_POST['precio_productos'];
        $precio_dolares = $_POST['precio_dolares'];
        $precio_total = $_POST['precio_total'];
        $ruta_imagen = ''; // Inicializar la variable de la imagen
        $fecha_registro = $_POST['fecha_registro'];

        // Verificación del ID del proveedor
        $sql_verificar_proveedor = "SELECT * FROM proveedores WHERE id_proveedor = '$id_proveedor'";
        $result_verificar = $conn->query($sql_verificar_proveedor);

        if ($result_verificar->num_rows == 0) {
            $mensaje = "<div class='mensaje error'>Error: El proveedor seleccionado no existe.</div>";
        } else {
            // Manejar la carga de imagen
            if (isset($_FILES['imagen_producto']) && $_FILES['imagen_producto']['error'] == 0) {
                // Validar el tipo de archivo
                $tipo_archivo = $_FILES['imagen_producto']['type'];
                $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];

                if (in_array($tipo_archivo, $tipos_permitidos)) {
                    // Definir la ruta donde se guardará la imagen
                    $ruta_imagen = 'imagenes/' . basename($_FILES['imagen_producto']['name']);

                    // Mover el archivo subido a la carpeta deseada
                    if (!move_uploaded_file($_FILES['imagen_producto']['tmp_name'], $ruta_imagen)) {
                        $mensaje = "<div class='mensaje error'>Error al mover el archivo subido.</div>";
                    }
                } else {
                    $mensaje = "<div class='mensaje error'>Tipo de archivo no permitido. Solo se permiten imágenes JPEG, PNG y GIF.</div>";
                }
            }

            // Inserción en la base de datos
            $sql_producto = "INSERT INTO productos (nombre_productos, codigo_productos, modelo_productos, marca_productos, descripcion_productos, cantidad_disponible, stock_minimo, precio_productos, precio_dolares, precio_total, imagen_producto, fecha_registro, ID_PROVEEDOR) 
                             VALUES ('$nombre_productos', '$codigo_productos', '$modelo_productos', '$marca_productos', '$descripcion_productos', '$cantidad_disponible', '$stock_minimo', '$precio_productos', '$precio_dolares', '$precio_total', '$ruta_imagen', '$fecha_registro', '$id_proveedor')";

            // Ejecutar la consulta
            if ($conn->query($sql_producto) === TRUE) {
                $mensaje = "<div class='mensaje exito'>Producto agregado con éxito.</div>";
            } else {
                $mensaje = "<div class='mensaje error'>Error al agregar el producto: $conn->error</div>";
            }
        }
    }

    // Mostrar mensaje si existe
    if ($mensaje) {
        echo $mensaje;
    }

    // Registrar proveedor
    if (isset($_POST['registro_proveedor_modal'])) {
        $nombre_proveedor = $_POST['nombre_proveedor'];
        $tipo_documento = $_POST['tipo_documento']; 
        $documento_proveedor = $_POST['documento_proveedor'];
        $telefono_proveedor = $_POST['telefono_proveedor'];
        $direccion_proveedor = $_POST['direccion_proveedor'];

        // Combinar el tipo de documento con el número de cédula/RIF
        $documento_proveedor = $tipo_documento . '-' . $documento_proveedor; // Ejemplo: V-12345678 o J-12345678

        // Asegúrate de que la tabla 'proveedores' tenga una columna para el documento
        $sql_proveedor = "INSERT INTO proveedores (nombre_proveedor, documento_proveedor, telefono_proveedor, direccion_proveedor) VALUES ('$nombre_proveedor', '$documento_proveedor', '$telefono_proveedor', '$direccion_proveedor')";

        if ($conn->query($sql_proveedor) === TRUE) {
            $mensaje = "<div class='mensaje exito'>Proveedor registrado exitosamente.</div>";
            echo "<script>document.getElementById('modalProveedor').style.display='none';</script>";
        } else {
            $mensaje = "<div class='mensaje error'>Error al registrar proveedor: $conn->error</div>";
        }
    }

    // Editar proveedor
    if (isset($_POST['editar_proveedor'])) {
        $id_proveedor = $_POST['id_proveedor_editar'] ?? null; 
        $nombre_proveedor = $_POST['nombre_proveedor_editar'] ?? '';
        $documento_proveedor = $_POST['documento_proveedor_editar'] ?? '';
        $telefono_proveedor = $_POST['telefono_proveedor_editar'] ?? '';
        $direccion_proveedor = $_POST['direccion_proveedor_editar'] ?? '';

        $sql_actualizar = "UPDATE proveedores SET 
            nombre_proveedor='$nombre_proveedor', 
            documento_proveedor='$documento_proveedor', 
            telefono_proveedor='$telefono_proveedor', 
            direccion_proveedor='$direccion_proveedor' 
            WHERE id_proveedor='$id_proveedor'";

        if ($conn->query($sql_actualizar) === TRUE) {
            $mensaje = "<div class='mensaje exito'>Proveedor actualizado con éxito.</div>";
        } else {
            $mensaje = "<div class='mensaje error'>Error al actualizar el proveedor: $conn->error</div>";
        }   
    }

    // Eliminar proveedor
    if (isset($_POST['eliminar_proveedor'])) {
        $id_proveedor = $_POST['id_proveedor_eliminar'];

        // Intentar eliminar el proveedor
        try {
            $sql_eliminar = "DELETE FROM proveedores WHERE id_proveedor='$id_proveedor'";
            if ($conn->query($sql_eliminar) === TRUE) {
                $mensaje = "<div class='mensaje exito'>Proveedor eliminado con éxito.</div>";
            }
        } catch (mysqli_sql_exception $e) {
            if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
                $mensaje = "<div class='mensaje error'>No se puede eliminar el proveedor porque tiene productos asociados. Por favor, elimina los productos primero.</div>";
            } else {
                $mensaje = "<div class='mensaje error'>Error al eliminar el proveedor: ".htmlspecialchars($e->getMessage())."</div>";
            }
        }   
    }

    // Mostrar mensaje si existe
    if ($mensaje) {
        echo $mensaje;
    }
    ?>

    
    <?php
        // Conectar a la base de datos
        $conn = new mysqli("localhost", "root", "", "sistema");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Obtener la tasa de cambio más reciente
        $sql_tasa = "SELECT tasa FROM tasas_dolar ORDER BY fecha DESC LIMIT 1";
        $result_tasa = $conn->query($sql_tasa);
        $tasa_dolar = '0.00'; // Valor por defecto
        if ($result_tasa && $result_tasa->num_rows > 0) {
            $row_tasa = $result_tasa->fetch_assoc();
            $tasa_dolar = htmlspecialchars($row_tasa['tasa']);
        }
    ?>
    <style>
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
        .more-text {
            display: none; /* Oculta el texto adicional por defecto */
        }
        .read-more {
            color: #4B6F8E;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
            display: inline-block;
        }
        .formulario {
            flex: 1; /* Ocupa el espacio restante */
            padding: 20px;
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
    <style>.mensaje {
    padding: 15px;
    margin: 20px 0;
    border-radius: 5px;
    font-size: 16px;
}

.mensaje.exito {
    background-color: #d4edda; /* Color de fondo verde claro */
    color: #155724; /* Color del texto verde oscuro */
    border: 1px solid #c3e6cb; /* Borde verde */
}

.mensaje.error {
    background-color: #f8d7da; /* Color de fondo rojo claro */
    color: #721c24; /* Color del texto rojo oscuro */
    border: 1px solid #f5c6cb; /* Borde rojo */
}</style>
</head>
<body>
    <div id="inventario-form">
        <h2>Registrar Nuevo Producto</h2>
        <div class="container">
            <div class="left">
                <div class="instructions">
                    <h3>Instrucciones para el Registro de un Nuevo Producto</h3>
                    <img src="http://localhost/inventario/Productos\photo_2024-11-24_15-21-59.jpg" alt="Descripción de la imagen" style="width: 150px; height: auto; border-radius: 10px; margin-top: 10px;">
                    <p><b>¡Bienvenido al módulo de registro de productos!</b> A continuación, se detallan los pasos para registrar un nuevo producto en el sistema:</p>
                    <h4>Selección de Proveedor:</h4>
                    <p>En el formulario, podrá seleccionar un proveedor existente.</p>
                    <p>Haga clic en el botón de opciones para acceder a tres acciones adicionales:</p>
                    <ul>
                        <li>Agregar Proveedor</li>
                        <li>Editar Proveedor</li>
                        <li>Eliminar Proveedor</li>
                    </ul>
                    <p>Recuerde que, para editar o eliminar un proveedor, primero debe seleccionarlo.</p>
                    <h4>Información del Producto:</h4>
                    <p>Ingrese el nombre del producto.</p>
                    <p>Proporcione el código del producto.</p>
                    <p>Indique la marca del producto.</p>
                    <p>Especifique la aplicación que tendrá el producto (por ejemplo, uso, categoría, etc.).</p>
                    <p>Agregue una descripción adicional del producto, si es necesario.</p>

                    <h4>Cantidad y Stock Mínimo:</h4>
                    <p>Indique la cantidad disponible de este producto en inventario.</p>
                    <p>Puede establecer un número para el stock mínimo, que activará una alerta cuando la cantidad disponible alcance este límite.</p>

                    <h4>Precio y Cálculos Automáticos:</h4>
                    <p>Al registrar previamente la tasa del dólar.</p>
                    <p>Ingrese el precio en dólares del producto.</p>
                    <p>El sistema calculará automáticamente el IVA (16%) y mostrará:</p>
                    <ul>
                        <li>El precio total en dólares con IVA incluido.</li>
                        <li>El precio en bolívares con IVA, basado en la tasa actual.</li>
                    </ul>
             </div>
            </div>
            <div class="right">
                <form action="" method="POST" enctype="multipart/form-data" onsubmit="return verificarStockMinimo()">
                    <label for="proveedor">Proveedor:</label>
                    <select id="proveedor" name="proveedor_id" required>
                        <option value="">Seleccione un proveedor</option>
                        <?php
                            // Conectar a la base de datos
                            $conn = new mysqli("localhost", "root", "", "sistema");
                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            }
                            $sql = "SELECT id_proveedor, nombre_proveedor, documento_proveedor, telefono_proveedor, direccion_proveedor FROM proveedores";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['id_proveedor']}'>{$row['nombre_proveedor']} - {$row['documento_proveedor']} - {$row['telefono_proveedor']} - {$row['direccion_proveedor']}</option>";
                                }
                            }
                            $conn->close();
                        ?>
                    </select>

                    <button type="button" id="btnOpciones" style="background-color:#4B6F8E; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer;">Opciones</button>
                    <div id="opciones" style="display: none; margin-top: 10px;">
                        <button type="button" id="btnAgregarProveedor" style="background-color: #4B6F8E; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer;">Agregar Proveedor</button>
                        <button type="button" id="btnEditarProveedor" style="background-color: #4B6F8E; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer;">Editar Proveedor</button>
                        <button type="button" id="btnEliminarProveedor" style="background-color: #4B6F8E; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer;">Eliminar Proveedor</button>
                    </div>

                    <script>
                    $(document).ready(function() {
                        $('#proveedor').select2({
                            placeholder: 'Buscar proveedor...',
                            allowClear: true
                        });

                        $('#btnOpciones').on('click', function() {
                            $('#opciones').toggle();
                        });
                    });
                    </script>
   <?php
                date_default_timezone_set("America/Caracas");
                $hora_fecha = date("Y-m-d");
                ?>
                    <!-- Campos del formulario para registrar productos -->
                    <label for="nombre_productos">Nombre:</label>
                    <input type="text" id="nombre_productos" name="nombre_productos" required title="Ingrese el nombre del producto">

                    <label for="codigo_productos">Código:</label>
                    <input type="text" id="codigo_productos" name="codigo_productos" required title="Ingrese el código único del producto">

                    <label for="marca_productos">Marca:</label>
                    <input type="text" id="marca_productos" name="marca_productos" title="Ingrese la marca del producto">

                    <label for="modelo_productos">Aplicación:</label>
                    <input type="text" id="modelo_productos" name="modelo_productos" title="Ingrese la aplicación del producto">

                    <label for="descripcion_productos">Descripción:</label>
                    <input type="text" id="descripcion_productos" name="descripcion_productos" title="Ingrese una breve descripción del producto">

                    <label for="cantidad_disponible">Cantidad Disponible:</label>
                    <input type="number" id="cantidad_disponible" name="cantidad_disponible" required title="Ingrese la cantidad disponible en inventario">

                     <label for="stock_minimo">Stock Mínimo para Alerta:</label>
                     <input type="number" id="stock_minimo" name="stock_minimo" required title="Ingrese el stock mínimo para recibir alertas">

                    <label for="tasa_dolar">Tasa de Dólar (en Bs):</label>
                    <input type="number" id="tasa_dolar" name="tasa_dolar" step="0.01" required readonly value="<?php echo $tasa_dolar; ?>" title="Tasa de cambio actual del dólar en bolívares">

                    <label for="precio_dolares">Precio (en $):</label>
                    <input type="number" id="precio_dolares" name="precio_dolares" step="0.01" required oninput="calcularPrecioConIVA(); calcularPrecioEnBolivares();" title="Ingrese el precio del producto en dólares">

                    <label for="iva">IVA (16%):</label>
                    <input type="text" id="iva" name="iva" readonly title="Este campo muestra el IVA calculado del precio">

                    <label for="precio_total">Precio dolar con IVA:</label>
                    <input type="text" id="precio_total" name="precio_total" readonly title="Este campo muestra el precio total incluyendo IVA">

                    <label for="nuevo_precio_con_aumento"> Precio dolar con Aumento (40%):</label>
                    <input type="text" id="nuevo_precio_con_aumento" name="nuevo_precio_con_aumento" 
                    readonly title="Este campo muestra el nuevo precio con un aumento del 40%">

                    <label for="precio_productos">Precio en Bolivares con IVA (16%) y porcentaje (40%):</label>
                    <input type="number" id="precio_productos" name="precio_productos" step="0.01" readonly title="Este campo muestra el precio en bolívares con IVA incluido">
                   
                    <label for="fecha_registro">Fecha:</label>
                    <input type="date" id="fecha_registro" name="fecha_registro" value="<?php echo $hora_fecha; ?>" readonly>

                    <label for="imagen_producto">Imagen del Producto:</label>
                     <input type="file" id="imagen_producto" name="imagen_producto" accept="image/*" onchange="previewImage(event)">
                   <img id="imagenPreview" src="#" alt="Previsualización de la imagen" style="display:none;">

                    <input type="submit" name="registro_producto" value="Agregar Producto">
                </form>
            </div>
        </div>
    </div>
  
    <script>
function verificarStockMinimo() {
    const cantidadDisponible = parseInt(document.getElementById('cantidad_disponible').value);
    const stockMinimo = parseInt(document.getElementById('stock_minimo').value);

    if (cantidadDisponible <= stockMinimo) {
        alert('¡Alerta! La cantidad disponible ha alcanzado el stock mínimo.');
    }

    return true; // Permitir que el formulario se envíe
}
</script>
            <script>
               function calcularPrecioConIVA() {
    const precioBaseDolares = parseFloat(document.getElementById('precio_dolares').value);
    const tasaIVA = 16; // Tasa de IVA en porcentaje
    const aumentoAdicional = 40; // Aumento adicional del 40%

    if (!isNaN(precioBaseDolares) && precioBaseDolares >= 0) {
        const decimalIVA = tasaIVA / 100;
        const montoIVA = precioBaseDolares * decimalIVA;
        const precioTotalConIVA = precioBaseDolares + montoIVA;

        // Calcular el nuevo precio con aumento adicional
        const decimalAumento = aumentoAdicional / 100;
        const nuevoPrecioConAumento = precioTotalConIVA + (precioTotalConIVA * decimalAumento);

        document.getElementById('iva').value = montoIVA.toFixed(2);
        document.getElementById('precio_total').value = precioTotalConIVA.toFixed(2);
        document.getElementById('nuevo_precio_con_aumento').value = nuevoPrecioConAumento.toFixed(2); // Nuevo campo para mostrar el precio con aumento
    }
}

function calcularPrecioEnBolivares() {
    const precioBaseDolares = parseFloat(document.getElementById('precio_dolares').value);
    const tasaDolar = parseFloat(document.getElementById('tasa_dolar').value);

    if (!isNaN(precioBaseDolares) && !isNaN(tasaDolar) && tasaDolar > 0) {
        // Cálculo del IVA
        const iva = precioBaseDolares * 0.16; // 16%
        const precioTotalConIVA = precioBaseDolares + iva;

        // Cálculo del aumento adicional
        const aumento = precioTotalConIVA * 0.40; // 40%
        const nuevoPrecioTotal = precioTotalConIVA + aumento;

        // Conversión a bolívares
        const precioEnBolivares = nuevoPrecioTotal * tasaDolar;

        // Actualizar el campo correspondiente
        document.getElementById('precio_productos').value = precioEnBolivares.toFixed(2);
    } else {
        document.getElementById('precio_productos').value = '';
    }
}


            </script>
<script>
function previewImage(event) {
                const image = document.getElementById('imagenPreview');
                image.src = URL.createObjectURL(event.target.files[0]);
                image.style.display = 'block'; // Mostrar la imagen
            }
            </script>

            <style>
            #imagenPreview {
                max-width: 200px; /* Limitar el ancho de la imagen */
                max-height: 200px; /* Limitar la altura de la imagen */
                height: auto; /* Mantener la relación de aspecto */
                margin-top: 10px; /* Margen superior para separación */
                display: none; /* Inicialmente oculto */
            }
            </style>
            <script>
        function filterProviders() {
            const input = document.getElementById('search_proveedor');
            const filter = input.value.toLowerCase();
            const select = document.getElementById('proveedor');
            const options = select.options;

            for (let i = 1; i < options.length; i++) { // Comenzar desde 1 para omitir la opción predeterminada
                const txtValue = options[i].text.toLowerCase();
                options[i].style.display = txtValue.includes(filter) ? "" : "none"; // Mostrar u ocultar opción
            }
        }
    </script>
           <!-- Modal para agregar proveedor -->
<div id="modalProveedor" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal('modalProveedor')">&times;</span>
        <h3>Agregar Proveedor</h3>
        <form action="" method="POST">
            <input type="hidden" id="id_proveedor" name="id_proveedor">
            <label for="nombre_proveedor">Nombre del Proveedor:</label>
            <input type="text" id="nombre_proveedor" name="nombre_proveedor" required>

            <label for="tipo_documento">Tipo de Documento:</label>
            <select id="tipo_documento" name="tipo_documento" required>
                <option value="" disabled>Seleccione...</option>
                <option value="V">Cédula (V)</option>
                <option value="J">RIF (J)</option>
            </select>

            <label for="documento_proveedor">Cédula/RIF:</label>
            <input type="text" id="documento_proveedor" name="documento_proveedor" required>

            <label for="telefono_proveedor">Teléfono:</label>
            <input type="text" id="telefono_proveedor" name="telefono_proveedor" required>

            <label for="direccion_proveedor">Dirección:</label>
            <input type="text" id="direccion_proveedor" name="direccion_proveedor" required>

            <input type="submit" name="registro_proveedor_modal" value="Agregar Proveedor">
        </form>
    </div>
</div>
<script>
function validarFormulario() {
    const cedulaInput = document.getElementById('documento_proveedor').value.trim();
    
    // Expresión regular para validar cédula (Ej: 12345678)
    const cedulaRegex = /^\d{7,8}$/;
    // Expresión regular para validar RIF (Ej: J-12345678-9)
    const rifRegex = /^[JVG]-\d{8}-\d$/;

    if (cedulaRegex.test(cedulaInput)) {
        alert("Número de Cédula válido.");
        return true; // Formulario válido
    } else if (rifRegex.test(cedulaInput)) {
        alert("Número de RIF válido.");
        return true; // Formulario válido
    } else {
        alert("Por favor, ingrese un número de Cédula o RIF válido.");
        return false; // Evitar el envío del formulario
    }
}
</script>


        <!-- Modal para editar proveedor -->
        <div id="modalEditarProveedor" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="document.getElementById('modalEditarProveedor').style.display='none';">&times;</span>
                    <h3>Editar Proveedor</h3>
                    <form id="formEditarProveedor" action="" method="POST">
                        <input type="hidden" id="id_proveedor_editar" name="id_proveedor_editar">
                        <label for="nombre_proveedor_editar">Nombre del Proveedor:</label>
                        <input type="text" id="nombre_proveedor_editar" name="nombre_proveedor_editar" required>

                        <label for="documento_proveedor_editar">Cédula del Proveedor:</label>
                        <input type="text" id="documento_proveedor_editar" name="documento_proveedor_editar" required>

                        <label for="telefono_proveedor_editar">Teléfono del Proveedor:</label>
                        <input type="text" id="telefono_proveedor_editar" name="telefono_proveedor_editar" required>

                        <label for="direccion_proveedor">Dirección:</label>
                        <input type="text" id="direccion_proveedor" name="direccion_proveedor" required>
                        
                        <input type="submit" name="editar_proveedor" value="Guardar Cambios">
                    </form>
                </div>
            </div>

  <!-- Modal para eliminar proveedor -->
  <div id="modalEliminarProveedor" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="document.getElementById('modalEliminarProveedor').style.display='none';">&times;</span>
                    <h3>Eliminar Proveedor</h3>
                    <p>¿Estás seguro de que deseas eliminar a <strong id="nombre_proveedor_eliminar"></strong>?</p>
                    <form id="formEliminarProveedor" action="" method="POST">
                        <input type="hidden" id="id_proveedor_eliminar" name="id_proveedor_eliminar">
                        <input type="submit" name="eliminar_proveedor" value="Eliminar Proveedor">
                    </form>
                </div>
            </div>

            <script>
                // Obtener el modal
                var modalProveedor = document.getElementById("modalProveedor");
                var modalEditarProveedor = document.getElementById("modalEditarProveedor");
                var modalEliminarProveedor = document.getElementById("modalEliminarProveedor");

                // Obtener el botón que abre el modal para agregar proveedor
                var btnAgregar = document.getElementById("btnAgregarProveedor");

                // Cuando el usuario hace clic en el botón, abre el modal
                btnAgregar.onclick = function() {
                    modalProveedor.style.display = "block";
                }

                // Obtener el botón que abre el modal para editar proveedor
                $('#btnEditarProveedor').on('click', function() {
                    var selectedOption = $('#proveedor option:selected');
                    if (selectedOption.val() !== "") {
                        var idProveedor = selectedOption.val();
                        var nombreProveedor = selectedOption.text().split(' - ')[0];
                        var documento_proveedor = selectedOption.text().split(' - ')[1];
                        var telefonoProveedor = selectedOption.text().split(' - ')[2];

                        $('#id_proveedor_editar').val(idProveedor);
                        $('#nombre_proveedor_editar').val(nombreProveedor);
                        $('#documento_proveedor_editar').val(documento_proveedor);
                        $('#telefono_proveedor_editar').val(telefonoProveedor);

                        modalEditarProveedor.style.display = "block";
                    } else {
                        alert("Por favor, selecciona un proveedor para editar.");
                    }
                });

                // Obtener el botón que abre el modal para eliminar proveedor
                $('#btnEliminarProveedor').on('click', function() {
                    var selectedOption = $('#proveedor option:selected');
                    if (selectedOption.val() !== "") {
                        var idProveedor = selectedOption.val();
                        var nombreProveedor = selectedOption.text().split(' - ')[0];

                        $('#id_proveedor_eliminar').val(idProveedor);
                        $('#nombre_proveedor_eliminar').text(nombreProveedor);

                        modalEliminarProveedor.style.display = "block";
                    } else {
                        alert("Por favor, selecciona un proveedor para eliminar.");
                    }
                });

                // Cerrar modales al hacer clic en la "x"
                var spans = document.getElementsByClassName("close");
                for (var i = 0; i < spans.length; i++) {
                    spans[i].onclick = function() {
                        modalProveedor.style.display = "none";
                        modalEditarProveedor.style.display = "none";
                        modalEliminarProveedor.style.display = "none";
                    }
                }

                // Cerrar modales al hacer clic fuera de ellos
                window.onclick = function(event) {
                    if (event.target == modalProveedor || event.target == modalEditarProveedor || event.target == modalEliminarProveedor) {
                        modalProveedor.style.display = "none";
                        modalEditarProveedor.style.display = "none";
                        modalEliminarProveedor.style.display = "none";
                    }
                }
            </script>
    <?php
    $conn = new mysqli("localhost", "root", "", "sistema");

    // Verificar conexión
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }?>
            <script>
                // Previsualizar imagen
                function previewImage(event) {
                    const imagePreview = document.getElementById('imagenPreview');
                    imagePreview.src = URL.createObjectURL(event.target.files[0]);
                    imagePreview.style.display = 'block';
                }
                // Obtener el modal
                var modal = document.getElementById("modalProveedor");
                var btn = document.getElementById("btnAgregarProveedor");
                var span = document.getElementsByClassName("close")[0];

                // Cuando el usuario hace clic en el botón, abre el modal
                btn.onclick = function() {
                    modal.style.display = "block";
                }
                // Cuando el usuario hace clic en <span> (x), cierra el modal
                span.onclick = function() {
                    modal.style.display = "none";
                }
                // Cuando el usuario hace clic en cualquier parte fuera del modal, cierra el modal
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                }
                function validarFormulario() {
                    var documento_proveedor = document.getElementById("documento_proveedor").value;
                    var telefono = document.getElementById("telefono_proveedor").value;

                    // Validar cédula
                    if (!validarNumeros(documento_proveedor)) {
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
        </div>
    </div>

</form>
</body>
</html>