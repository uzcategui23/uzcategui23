<?php
session_start(); // Inicia la sesión

function logUserActivity($userId, $activity) {
    // Conectar a la base de datos
    $conn = new mysqli("localhost", "root", "", "sistema");

    // Verificar conexión
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Preparar y ejecutar la consulta SQL
    $stmt = $conn->prepare("INSERT INTO user_activity_logs (user_id, activity) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $activity);
    
    if ($stmt->execute()) {
        // Manejo exitoso opcional
    } else {
        echo "Error al registrar actividad: " . $stmt->error;
    }

    // Cerrar la conexión
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    // Conectar a la base de datos
    $conn = new mysqli("localhost", "root", "", "sistema");

    // Verificar conexión
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Obtener datos del formulario
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Usar una consulta preparada para buscar el usuario por nombre
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verificar la contraseña
        if (password_verify($password, $row['password'])) {
            // Obtener el rol del usuario
            $usuario_id = $row['id'];
            $sql_rol = "SELECT r.nombre_rol FROM usuario_roles ur JOIN rol r ON ur.id_rol = r.id WHERE ur.id_usuario=?";
            $stmt_rol = $conn->prepare($sql_rol);
            $stmt_rol->bind_param("i", $usuario_id);
            $stmt_rol->execute();
            $resultado_rol = $stmt_rol->get_result();

            if ($resultado_rol->num_rows > 0) {
                $rol = $resultado_rol->fetch_assoc();
                $_SESSION['username'] = htmlspecialchars($row['username']);
                $_SESSION['user_role'] = htmlspecialchars($rol['nombre_rol']); // Almacena el rol en la sesión

                // Registro de actividad del usuario (opcional)
                logUserActivity($usuario_id, 'Inicio de sesión exitoso');

                // Redirigir según el rol del usuario
                if ($rol['nombre_rol'] == 'Administrador') {
                    header("Location: /inventario/index.php/ingresar/admin"); // Página para administradores
                } else {
                    header("Location: /inventario/index.php/ingresar/usuario"); // Página para usuarios regulares
                }
                exit();
            }
        } else {
            echo "<p>Contraseña incorrecta</p>";
        }
    } else {
        echo "<p>Usuario no encontrado</p>";
    }

    // Cerrar conexiones
    $stmt->close();
    if (isset($stmt_rol)) {
        $stmt_rol->close();
    }
    
    // Cerrar conexión a la base de datos
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="http://localhost/inventario/ingresar/index.css">
   <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px; /* Ancho del contenedor */
            margin-left: 50px; /* Aumentar el margen izquierdo */
        }
        h2 { text-align: center; color: #333333; }
        label { margin-top: 10px; display: block; color: #555555; }
        input[type="text"], input[type="password"], .btn {
            width: calc(100% - 20px); /* Ajustar para el padding */
            padding: 10px;
            margin-top: 5px;
            border-radius: 4px;
            border: 1px solid #cccccc;
            box-sizing: border-box; /* Incluir padding y borde en el ancho total */
        }
        .btn {
            display: inline-block; /* Permitir el uso de márgenes automáticos */
            width: calc(100% - 20px); /* Botones ocupan casi todo el ancho */
            padding: 8px;
            margin-top: 7px; /* Espacio entre botones */
            margin-bottom: 10px; /* Espacio inferior para el último botón */
            background-color: #143150; /* Color de fondo */
            color: white; /* Color del texto */
            text-align: center; /* Centrar texto */
            border-radius: 4px; /* Bordes redondeados */
            text-decoration: none; /* Sin subrayado */
        }
        .btn:hover { background-color: #0056b3; /* Color al pasar el mouse */ }
        p { text-align: center; color: red; /* Color del mensaje de error */ }
     </style>
   <title>Login</title>
   <script>
    // Evitar que el usuario vuelva a la página anterior
    window.history.pushState(null, document.title, window.location.href);
    window.addEventListener('popstate', function(event) {
        window.history.pushState(null, document.title, window.location.href);
        // Opcionalmente puedes redirigir aquí si lo prefieres
        window.location.href = 'http://localhost/inventario/ingresar/index.php';
    });
</script>
</head>
<body>

<div class="container">
   <h2>Iniciar Sesión</h2>
   <form action="" method="POST">
       <label for="username">Nombre de Usuario:</label>
       <input type="text" id="username" name="username" required>

       <label for="password">Contraseña:</label>
       <input type="password" id="password" name="password" required>

       <!-- Botón para iniciar sesión -->
       <button type="submit" name="login" class="btn btn-primary">Iniciar Sesión</button>
   </form>

   <!-- Botón para recuperar contraseña -->
   <div style="margin-top: 10px;">
       <a href="http://localhost/inventario/index.php/ingresar/formularioRecuperar" class="btn btn-primary">Recuperar Contraseña</a>
   </div>
</div>

</body>
</html>
