<?php 
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

/*ingresar*/
if ($uri == "/inventario/index.php/ingresar" && $method == 'GET') { //boton ingresar
  include './Ingresar/index.php';
}else if ($uri == "/inventario/index.php/ingresar" && $method == 'POST') { //boton ingresar
    include './Ingresar/index.php';
    
} else if ($uri == "/inventario/index.php/ingresar/formularioRecuperar" && $method == 'GET') {
  include './Ingresar/formularioRecuperar.php';
} else if ($uri == "/inventario/index.php/ingresar/formularioRecuperar" && $method == 'POST') {
  include './Ingresar/formularioRecuperar.php';
} 
else if ($uri == "/inventario/index.php/ingresar/formularioRecuperar" && $method == 'GET') {
  include './Ingresar/formularioRecuperar.php';
} else if ($uri == "/inventario/index.php/ingresar/formularioRecuperar" && $method == 'POST') {
  include './Ingresar/formularioRecuperar.php';
} 
 else if ($uri == "/inventario/index.php/ingresar/logout.php" && $method == 'GET') {
  include './Ingresar/index.php';

} else if ($uri == "/inventario/index.php/ingresar/logout.php" && $method == 'POST') {
  include './Ingresar/index.php';
}
 else if ($uri == "/inventario/index.php/ingresar/admin" && $method == 'POST') {
  include './Ingresar/admin.php'; // Página para administradores
}  else if ($uri == "/inventario/index.php/ingresar/admin" && $method == 'GET') {
  include './Ingresar/admin.php'; // Página para administradores
} 

else if ($uri == "/inventario/index.php/ingresar/usuario" && $method == 'GET') {
  include './Ingresar/usuario.php'; // Página para usuarios regulares
} else if ($uri == "/inventario/index.php/ingresar/usuario" && $method == 'POST') {
  include './Ingresar/usuario.php'; // Página para usuarios regulares
}


/*Busqueda-inicio*/
if ($uri == "/inventario/index.php/inicio" && $method == 'GET') {
  include './inicio/inicio.php';
}
if ($uri == "/inventario/index.php/inicio" && $method == 'POST') {
  include './inicio/inicio.php';
}

/*USUARIOS */
if($uri == "/inventario/index.php/usuarios" && $method == 'GET') {
  include './Usuarios/index.php';
}
else if($uri == "/inventario/index.php/usuarios" && $method == 'POST') {
  include './Usuarios/index.php';
}
else if($uri == "/inventario/index.php/usuarios/crear" && $method == 'GET') {
  include './Usuarios/crear_vista.php';
}
else if ($uri == "/inventario/index.php/usuarios/crear" && $method == 'POST') {
  include './Usuarios/crear_vista.php';
}


/*PRODUCTOS*/
if ($uri == "/inventario/index.php/productos" && $method == 'GET') {
  include './Productos/index.php';
}
else if ($uri == "/inventario/index.php/productos" && $method == 'POST') {
  include './Productos/index.php';
}
else if($uri == "/inventario/index.php/productos/crear" && $method == 'GET') {
  include './Productos/crear_vista.php';
}
else if ($uri == "/inventario/index.php/productos/crear" && $method == 'POST') {
  include './Productos/crear_vista.php';
}
else if($uri == "/inventario/index.php/productos/tasa" && $method == 'GET') {
  include './Productos/registrar_tasa.php';
}
else if ($uri == "/inventario/index.php/productos/tasa" && $method == 'POST') {
  include './Productos/registrar_tasa.php';
}
/*VENTAS*/
if ($uri == "/inventario/index.php/ventas" && $method == 'GET') {
  include './Ventas/index.php';
}
else if($uri == "/inventario/index.php/ventas/crear" && $method == 'GET') {
  include './Ventas/crear_vista.php';
}
else if ($uri == "/inventario/index.php/ventas/crear" && $method == 'POST') {
  include './Ventas/crear_vista.php';
}
else if ($uri == "/inventario/index.php/ventas/ventashoy" && $method == 'GET') {
  include './Ventas/ventasdeldia.php';
}
else if ($uri == "/inventario/index.php/ventas/devoluciones" && $method == 'GET') {
  include './Ventas/devolucion.php';
}
else if ($uri == "/inventario/index.php/ventas/devoluciones" && $method == 'POST') {
  include './Ventas/devolucion.php';
}

/*ayuda*/
if ($uri == "/inventario/index.php/ayuda" && $method == 'GET') {
  include './ayuda/index.php';
}
else if ($uri == "/inventario/index.php/ayuda/crear" && $method == 'GET') {
  include './ayuda/crear_vista.php';
}
else if ($uri == "/inventario/index.php/ayuda/crear" && $method == 'POST') {
  include './Ayuda/crear_vista.php';
}

/*Estadistica*/
if ($uri == "/inventario/index.php/estadisticas" && $method == 'GET') {
  include './estadisticas/index.php';
}
 else if ($uri == "/inventario/index.php/estadisticas" && $method == 'POST') {
  include './estadisticas/venta.php';
}
 else if ($uri == "/inventario/index.php/estadisticas" && $method == 'POST') {
  include './estadisticas/producto.php';
}
/*HISTORIAL*/
if ($uri == "/inventario/index.php/historial" && $method == 'GET') {
  include './historial/index.php';
}
 else if ($uri == "/inventario/index.php/historial" && $method == 'POST') {
  include './historial/index.php';
}
?>