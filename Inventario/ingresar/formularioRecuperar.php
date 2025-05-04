<?php
// Conexión a la base de datos
$host = 'localhost';
$db = 'sistema';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Variables para manejar el estado
$usuario_valido = false;
$exito = false;
$username = '';
$mensaje_error = '';

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar que todos los campos estén presentes
    if (isset($_POST['username'], $_POST['email'], $_POST['cedula'], $_POST['telefono'], $_POST['nueva_contraseña'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $cedula = trim($_POST['cedula']);
        $telefono = trim($_POST['telefono']);
        $nueva_contraseña = trim($_POST['nueva_contraseña']);

        // Validar que la nueva contraseña cumpla con los requisitos mínimos
        if (strlen($nueva_contraseña) < 8) {
            $mensaje_error = "La contraseña debe tener al menos 8 caracteres.";
        } else {
            // Verificar si los datos son válidos
            $stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = ? AND email = ? AND cedula = ? AND telefono = ?");
            $stmt->bind_param("ssss", $username, $email, $cedula, $telefono);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows === 1) {
                // Usuario válido, proceder a actualizar la contraseña
                $nueva_contraseña_hash = password_hash($nueva_contraseña, PASSWORD_DEFAULT);
                $stmt_actualizar = $conn->prepare("UPDATE usuarios SET password = ? WHERE username = ?");
                $stmt_actualizar->bind_param("ss", $nueva_contraseña_hash, $username);

                if ($stmt_actualizar->execute()) {
                    $exito = true;
                } else {
                    $mensaje_error = "Error al actualizar la contraseña. Inténtalo de nuevo.";
                }
                $stmt_actualizar->close();
            } else {
                $mensaje_error = "Los datos proporcionados no son válidos.";
            }
            $stmt->close();
        }
    } else {
        $mensaje_error = "Por favor completa todos los campos.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #4CAF50;
        }

        .container {
            max-width: 400px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .error-message {
            color: red;
            text-align: center;
        }

        .success-message {
            color: green;
            text-align: center;
        }

        .login-button {
            margin-top: 15px; /* Espacio entre el mensaje y el botón */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Actualizar Contraseña</h1>

        <?php if ($exito): ?>
            <p class="success-message">La contraseña se ha actualizado correctamente.</p>
            
            <!-- Botón para ir al login -->
            <div class="login-button">
                <a href="http://localhost/inventario/ingresar/index.php">
                    <button>Ir al Login</button>
                </a>
            </div>
            
        <?php elseif ($mensaje_error): ?>
            <p class="error-message"><?php echo htmlspecialchars($mensaje_error); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="username">Nombre de usuario:</label>
            <input type="text" name="username" id="username" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="cedula">Cédula:</label>
            <input type="text" name="cedula" id="cedula" required>

            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" id="telefono" required>

            <label for="nueva_contraseña">Nueva Contraseña:</label>
            <input type="password" name="nueva_contraseña" id="nueva_contraseña" required>

            <button type="submit">Actualizar Contraseña</button>
        </form>
    </div>
</body>
</html>
