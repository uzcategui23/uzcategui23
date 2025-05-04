
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

// Obtener las fechas desde la URL
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : 'No especificada';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : 'No especificada';

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
        <h2>Reporte de Ventas</h2>
        <p>Desde: ' . $startDate . ' Hasta: ' . $endDate . '</p>
        <table border="1" cellpadding="5">
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
            <tbody>';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
                    <td>' . $row['nombre_cliente'] . '</td>
                    <td>' . $row['cedula_cliente'] . '</td>
                    <td>' . $row['telefono_cliente'] . '</td>
                    <td>' . $row['total_vendido'] . '</td>
                    <td>' . $row['total_precio'] . '</td>
                    <td>' . $row['fecha_venta'] . '</td>
                    <td>' . $row['nombre_productos'] . '</td>
                  </tr>';
    }
} else {
    $html .= '<tr><td colspan="7" class="text-center">No hay resultados</td></tr>';
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
$dompdf->stream('reporte_ventas.pdf', ['Attachment' => true]);
?>