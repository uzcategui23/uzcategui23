<!DOCTYPE html>
<html lang="es">
<head>
    <?php include './Commons/head.php'; ?>
    <title>Módulo de Ayuda</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .content {
            display: none;
        }
        .active {
            display: block;
        }
        .btn-group {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .btn {
            margin-right: 10px;
            background-color: rgba(0, 123, 255, 0.3);
            color: white;
        }
        .btn:hover {
            background-color: rgba(0, 123, 255, 0.5);
        }
        .row {
            align-items: center;
            margin-top: 20px;
        }
        .video-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include './Commons/nav.php'; ?>
    <main class="container text-center">
        <div class="btn-group">
            <button id="loginBtn" class="btn btn-primary">Módulo Login</button>
            <button id="userBtn" class="btn btn-primary">Módulo Usuario</button>
            <button id="productBtn" class="btn btn-primary">Módulo Producto</button>
            <button id="salesBtn" class="btn btn-primary">Módulo Venta</button>
            <button id="statsBtn" class="btn btn-primary">Módulo Estadísticas</button>
        </div>

        <div id="loginContent" class="content active">
            <h3>Ayuda - Módulo Login</h3>
            <p>Aquí encontrarás información útil sobre el módulo de login.</p>
            <p>Los datos presentados son meramente ejemplos.</p>
            <div class="video-container">
                <video controls class="img-fluid">
                    <source src="http://localhost/Inventario/Ayuda/login.mp4" type="video/mp4">
                    Tu navegador no soporta el video.
                </video>
            </div>
        </div>

        <div id="userContent" class="content">
            <h3>Ayuda - Módulo Usuario</h3>
            <p>Aquí encontrarás información útil sobre el módulo de usuario.</p>
            <p>Los datos presentados son meramente ejemplos.</p>
            <div class="video-container">
                <video controls class="img-fluid">
                    <source src="http://localhost/Inventario/Ayuda/Usuarios.mp4" type="video/mp4">
                    Tu navegador no soporta el video.
                </video>
            </div>
        </div>

        <div id="productContent" class="content">
            <h3>Ayuda - Módulo Producto</h3>
            <p>Aquí encontrarás información útil sobre el módulo de producto.</p>
            <p>Los datos presentados son meramente ejemplos.</p>
            <div class="video-container">
                <video controls class="img-fluid">
                    <source src="http://localhost/Inventario/Ayuda/Inventario.mp4" type="video/mp4">
                    Tu navegador no soporta el video.
                </video>
            </div>
        </div>

        <div id="salesContent" class="content">
            <h3>Ayuda - Módulo Venta</h3>
            <p>Aquí encontrarás información útil sobre el módulo de venta.</p>
            <p>Los datos presentados son meramente ejemplos.</p>
                        <div class="video-container">
                <video controls class="img-fluid">
                    <source src="http://localhost/Inventario/Ayuda/ventas.mp4" type="video/mp4">
                    Tu navegador no soporta el video.
                </video>
            </div>
        </div>

        <div id="statsContent" class="content">
            <h3>Ayuda - Módulo Estadísticas</h3>
            <p>Aquí encontrarás información útil sobre el módulo de estadísticas.</p>
            <p>Los datos presentados son meramente ejemplos.</p>
            <div class="video-container">
                <video controls class="img-fluid">
                    <source src="http://localhost/Inventario/Ayuda/estadisticas.mp4" type="video/mp4">
                    Tu navegador no soporta el video.
                </video>
            </div>
        </div>

        </div>
    </main>

    <script>
        const loginBtn = document.getElementById('loginBtn');
        const userBtn = document.getElementById('userBtn');
        const salesBtn = document.getElementById('salesBtn');
        const productBtn = document.getElementById('productBtn');
        const statsBtn = document.getElementById('statsBtn');

        const loginContent = document.getElementById('loginContent');
        const userContent = document.getElementById('userContent');
        const salesContent = document.getElementById('salesContent');
        const productContent = document.getElementById('productContent');
        const statsContent = document.getElementById('statsContent');

        function hideAllContents() {
            loginContent.classList.remove('active');
            userContent.classList.remove('active');
            salesContent.classList.remove('active');
            productContent.classList.remove('active');
            statsContent.classList.remove('active');
        }

        loginBtn.onclick = function() {
            hideAllContents();
            loginContent.classList.add('active');
        }

        userBtn.onclick = function() {
            hideAllContents();
            userContent.classList.add('active');
        }

        salesBtn.onclick = function() {
            hideAllContents();
            salesContent.classList.add('active');
        }

        productBtn.onclick = function() {
            hideAllContents();
            productContent.classList.add('active');
        }

        statsBtn.onclick = function() {
            hideAllContents();
            statsContent.classList.add('active');
        }
    </script>
</body>
</html>