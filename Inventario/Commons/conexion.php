<?php
                // Conectar a la base de datos
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "sistema";

                $conn = new mysqli($servername, $username, $password, $dbname);

                // Verificar conexión
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                
 ?>