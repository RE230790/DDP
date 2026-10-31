<?php
include 'conexion.php';
$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $contrasena_plana = $_POST['contrasena']; 

    $contrasena_encriptada = password_hash($contrasena_plana, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuario (nombre, correo, contrasena) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nombre, $correo, $contrasena_encriptada);

    if ($stmt->execute()) {
        header("Location: index.php?registro=exito");
        exit();
    } else {
        $mensaje = "<div class='alert error'>❌ Error al registrar usuario: " . $conn->error . "</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta | OdontoWeb</title>
    <link rel="stylesheet" href="css/registro.css?v=3">
</head>
<body>

    <div class="registro-container">
        <img src="logos/dental.png" alt="Logo OdontoWeb">
        <h2>Crear Cuenta</h2>
        <p class="subtitulo">Registra un nuevo usuario para el sistema</p>
        
        <?= $mensaje ?>

        <form action="registro.php" method="POST">
            <div class="form-group">
                <label>Usuario</label>
                <input type="text" name="nombre" placeholder=" " required>
            </div>
            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" name="correo" placeholder=" " required>
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <div class="password-container">
                    <input type="password" id="contrasena_input" name="contrasena" placeholder="Mínimo 8 caracteres" minlength="8" required>
                    <!-- Botón con el icono SVG del Ojo -->
                    <button type="button" class="toggle-password" onclick="mostrarOcultarPass()" title="Ver contraseña" style="display: flex; align-items: center; justify-content: center; height: 100%;">
                        <span id="icono-ojo">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="btn-submit">Registrar Usuario</button>
        </form>
        
        <a href="index.php" class="login-link">¿Ya tienes cuenta? <span>Inicia sesión aquí</span></a>
    </div>

    <script>
        // Códigos SVG para los iconos vectoriales
        const ojoAbierto = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
        const ojoCerrado = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;

        function mostrarOcultarPass() {
            var input = document.getElementById("contrasena_input");
            var contenedorIcono = document.getElementById("icono-ojo");
            
            if (input.type === "password") {
                input.type = "text";
                contenedorIcono.innerHTML = ojoCerrado;
            } else {
                input.type = "password";
                contenedorIcono.innerHTML = ojoAbierto;
            }
        }
    </script>
</body>
</html>