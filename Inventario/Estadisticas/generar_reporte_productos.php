<?php
require 'dompdf/autoload.inc.php'; // Asegúrate de que esta ruta sea correcta

use Dompdf\Dompdf;
use Dompdf\Options;

// Configura Dompdf
$options = new Options();
$options->set('defaultFont', 'Courier');
$dompdf = new Dompdf($options);

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

// Consulta SQL para obtener datos de productos
$sql = "SELECT 
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

$result = $conn->query($sql);

// Crear contenido HTML para el PDF
$currentDate = date('Y-m-d'); // Fecha actual
$html = '
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            text-align: center;
            margin-bottom: 20px;
        }
        header img {
            width: 100px; /* Ajusta el tamaño de la imagen */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        h1 {
            margin-bottom: 0;
        }
        .footer {
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1>El Comercial Alexmil</h1>
        <p>Ubicado en la avenida Bertorelli Cisnero, local 31-A Sector Santa Eulalia,<br> Los Teques, Edo Miranda</p>
    </header>
    <main>
        <h2>Reporte de Productos</h2>
        <table border="1" cellpadding="5">
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
                    <th>Fecha Registro</th>
                </tr>
            </thead>
            <tbody>';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
                    <td>' . $row['nombre_productos'] . '</td>
                    <td>' . $row['codigo_productos'] . '</td>
                    <td>' . $row['modelo_productos'] . '</td>
                    <td>' . $row['marca_productos'] . '</td>
                    <td>' . $row['descripcion_productos'] . '</td>
                    <td>' . $row['cantidad_disponible'] . '</td>
                    <td>' . $row['precio_productos'] . '</td>
                    <td>' . $row['precio_dolares'] . '</td>
                    <td>' . $row['precio_total'] . '</td>
                    <td>' . $row['fecha_registro'] . '</td>
                  </tr>';
    }
} else {
    $html .= '<tr><td colspan="10" class="text-center">No hay resultados</td></tr>';
}

$html .= '          </tbody></table>';
$html .= '<div class="footer">Fecha del Informe: ' . $currentDate . '</div>'; // Fecha actual en el pie del PDF
$html .= '</main></body></html>';

// Cerrar conexión
$conn->close();

// Cargar contenido HTML en Dompdf
$dompdf->loadHtml($html);

// (Opcional) Configurar tamaño y orientación del papel
$dompdf->setPaper('A4', 'landscape');

// Renderizar el HTML como PDF
$dompdf->render();

// Enviar el PDF al navegador para descarga
$dompdf->stream('reporte_productos.pdf', ['Attachment' => true]);
?>