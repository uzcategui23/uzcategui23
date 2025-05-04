<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root"; // Cambia esto
$password = ""; // Cambia esto
$dbname = "sistema";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include './Commons/head.php'; ?>
    <title>Registrar Usuario</title>
</head>
<body>
    <?php include './Commons/nav.php'; ?>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1, h2 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Registros de Actividad de Usuarios</h1>

    <table id="activity-logs">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Actividad</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM user_activity_logs";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['user_id']}</td>
                            <td>{$row['activity']}</td>
                            <td>{$row['timestamp']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No hay registros</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <h2>Usuarios</h2>
    <table id="usuarios">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre de Usuario</th>
                <th>Correo Electrónico</th>
                <th>Nombre Empleado</th>
                <th>Cédula</th>
                <th>Teléfono</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM usuarios";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['username']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['nombre_empleado']}</td>
                            <td>{$row['cedula']}</td>
                            <td>{$row['telefono']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No hay registros</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <script>
        // Puedes agregar funciones JavaScript aquí si es necesario
        console.log("JavaScript cargado correctamente.");
    </script>
</body>
</html>

<?php
$conn->close();
?>
