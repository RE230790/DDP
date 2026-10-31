<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido']; 
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'] ?? '';
    $fecha_deseada = $_POST['fecha'];
    $hora_deseada = $_POST['hora'];
    $duracion = $_POST['duracion']; // CAPTURAMOS LA DURACIÓN
    
    // Unimos el catálogo de servicio con lo que escribió el paciente libremente
    $servicio_nombre = $_POST['servicio_nombre'];
    $detalles = $_POST['detalles'];
    $motivo_completo = "Tratamiento: " . $servicio_nombre . "\nDetalle: " . $detalles;
    
    $estado = 'pendiente';
    $ticket_id = null;
    $exito = false;

    if (isset($conn)) {
        // Guardamos también la duración
        $sql = "INSERT INTO solicitudes_web (nombre, apellido, telefono, correo, fecha_deseada, hora_deseada, duracion_minutos, motivo, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssiss", $nombre, $apellido, $telefono, $correo, $fecha_deseada, $hora_deseada, $duracion, $motivo_completo, $estado);
        
        if ($stmt->execute()) {
            $ticket_id = $conn->insert_id; 
            $exito = true;
        }
        $stmt->close();
        $conn->close();
    }
} else {
    header("Location: portal.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud Enviada | OdontoWeb</title>
    <link rel="stylesheet" href="css/portal.css">
    <style>
        .success-box { background-color: #ffffff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); max-width: 500px; margin: 100px auto; padding: 40px; text-align: center; border-top: 5px solid #2a9d8f; }
        .success-box h1 { color: #2a9d8f; margin-bottom: 20px; }
        .ticket-number { font-size: 3em; color: #0077b6; font-weight: 700; margin: 20px 0; background: #f1f9fa; padding: 15px; border-radius: 8px; display: inline-block; }
    </style>
</head>
<body class="bg-light">
    <?php if ($exito): ?>
        <div class="success-box">
            <h1>¡Cita Apartada!</h1>
            <p>Hola <strong><?= htmlspecialchars($nombre) ?></strong>, la doctora ya reservó este bloque de tiempo para ti.</p>
            <div class="ticket-number">Ticket #<?= $ticket_id ?></div>
            <p style="color: #555; margin-bottom: 30px; font-size: 14px;">
                Nuestro equipo aprobará la solicitud en breve. Usa tu número de ticket para consultar el estado en nuestro portal.
            </p>
            <a href="portal.php" class="btn-main">Volver al Portal</a>
        </div>
    <?php else: ?>
        <div class="success-box" style="border-top: 5px solid #e63946;">
            <h1 style="color: #e63946;">Ocurrió un error</h1>
            <p>No pudimos procesar tu solicitud. Revisa si llenaste la hora.</p>
            <a href="portal.php" class="btn-main">Volver al Portal</a>
        </div>
    <?php endif; ?>
</body>
</html>