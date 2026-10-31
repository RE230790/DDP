<?php
include 'conexion.php';
include 'init.php'; 

$result_solicitudes = $conn->query("SELECT * FROM solicitudes_web WHERE estado = 'pendiente' ORDER BY creado_en ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bandeja de Solicitudes | OdontoWeb</title>
    <link rel="stylesheet" href="css/crud.css?v=6"> 
    <script defer src="js/script.js?v=6"></script>
</head>
<body>
    <div style="text-align: center; padding: 20px;">
        <img src="logos/bandeja.png" alt="Icono Mensajes" style="max-width: 120px; height: auto; margin-bottom: 10px;">
        
        <h1 style="color: #f4a261; margin-top: 5px; font-size: 2.5em;">Bandeja de Solicitudes Web</h1>
        <p style="color: #6c757d; font-size: 1.1em;">Revisa y gestiona las peticiones de citas desde el portal.</p>
    </div>
 
    <!-- BARRA DE USUARIO ALINEADA SIN BOTÓN DE CERRAR SESIÓN -->
    <div class="user-bar" style="display: flex; align-items: center; justify-content: center; gap: 15px; padding: 15px;">
        
        <div style="display: flex; align-items: center;">
            <img src="logos/usuario.png" alt="Usuario" style="width: 20px; height: 20px; margin-right: 5px;">
            <span>Usuario: <?php echo $_SESSION['usuario_nombre']; ?></span>
        </div>
        
        <button onclick="window.location.href='indexcrud.php'" style="background: linear-gradient(135deg, #0077b6 0%, #023e8a 100%); color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; margin: 0; font-size: 14px;">
            Volver al Sistema Principal
        </button>
        
    </div>

    <div>
        <p style="color: #6c757d; font-size: 14px; margin-bottom: 15px;">Selecciona una solicitud de la tabla para aprobarla o rechazarla.</p>
        <button id="aprobar-solicitud" style="background: linear-gradient(135deg, #2a9d8f 0%, #21867a 100%);">Marcar como Aprobada</button>
        <button id="rechazar-solicitud" style="background: linear-gradient(135deg, #e63946 0%, #d62828 100%);">Rechazar Solicitud</button>
    </div>

    <br>

    <table border="1" id="solicitudes-table">
        <thead style="background: linear-gradient(135deg, #f4a261 0%, #e76f51 100%);">
            <tr>
                <th>Ticket</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Teléfono</th>
                <th>Fecha Deseada</th>
                <th>Hora</th>
                <th>Motivo</th>
                <th>Recibida el</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result_solicitudes->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['nombre'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['apellido'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['telefono'] ?? '') ?></td>
                    <td><?= $row['fecha_deseada'] ?></td>
                    <td><?= $row['hora_deseada'] ?></td>
                    <td><?= htmlspecialchars($row['motivo'] ?? '') ?></td>
                    <td><?= $row['creado_en'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>