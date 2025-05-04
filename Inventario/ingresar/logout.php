<?php
session_start(); // Iniciar la sesión

// Eliminar todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();

// Redirigir al usuario a la página de inicio o login
header("Location: http://localhost/inventario/index.php/ingresar");
exit(); // Asegúrate de salir después de redirigir
?>
