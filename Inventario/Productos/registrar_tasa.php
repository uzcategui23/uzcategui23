<!DOCTYPE html>
<html lang="es">
<head>
    <?php include './Commons/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Tasa de Dólar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        main {
            height: 100vh; /* Altura completa de la ventana */
            display: flex; /* Usar flexbox para centrar */
            justify-content: center; /* Centrar horizontalmente */
            align-items: center; /* Centrar verticalmente */
        }

        .container {
            max-width: 600px;
            width: 100%; /* Asegura que el contenedor no exceda el ancho de la pantalla */
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="number"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        p {
            text-align: center;
            color: #d9534f; /* Color rojo para errores */
        }
    </style>
</head>
<body>
    <?php include './Commons/nav.php'; ?>
    <main>
        <div class="container">
            <h2>Registrar Tasa de Dólar</h2>
            <form action="" method="POST">
                <label for="tasa_dolar">Tasa de Dólar (en Bs):</label>
                <input type="number" id="tasa_dolar" name="tasa_dolar" step="0.01" required>

                <input type="submit" name="registrar_tasa" value="Registrar Tasa">
            </form>

            <?php
            // Conectar a la base de datos
            $conn = new mysqli("localhost", "root", "", "sistema");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Registrar tasa
            if (isset($_POST['registrar_tasa'])) {
                $tasa_dolar = $_POST['tasa_dolar'];
                $fecha = date('Y-m-d'); // Fecha actual

                // Verificar si ya existe una tasa para la fecha actual
                $sql_verificar = "SELECT * FROM tasas_dolar WHERE fecha = '$fecha'";
                $result = $conn->query($sql_verificar);

                if ($result->num_rows > 0) {
                    echo "<p>Error: Ya existe un registro para la fecha $fecha.</p>";
                } else {
                    // Insertar nueva tasa
                    $sql_insertar = "INSERT INTO tasas_dolar (tasa, fecha) VALUES ('$tasa_dolar', '$fecha')";
                    if ($conn->query($sql_insertar) === TRUE) {
                        echo "<p>Tasa registrada con éxito.</p>";
                    } else {
                        echo "<p>Error al registrar la tasa: " . $conn->error . "</p>";
                    }
                }
            }

            $conn->close();
            ?>
        </div>
    </main>
</body>
</html>
