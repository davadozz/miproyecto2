<?php
// Configuración de la base de datos
$servidor = "elrocasad.ddns.net"; 
$usuario = "david34varela";
$clave = "Chronos01";
$baseDeDatos = "pruebas2";

// Procesamiento de formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enlace = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);
    
    if (!$enlace) {
        die(json_encode(['error' => 'Error de conexión: ' . mysqli_connect_error()]));
    }

    // Crear tabla si no existe
    $crearTabla = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        correo VARCHAR(100) NOT NULL UNIQUE,
        contrasena VARCHAR(255) NOT NULL,
        fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    mysqli_query($enlace, $crearTabla);

    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['accion'])) {
        if ($data['accion'] === 'registro') {
            $nombre = mysqli_real_escape_string($enlace, $data['nombre']);
            $correo = mysqli_real_escape_string($enlace, $data['correo']);
            $contrasena = mysqli_real_escape_string($enlace, $data['contrasena']);
            
            $insertar = "INSERT INTO usuarios (nombre, correo, contrasena) VALUES ('$nombre', '$correo', '$contrasena')";
            
            if (mysqli_query($enlace, $insertar)) {
                echo json_encode(['success' => 'Registro exitoso']);
            } else {
                echo json_encode(['error' => 'Error al registrar: ' . mysqli_error($enlace)]);
            }
        }
        elseif ($data['accion'] === 'login') {
            $correo = mysqli_real_escape_string($enlace, $data['correo']);
            $contrasena = mysqli_real_escape_string($enlace, $data['contrasena']);
            
            $consulta = "SELECT * FROM usuarios WHERE correo = '$correo' AND contrasena = '$contrasena'";
            $resultado = mysqli_query($enlace, $consulta);
            
            if (mysqli_num_rows($resultado) > 0) {
                echo json_encode(['success' => 'Login exitoso']);
            } else {
                echo json_encode(['error' => 'Credenciales incorrectas']);
            }
        }
    }
    mysqli_close($enlace);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso | Mi Proyecto</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ESTILOS GENERALES */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            color: #FFFFFF;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* [Todos tus estilos CSS existentes...] */

        /* Animaciones para mensajes */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-20px); }
        }

        .mensaje-flotante {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            color: white;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 1000;
            animation: fadeIn 0.3s;
        }

        .mensaje-flotante.error {
            background-color: #ff4444;
        }

        .mensaje-flotante.success {
            background-color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="video-background">
        <video autoplay muted loop id="video-bg">
            <source src="13139048_3840_2160_25fps (1).webm" type="video/mp4">
            <img src="13139048_3840_2160_25fps (1).webm" alt="Fondo de estrellas">
        </video>
    </div>

    <div class="main-container">
        <div class="auth-container">
            <div class="logo">
                <i class="fas fa-rocket"></i> MiProyecto
            </div>

            <div class="auth-tabs">
                <button class="tab-btn active" id="login-tab">Iniciar Sesión</button>
                <button class="tab-btn" id="register-tab">Registrarse</button>
            </div>

            <form id="login-form" class="auth-form active">
                <div class="form-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="login-email" placeholder="Correo electrónico" required>
                </div>
                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="login-password" placeholder="Contraseña" required>
                </div>
                <button type="submit" class="submit-btn">
                    <i class="fas fa-sign-in-alt"></i> Acceder
                </button>
                <div class="auth-footer">
                    <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
                </div>
            </form>

            <form id="register-form" class="auth-form">
                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="register-name" placeholder="Nombre completo" required>
                </div>
                <div class="form-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="register-email" placeholder="Correo electrónico" required>
                </div>
                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="register-password" placeholder="Contraseña" required>
                </div>
                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="register-confirm" placeholder="Confirmar contraseña" required>
                </div>
                <button type="submit" class="submit-btn">
                    <i class="fas fa-user-plus"></i> Crear cuenta
                </button>
            </form>
        </div>
    </div>

    <script>
        // Función para mostrar mensajes
        function mostrarMensaje(mensaje, esError = false) {
            const contenedor = document.createElement('div');
            contenedor.className = `mensaje-flotante ${esError ? 'error' : 'success'}`;
            contenedor.textContent = mensaje;
            
            document.body.appendChild(contenedor);
            
            setTimeout(() => {
                contenedor.style.animation = 'fadeOut 0.3s';
                setTimeout(() => contenedor.remove(), 300);
            }, 3000);
        }

        // Función para enviar datos
        async function enviarDatos(datos) {
            try {
                const response = await fetch('<?php echo $_SERVER['PHP_SELF']; ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(datos)
                });

                const responseData = await response.json();
                
                if (!response.ok || responseData.error) {
                    throw new Error(responseData.error || 'Error en la solicitud');
                }

                return responseData;
            } catch (error) {
                console.error('Error:', error);
                mostrarMensaje(error.message, true);
                throw error;
            }
        }

        // Manejo de pestañas
        document.getElementById('login-tab').addEventListener('click', function() {
            this.classList.add('active');
            document.getElementById('register-tab').classList.remove('active');
            document.getElementById('login-form').classList.add('active');
            document.getElementById('register-form').classList.remove('active');
        });

        document.getElementById('register-tab').addEventListener('click', function() {
            this.classList.add('active');
            document.getElementById('login-tab').classList.remove('active');
            document.getElementById('register-form').classList.add('active');
            document.getElementById('login-form').classList.remove('active');
        });

        // Registro
        document.getElementById('register-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = {
                accion: 'registro',
                nombre: document.getElementById('register-name').value,
                correo: document.getElementById('register-email').value,
                contrasena: document.getElementById('register-password').value,
                confirmacion: document.getElementById('register-confirm').value
            };
            
            if (formData.contrasena !== formData.confirmacion) {
                mostrarMensaje('Las contraseñas no coinciden', true);
                return;
            }
            
            try {
                const resultado = await enviarDatos(formData);
                mostrarMensaje(resultado.success);
                document.getElementById('login-tab').click();
                document.getElementById('register-form').reset();
            } catch (error) {
                // El error ya se mostró
            }
        });

        // Login
        document.getElementById('login-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            try {
                const resultado = await enviarDatos({
                    accion: 'login',
                    correo: document.getElementById('login-email').value,
                    contrasena: document.getElementById('login-password').value
                });
                
                mostrarMensaje(resultado.success);
                setTimeout(() => {
                    window.location.href = 'formulario.html';
                }, 1500);
            } catch (error) {
                // El error ya se mostró
            }
        });
    </script>
</body>
</html>