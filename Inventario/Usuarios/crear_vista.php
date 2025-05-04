
<script>/*
function registrarAccion(modulo, accion) {
    fetch('/Inventario/Historial/registro_historial.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ modulo: modulo, accion: accion, user_id: 1 }) // Cambia el user_id según sea necesario
    })
    .then(response => response.json())
    .then(data => {
        console.log('Respuesta del servidor:', data);
    })
    .catch(error => {
        console.error('Error al registrar la acción:', error);
    });
}*/
</script>
<form action="" method="POST" onsubmit="registrarAccion('usuarios', 'Registrar Usuario'); return validateForm();">
</script>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include './Commons/head.php'; ?>
    <link rel="stylesheet" type="text/css" href="http://localhost/inventario/estilo.css">
    <title>Registrar Usuario</title>
</head>
<body>
    <?php include './Commons/nav.php'; ?>
            <?php
            // Mostrar mensajes de éxito o error
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registro_usuario'])) {
                // Conectar a la base de datos
                $conn = new mysqli("localhost", "root", "", "sistema");

                // Verificar conexión
                if ($conn->connect_error) {
                    die("Error de conexión: " . $conn->connect_error);
                }

                // Obtener datos del formulario
                $nombre_empleado = $_POST['nombre_empleado'];
                $cedula = $_POST['cedula'];
                $telefono = $_POST['telefono'];
                $username = $_POST['username'];
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encriptar la contraseña
                $email = $_POST['email'];
                $rol_id = $_POST['rol_id'];

                // Verificar si el nombre de usuario ya existe
                $sql_check_username = "SELECT * FROM usuarios WHERE username = '$username'";
                $result_username = $conn->query($sql_check_username);

                if ($result_username->num_rows > 0) {
                    echo "<div class='alert alert-danger'>El nombre de usuario '$username' ya está en uso. Por favor, elige otro.</div>";
                } else {
                    // Insertar nuevo usuario
                    $sql_usuario = "INSERT INTO usuarios (nombre_empleado, cedula, telefono, username, password, email) VALUES ('$nombre_empleado', '$cedula', '$telefono', '$username', '$password', '$email')";

                    try {
                        if ($conn->query($sql_usuario) === TRUE) {
                            $usuario_id = $conn->insert_id; // Obtener el ID del nuevo usuario

                            // Asignar rol al nuevo usuario
                            $sql_usuario_rol = "INSERT INTO usuario_roles (id_usuario, id_rol) VALUES ('$usuario_id', '$rol_id')";
                            if ($conn->query($sql_usuario_rol) === TRUE) {
                                echo "<div class='alert alert-success'>Usuario registrado exitosamente y rol asignado correctamente.</div>";
                            } else {
                                echo "<div class='alert alert-danger'>Error al asignar rol: " . htmlspecialchars($conn->error) . "</div>";
                            }
                        }
                    } catch (mysqli_sql_exception $e) {
                        if ($e->getCode() == 1062) { // Código de error para entrada duplicada
                            echo "<div class='alert alert-danger'>El correo electrónico '$email' ya está en uso. Por favor, utiliza otro.</div>";
                        } else {
                            echo "<div class='alert alert-danger'>Error al registrar usuario: " . htmlspecialchars($e->getMessage()) . "</div>";
                        }
                    }
                }

                // Cerrar conexión
                $conn->close();
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
        .flex-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
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
    </style>
    <title>Registrar Usuario</title>
</head>
<body>
    <div id="inventario-form">
        <h2 style="text-align: center;">Registrar Nuevo Usuario</h2>
        <div class="container">
            <div class="left">
                <div class="flex-container">
                    <p><b>¡Bienvenido, Administrador!</b><br>
                    Este módulo ha sido diseñado especialmente para usted. A continuación, encontrará los pasos necesarios para registrar una nueva cuenta de usuario:</p>
                    <img src="http://localhost/inventario/Usuarios/formulario.png" alt="Descripción de la imagen"> 
                </div>
                
                <p><b>Información del Empleado:</b></p>
                <ul>
                    <li>Ingrese el nombre y apellido del empleado.</li>
                    <li>Proporcione la cédula correspondiente.</li>
                    <li>Introduzca el número de teléfono, asegurándose de ingresar solo dígitos.</li>
                </ul>
                <div class="more-text">           
                         <p><b>Credenciales de Acceso:</b></p>
                <ul>
                    <li>Elija un nombre de usuario que desee utilizar para iniciar sesión.</li>
                    <li>Establezca una contraseña segura. Recuerde que una contraseña fuerte es fundamental para la seguridad de la cuenta.</li>
                    <li>Ingrese una dirección de correo electrónico válida. Este correo será utilizado para recuperar la contraseña en caso de que se olvide.</li>
                </ul>

                <p><b>Selección de Rol:</b></p>
                <ul>
                    <li>Si elige "Administrador", tendrá acceso completo al sistema.</li>
                    <li>Si selecciona "Usuario", el acceso será limitado y no podrá ingresar al módulo de usuarios para registrar nuevas cuentas.</li>
                </ul>
                </div>
                
                <span class="read-more" onclick="toggleText()">Leer más</span>
            </div>
            <div class="right">
            <form action="" method="POST" onsubmit="return validateForm()">
    <label for="nombre_empleado">Nombre del Empleado:</label>
    <input type="text" id="nombre_empleado" name="nombre_empleado" required title="Ingrese el nombre completo del empleado">

    <label for="cedula">Cédula:</label>
    <input type="text" id="cedula" name="cedula" required title="Ingrese la cédula de identidad del empleado" onkeypress="return isNumberKey(event)">

    <label for="telefono">Teléfono:</label>
    <input type="text" id="telefono" name="telefono" required title="Ingrese el número de teléfono del empleado" onkeypress="return isNumberKey(event)">

    <label for="username">Nombre de Usuario:</label>
    <input type="text" id="username" name="username" required title="Ingrese un nombre de usuario único">

    <label for="password">Contraseña:</label>
    <input type="password" id="password" name="password" required title="Ingrese una contraseña segura">

    <label for="email">Correo Electrónico:</label>
    <input type="email" id="email" name="email" required title="Ingrese un correo electrónico válido">

    <label for="rol_id">Rol:</label>
    <select id="rol_id" name="rol_id" required title="Seleccione el rol del usuario">
        <option value="">Seleccione un rol</option>
        <?php
            // Conectar a la base de datos
            $conn = new mysqli("localhost", "root", "", "sistema");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $sql = "SELECT id, nombre_rol FROM rol";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['nombre_rol']}</option>";
                }
            }
            $conn->close();
        ?>
    </select>
    <input type="submit" name="registro_usuario" value="Registrar Usuario">
</form>

<script>
    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        // Permitir solo números (0-9)
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }

    function validateForm() {
        const cedula = document.getElementById('cedula').value;
        const telefono = document.getElementById('telefono').value;

        // Comprobar que la cédula y el teléfono contengan solo números
        if (!/^\d+$/.test(cedula)) {
            alert('La cédula debe contener solo números.');
            return false;
        }
        if (!/^\d+$/.test(telefono)) {
            alert('El teléfono debe contener solo números.');
            return false;
        }
        return true; // Permitir el envío si todo es válido
    }
</script>
            </div>
        </div>
    </div>
    <script>
        function toggleText() {
            const moreText = document.querySelector('.more-text');
            moreText.style.display = moreText.style.display === 'none' || moreText.style.display === '' ? 'block' : 'none';
            const readMore = document.querySelector('.read-more');
            readMore.textContent = readMore.textContent === 'Leer más' ? 'Leer menos' : 'Leer más';
        }
    </script>
</body>
</html>