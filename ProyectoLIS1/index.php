<?php
session_start();
include 'conexion.php';

$error = ''; 
$mensaje_exito = '';

// Si viene redirigido desde registro.php con éxito, mostramos el mensaje
if (isset($_GET['registro']) && $_GET['registro'] == 'exito') {
    $mensaje_exito = "✅ Cuenta creada exitosamente. Ya puedes iniciar sesión.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo']);  
    $contrasena = $_POST['contrasena'];

    // Agregamos intentos_fallidos y bloqueado_hasta a la consulta
    $stmt = $conn->prepare("SELECT id, nombre, contrasena, intentos_fallidos, bloqueado_hasta FROM usuario WHERE correo = ?"); 
    $stmt->bind_param("s", $correo);  
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        
        // 1. Verificamos si la cuenta está bloqueada actualmente
        if ($row['bloqueado_hasta'] != null && strtotime($row['bloqueado_hasta']) > time()) {
            $segundos_restantes = strtotime($row['bloqueado_hasta']) - time();
            $minutos_restantes = ceil($segundos_restantes / 60);
            $error = "Has superado el límite de intentos. Cuenta bloqueada por seguridad. Intenta de nuevo en $minutos_restantes minuto(s).";
        } else {
            // 2. Si no está bloqueada, verificamos la contraseña
            if (password_verify($contrasena, $row['contrasena'])) {
                
                // ¡ÉXITO! Reseteamos los intentos a 0 y quitamos el bloqueo
                $reset = $conn->prepare("UPDATE usuario SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE correo = ?");
                $reset->bind_param("s", $correo);
                $reset->execute();

                // Iniciamos la sesión de forma segura
                session_regenerate_id(true);

                $_SESSION['usuario_id'] = $row['id']; 
                $_SESSION['usuario_nombre'] = $row['nombre']; 
                $_SESSION['inicio'] = time();
                setcookie("usuario_id", $row['id'], time() + (86400 * 5), "/"); 
                
                header("Location: indexcrud.php");
                exit();
            } else {
                // CONTRASEÑA INCORRECTA
                $intentos = $row['intentos_fallidos'] + 1;
                
                if ($intentos >= 3) {
                    // Bloqueamos la cuenta por 2 minutos desde este exacto momento
                    $bloqueo = $conn->prepare("UPDATE usuario SET intentos_fallidos = ?, bloqueado_hasta = DATE_ADD(NOW(), INTERVAL 2 MINUTE) WHERE correo = ?");
                    $bloqueo->bind_param("is", $intentos, $correo);
                    $bloqueo->execute();
                    $error = "Has fallado 3 veces. Tu cuenta ha sido bloqueada por 2 minutos.";
                } else {
                    // Solo sumamos un intento fallido y le avisamos cuántos le quedan
                    $suma = $conn->prepare("UPDATE usuario SET intentos_fallidos = ? WHERE correo = ?");
                    $suma->bind_param("is", $intentos, $correo);
                    $suma->execute();
                    $restantes = 3 - $intentos;
                    $error = "Correo o contraseña incorrectos. Te quedan $restantes intento(s).";
                }
            }
        }
    } else {
        $error = "Correo o contraseña incorrectos."; 
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/index.css">
    <style>
        .error { color: #e63946; background: #f8d7da; padding: 10px; border-radius: 5px; text-align: center; margin-top: 15px; font-size: 14px; border: 1px solid #f5c6cb; }
        .success { color: #2a9d8f; background: #f1f9fa; padding: 10px; border-radius: 5px; text-align: center; margin-bottom: 15px; font-size: 14px; border: 1px solid #2a9d8f; font-weight: bold; }
        
        /* Estilos específicos para el ojito de la contraseña */
        .password-container {
            position: relative;
            width: 100%;
        }
        .password-container input {
            width: 100%;
            padding-right: 45px; /* Deja espacio interior para que el texto no se monte sobre el icono */
            box-sizing: border-box;
        }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.3s;
        }
        .toggle-password:hover {
            color: #0077b6; /* Se pinta de azul al pasar el mouse */
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Iniciar Sesión</h2>
    
    <!-- Muestra mensaje de éxito si acaba de registrarse -->
    <?php if (!empty($mensaje_exito)) echo "<div class='success'>$mensaje_exito</div>"; ?>

    <form method="POST">
        <input type="text" name="correo" placeholder="Correo" required><br>  
        
        <!-- Campo de contraseña envuelto para soportar el icono -->
        <div class="password-container">
            <input type="password" name="contrasena" id="contrasena" placeholder="Contraseña" required>
            <span class="toggle-password" onclick="togglePassword()" id="eye-icon">
                <!-- Icono de ojo (SVG) -->
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            </span>
        </div>
        <br>
        
        <button type="submit" class="btn">Ingresar</button>
        
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    </form>
  
    <div class="register-link">
        <p>¿No tienes cuenta? <a href="registro.php" class="btn-register">Regístrate aquí</a></p>
    </div>
</div>

<script>
    function togglePassword() {
        var pwdInput = document.getElementById("contrasena");
        var eyeIcon = document.getElementById("eye-icon");
        
        if (pwdInput.type === "password") {
            pwdInput.type = "text";
            // Cambiamos el SVG cuando se ve la contraseña
            eyeIcon.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
        } else {
            pwdInput.type = "password";
            // Devolvemos el SVG cuando se oculta
            eyeIcon.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
        }
    }
</script>

</body>
</html>