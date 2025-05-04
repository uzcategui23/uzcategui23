<!DOCTYPE html>
<html lang="es">
<head>
    <?php include './Commons/head.php'; ?>
    <title>Devoluciones</title>
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
        button {
            background-color: #4B6F8E;
            color: white;
            cursor: pointer;
            border: none;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            width: 100%;
        }
        button:hover {
            background-color: #3A5B73;
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
    </style>
</head>
<body>
    <?php include './Commons/nav.php'; ?>
    <div class="container">
        <!-- Sección de texto e imagen a la izquierda -->
        <div class="left">
            <div class="instructions">
                <h3>Módulo de Ventas: Proceso de Devoluciones</h3>
                <img src="http://localhost/inventario/Ventas/photo_2024-11-24_15-21-59 (2).jpg" alt="Descripción de la imagen" style="width: 150px; height: auto; border-radius: 10px; margin-top: 10px;">
                <p>En el módulo de ventas, encontrarás un botón dedicado a las devoluciones. Al hacer clic en este botón, podrás seleccionar la venta que deseas devolver. Los detalles de la venta se cargarán automáticamente en el formulario correspondiente.</p>
                <h4>Pasos para Procesar una Devolución</h4>
                <p><strong>Descripción de la Devolución:</strong> En el campo de descripción, podrás modificar o añadir información sobre la devolución.</p>
                <p><strong>Confirmar Devolución:</strong> Una vez que hayas completado los datos necesarios, haz clic en el botón "Confirmar Devolución".</p>
                <p><strong>Revisión de Detalles:</strong> Aparecerá un formulario debajo para que revises todos los detalles de la devolución antes de proceder.</p>
                <p><strong>Enviar Devolución:</strong> Al finalizar la revisión, podrás confirmar y enviar la devolución.</p>
                <h4>Visualización de Devoluciones</h4>
                <p>Además, encontrarás un botón que te permitirá visualizar todas las devoluciones en una tabla, facilitando así el seguimiento y gestión de las mismas.</p>
            </div>
        </div>


        <!-- Formulario a la derecha -->
        <div class="right">
            <form action="http://localhost/inventario/Ventas/procesar_devolucion.php" method="POST" id="ventas-form">
                <label for="venta">Seleccione una venta:</label>
                <select id="venta" name="venta_id" required>
                    <option value="">Seleccione una venta</option>
                    <?php
                    // Conectar a la base de datos
                    $conn = new mysqli("localhost", "root", "", "sistema");

                    // Verificar conexión
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Obtener todas las ventas con información del cliente y producto
                    $sql = "
                        SELECT 
                            v.id_venta,
                            c.nombre_cliente,
                            c.cedula_cliente,
                            c.telefono_cliente,
                            v.cantidad_venta,
                            v.precio_venta AS precio_bs,
                            v.precio_dolar AS precio_usd,
                            v.total_venta,
                            v.fecha_venta,
                            p.id_productos,
                            p.nombre_productos,
                            p.codigo_productos,
                            p.modelo_productos,
                            p.marca_productos,
                            p.descripcion_productos
                        FROM 
                            ventas v
                        JOIN 
                            clientes c ON v.id_cliente = c.id_cliente
                        JOIN 
                            productos p ON v.producto_venta = p.id_productos;
                    ";

                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {         
                            echo "<option value='{$row['id_venta']}' 
                                        data-nombre='{$row['nombre_cliente']}' 
                                        data-cedula='{$row['cedula_cliente']}' 
                                        data-telefono='{$row['telefono_cliente']}' 
                                        data-producto='{$row['nombre_productos']}' 
                                        data-codigo='{$row['codigo_productos']}'
                                        data-modelo='{$row['modelo_productos']}'
                                        data-marca='{$row['marca_productos']}'
                                        data-descripcion='{$row['descripcion_productos']}'
                                        data-cantidad='{$row['cantidad_venta']}' 
                                        data-precio-bs='{$row['precio_bs']}' 
                                        data-precio-usd='{$row['precio_usd']}' 
                                        data-total='{$row['total_venta']}' 
                                        data-fecha='{$row['fecha_venta']}'
                                        data-id-producto='{$row['id_productos']}'>
                                    {$row['nombre_cliente']} - Cédula: {$row['cedula_cliente']} - Teléfono: {$row['telefono_cliente']} - Producto: {$row['nombre_productos']}
                                  </option>";
                        }
                    } else {
                        echo "<option value=''>No hay ventas disponibles</option>";
                    }

                    // Cerrar la conexión
                    $conn->close();
                    ?>
                </select>

                <h3>Detalles de la Venta</h3>
                <div id="detalles-venta">
                    <label for="nombre-cliente">Nombre del Cliente:</label>
                    <input type="text" id="nombre-cliente" name="nombre_cliente" readonly>
                    
                    <label for="cedula-cliente">Cédula:</label>
                    <input type="text" id="cedula-cliente" name="cedula_cliente" readonly>
                    
                    <label for="telefono-cliente">Teléfono:</label>
                    <input type="text" id="telefono-cliente" name="telefono_cliente" readonly>
                    
                    <label for="nombre-producto">Producto:</label>
                    <input type="text" id="nombre-producto" name="nombre_producto" readonly>
                    
                    <label for="codigo-producto">Código:</label>
                    <input type="text" id="codigo-producto" name="codigo_producto" readonly>
                    
                    <label for="modelo-producto">Modelo:</label>
                    <input type="text" id="modelo-producto" name="modelo_producto" readonly>
                    
                    <label for="marca-producto">Marca:</label>
                    <input type="text" id="marca-producto" name="marca_producto" readonly>
                    
                    <label for="descripcion-producto">Descripción:</label>
                    <textarea id="descripcion-producto" name="descripcion_producto" readonly></textarea>
                    
                    <label for="cantidad-venta">Cantidad:</label>
                    <input type="number" id="cantidad-venta" name="cantidad_venta" readonly>
                    
                    <label for="precio-bs">Precio Bs:</label>
                    <input type="text" id="precio-bs" name="precio_bs" readonly>
                    
                    <label for="precio-usd">Precio USD:</label>
                    <input type="text" id="precio-usd" name="precio_usd" readonly>
                    
                    <label for="total-venta">Total:</label>
                    <input type="text" id="total-venta" name="total_venta" readonly>
                    
                    <label for="fecha-venta">Fecha:</label>
                    <input type="text" id="fecha-venta" name="fecha_venta" readonly>
                    
                    <label for="id-producto">Identificador del Producto:</label>
                    <input type="text" id="id-producto" name="id_producto" readonly>
                    
                    <label for="fecha-actual">Fecha Actual:</label>
                    <input type="text" id="fecha-actual" name="fecha_actual" readonly>
                </div>

                <button type="button" id="btn-confirmar">Confirmar Devolución</button>
                <button type="button" id="btn-mostrar-devoluciones">Mostrar Devoluciones</button>
            </form>

            <!-- Formulario de Confirmación -->
            <div id="formulario-confirmacion" style="display:none;">
                <h3>Confirmación de Devolución</h3>
                <p>Por favor, revise los detalles de la devolución:</p>
                <div id="detalles-confirmacion"></div>
                <button id="btn-enviar">Enviar Devolución</button>
                <button id="btn-cancelar">Cancelar</button>
            </div>

            <!-- Tabla para Mostrar Devoluciones -->
            <div id="tabla-devoluciones" style="display:none;">
                <h3>Devoluciones Registradas</h3>
                <table border="1">
                    <thead>
                        <tr>
                            <th>Nombre Cliente</th>
                            <th>Cédula</th>
                            <th>Teléfono</th>
                            <th>Descripcion Producto</th>
                            <th>Cantidad</th>
                            <th>Precio bs</th>
                            <th>Precio usd</th>
                            <th>Total</th>
                            <th>Fecha venta</th>
                            <th>Fecha Devolución</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpo-devoluciones">
                        <!-- Aquí se llenarán las devoluciones desde la base de datos -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#venta').change(function() {
            var selectedOption = $(this).find('option:selected');
            
            // Obtener datos del option seleccionado
            $('#nombre-cliente').val(selectedOption.data('nombre'));
            $('#cedula-cliente').val(selectedOption.data('cedula'));
            $('#telefono-cliente').val(selectedOption.data('telefono'));
            $('#nombre-producto').val(selectedOption.data('producto'));
            $('#codigo-producto').val(selectedOption.data('codigo'));
            $('#modelo-producto').val(selectedOption.data('modelo'));
            $('#marca-producto').val(selectedOption.data('marca'));
            $('#descripcion-producto').val(selectedOption.data('descripcion'));
            $('#cantidad-venta').val(selectedOption.data('cantidad'));
            $('#precio-bs').val(selectedOption.data('precio-bs'));
            $('#precio-usd').val(selectedOption.data('precio-usd'));
            $('#total-venta').val(selectedOption.data('total'));
            $('#fecha-venta').val(selectedOption.data('fecha'));
            $('#id-producto').val(selectedOption.data('id-producto'));
            
            // Establecer la fecha actual
            var today = new Date().toISOString().split('T')[0]; // Formato YYYY-MM-DD
            $('#fecha-actual').val(today);
        });

        $('#btn-confirmar').click(function() {
            var detalles = `
                <strong>Nombre del Cliente:</strong> ${$('#nombre-cliente').val()}<br>
                <strong>Cédula:</strong> ${$('#cedula-cliente').val()}<br>
                <strong>Teléfono:</strong> ${$('#telefono-cliente').val()}<br>
                <strong>Producto:</strong> ${$('#nombre-producto').val()}<br>
                <strong>Código:</strong> ${$('#codigo-producto').val()}<br>
                <strong>Modelo:</strong> ${$('#modelo-producto').val()}<br>
                <strong>Marca:</strong> ${$('#marca-producto').val()}<br>
                <strong>Descripción:</strong> ${$('#descripcion-producto').val()}<br>
                <strong>Cantidad:</strong> ${$('#cantidad-venta').val()}<br>
                <strong>Precio Bs:</strong> ${$('#precio-bs').val()}<br>
                <strong>Precio USD:</strong> ${$('#precio-usd').val()}<br>
                <strong>Total:</strong> ${$('#total-venta').val()}<br>
                <strong>Fecha de Venta:</strong> ${$('#fecha-venta').val()}<br>
                <strong>Identificador del Producto:</strong> ${$('#id-producto').val()}<br>
                <strong>Fecha Actual:</strong> ${$('#fecha-actual').val()}<br>
            `;
            $('#detalles-confirmacion').html(detalles);
            $('#formulario-confirmacion').show();
        });

        $('#btn-cancelar').click(function() {
            $('#formulario-confirmacion').hide();
        });

        $('#btn-enviar').click(function() {
            $('#ventas-form').submit();
        });

        $('#btn-mostrar-devoluciones').click(function() {
            $('#tabla-devoluciones').toggle(); // Mostrar/ocultar tabla de devoluciones
            if ($('#tabla-devoluciones').is(':visible')) {
                mostrarDevoluciones();
            }
        });

        function mostrarDevoluciones() {
            $.ajax({
                url: 'http://localhost/inventario/Ventas/obtener_devoluciones.php', // Archivo PHP que obtendrá las devoluciones
                method: 'GET',
                success: function(data) {
                    $('#cuerpo-devoluciones').html(data);
                },
                error: function() {
                    alert('Error al obtener las devoluciones.');
                }
            });
        }
    });
    </script>
</body>
</html>