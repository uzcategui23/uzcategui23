
<?php
// Manejo de eliminación de usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_usuario'])) {
    // Conectar a la base de datos
    $conn = new mysqli("localhost", "root", "", "sistema");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $id_usuario = $_POST['id_usuario'];
    $sql_eliminar_usuario = "DELETE FROM usuarios WHERE id='$id_usuario'";

    if ($conn->query($sql_eliminar_usuario) === TRUE) {
        $_SESSION['mensaje'] = "Usuario eliminado exitosamente.";
    } else {
        $_SESSION['mensaje'] = "Error al eliminar usuario: " . $conn->error;
    }

    $conn->close();
    header("Location: " . $_SERVER['PHP_SELF']); // Redirigir a la misma página
    exit();
}

// Manejo de edición de usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_cambios'])) {
    $conn = new mysqli("localhost", "root", "", "sistema");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $id = $_POST['id'];
    $nombre_empleado = $_POST['nombre_empleado'];
    $cedula = $_POST['cedula'];
    $telefono = $_POST['telefono'];
    $username = $_POST['username'];
    $rol = $_POST['rol'];

    // Actualizar la tabla usuarios
    $sql_actualizar_usuario = "UPDATE usuarios SET nombre_empleado='$nombre_empleado', cedula='$cedula', telefono='$telefono', username='$username' WHERE id='$id'";

    if ($conn->query($sql_actualizar_usuario) === TRUE) {
        // Actualizar la tabla usuario_roles
        $sql_actualizar_rol = "UPDATE usuario_roles SET id_rol='$rol' WHERE id_usuario='$id'";
        $conn->query($sql_actualizar_rol);
        
        $_SESSION['mensaje'] = "Usuario actualizado exitosamente.";
    } else {
        $_SESSION['mensaje'] = "Error al actualizar usuario: " . $conn->error;
    }

    $conn->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Mostrar mensajes de éxito o error
if (isset($_SESSION['mensaje'])) {
    $mensaje = "<div class='alert alert-info'>" . $_SESSION['mensaje'] . "</div>";
    unset($_SESSION['mensaje']); // Limpiar el mensaje después de mostrarlo
}

// Conectar a la base de datos para mostrar usuarios
$conn = new mysqli("localhost", "root", "", "sistema");

// Checar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obtener usuarios registrados con sus roles
$sql = "
    SELECT 
        u.id,
        u.nombre_empleado,
        u.cedula,
        u.telefono,
        u.username,
        r.nombre_rol 
    FROM 
        usuarios u 
    LEFT JOIN 
        usuario_roles ur ON u.id = ur.id_usuario 
    LEFT JOIN 
        rol r ON ur.id_rol = r.id";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include './Commons/head.php'; ?>
</head>
<body>
    <?php include './Commons/nav.php'; ?>
    <main>
        <div id="tabla-container_prov">
            <div class="container text-center">
                <div class="row">
                    <div class="col">
                        <h2 class="text-start">Usuarios Registrados</h2>
                    </div>
                    <div class="col">
                        <a href="http://localhost/inventario/index.php/usuarios/crear" type="button" class="btn btn-primary">Registrar</a>
                    </div>
                </div>
            </div>

            <?php if (isset($mensaje)) echo $mensaje; ?>

            <table id="tabla-usuarios" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre del Empleado</th>
                        <th>Cédula</th>
                        <th>Teléfono</th>
                        <th>Rol</th>
                        <th>Usuario</th>
                        <th>Acciones</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row["nombre_empleado"]); ?></td>
                                <td><?php echo htmlspecialchars($row["cedula"]); ?></td>
                                <td><?php echo htmlspecialchars($row["telefono"]); ?></td>
                                <td><?php echo htmlspecialchars($row["nombre_rol"] ? $row["nombre_rol"] : 'Sin rol'); ?></td>
                                <td><?php echo htmlspecialchars($row["username"]); ?></td>
                                <td> 
                                    <button class="btn btn-warning" data-toggle="modal" data-target="#editModal<?php echo $row['id']; ?>">Editar</button>
                                    
                                    <!-- Modal para editar -->
                                    <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel">Editar Usuario</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                        <div class="form-group">
                                                            <label for="nombre_empleado">Nombre del Empleado</label>
                                                            <input type="text" class="form-control" name="nombre_empleado" value="<?php echo htmlspecialchars($row['nombre_empleado']); ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="cedula">Cédula</label>
                                                            <input type="text" class="form-control" name="cedula" value="<?php echo htmlspecialchars($row['cedula']); ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="telefono">Teléfono</label>
                                                            <input type="text" class="form-control" name="telefono" value="<?php echo htmlspecialchars($row['telefono']); ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="username">Usuario</label>
                                                            <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="rol">Rol</label>
                                                            <select class="form-control" name="rol" required>
                                                                <?php
                                                                // Obtener los roles disponibles
                                                                $result_roles = $conn->query("SELECT * FROM rol");
                                                                while ($row_rol = $result_roles->fetch_assoc()) {
                                                                    $selected = ($row_rol['id'] == $row['id_rol']) ? 'selected' : ''; 
                                                                    echo "<option value='{$row_rol['id']}' $selected>{$row_rol['nombre_rol']}</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <input type="submit" name="guardar_cambios" value="Guardar cambios">
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button class="btn btn-danger" data-toggle="modal" data-target="#deleteModal<?php echo $row['id']; ?>">Eliminar</button>

                                    <!-- Modal para eliminar -->
                                    <div class="modal fade" id="deleteModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel">Eliminar Usuario</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    ¿Estás seguro de que deseas eliminar a <strong><?php echo htmlspecialchars($row["nombre_empleado"]); ?></strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" style="display:inline;">
                                                        <input type="hidden" name="id_usuario" value="<?php echo $row['id']; ?>">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                        <button type="submit" name="eliminar_usuario" class="btn btn-danger">Confirmar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr><td colspan="6">No hay usuarios registrados.</td></tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
    <?php include './Commons/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>