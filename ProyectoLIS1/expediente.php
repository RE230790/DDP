<?php
include 'conexion.php';
include 'init.php';

if (!isset($_GET['paciente_id'])) {
    header("Location: indexcrud.php");
    exit();
}

$paciente_id = $_GET['paciente_id'];

// Obtener datos del paciente
$query_paciente = $conn->query("SELECT * FROM pacientes WHERE id = $paciente_id");
$paciente = $query_paciente->fetch_assoc();

// Obtener texto del expediente
$query_exp = $conn->query("SELECT * FROM expedientes WHERE paciente_id = $paciente_id");
$expediente = $query_exp->fetch_assoc();

// Obtener archivos adjuntos
$query_archivos = $conn->query("SELECT * FROM archivos_expediente WHERE paciente_id = $paciente_id ORDER BY fecha_subida DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Expediente - <?= htmlspecialchars($paciente['nombre'] ?? 'Paciente') ?></title>
    <link rel="stylesheet" href="css/crud.css?v=6">
    <style>
        .expediente-container { max-width: 900px; margin: 30px auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
        .header-exp { border-bottom: 3px solid #007bff; padding-bottom: 15px; margin-bottom: 25px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { font-weight: bold; display: block; margin-bottom: 8px; color: #2b2d42; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 6px; font-family: inherit; }
        .form-group textarea { resize: vertical; min-height: 100px; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #007bff; box-shadow: 0 0 5px rgba(0,123,255,0.3); }
        .btn-save { background: linear-gradient(135deg, #2a9d8f 0%, #21867a 100%); color: white; padding: 12px 25px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: bold; width: 100%; transition: opacity 0.3s; }
        .btn-save:hover { opacity: 0.9; }
        .upload-box { background: #f8fbfa; border: 2px dashed #2a9d8f; padding: 25px; text-align: center; border-radius: 8px; margin-top: 20px; }
        .upload-box:hover { background: #f1f9fa; }
        .archivos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 20px; margin-top: 25px; }
        .archivo-card { border: 1px solid #e9ecef; padding: 15px; text-align: center; border-radius: 10px; background: #ffffff; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .archivo-icon { font-size: 40px; margin-bottom: 10px; }
        .archivo-card p { font-size: 13px; font-weight: 500; word-break: break-all; margin: 0 0 15px 0; color: #495057; }
        .archivo-card a { text-decoration: none; background: #0077b6; color: white; padding: 6px 12px; border-radius: 5px; font-size: 13px; font-weight: bold; display: inline-block; }
        .archivo-card a:hover { background: #023e8a; }
    </style>
</head>
<body style="background-color: #f4f7f6;">

    <div class="user-bar">
        <span>Usuario: <?php echo $_SESSION['usuario_nombre']; ?></span>
        <button onclick="window.location.href='indexcrud.php'" style="background: #0077b6; margin-left: 15px;">Volver al Sistema Principal</button>
    </div>

    <div class="expediente-container">
        <!-- CABECERA -->
        <div class="header-exp">
            <h1 style="color: #007bff; margin: 0 0 10px 0; font-size: 2.2em;">Expediente Clínico</h1>
            <p style="font-size: 20px; margin: 0 0 5px 0; color: #333;">Paciente: <strong><?= htmlspecialchars($paciente['nombre'] ?? '') ?> <?= htmlspecialchars($paciente['apellido'] ?? '') ?></strong></p>
            <p style="font-size: 15px; color: #6c757d; margin: 0;">📞 <?= htmlspecialchars($paciente['telefono'] ?? 'Sin teléfono') ?></p>
        </div>

        <!-- SECCIÓN 1: DATOS CLÍNICOS -->
        <h2 style="color: #2a9d8f; font-size: 1.5em;">1. Datos Clínicos Generales</h2>
        <form action="crud.php" method="POST">
            <input type="hidden" name="tipo" value="expediente">
            <input type="hidden" name="paciente_id" value="<?= $paciente_id ?>">
            
            <!-- AQUI MOVIMOS LA FECHA DE NACIMIENTO -->
            <div class="form-group" style="margin-bottom: 25px;">
                <label>Fecha de Nacimiento:</label>
                <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($paciente['fecha_nacimiento'] ?? '') ?>">
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Antecedentes Médicos:</label>
                    <textarea name="antecedentes" placeholder="Enfermedades previas, cirugías..."><?= htmlspecialchars($expediente['antecedentes'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Alergias:</label>
                    <textarea name="alergias" placeholder="Medicamentos, alimentos, látex..."><?= htmlspecialchars($expediente['alergias'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label>Observaciones / Evolución:</label>
                <textarea name="observaciones" style="min-height: 140px;" placeholder="Anotaciones generales del tratamiento..."><?= htmlspecialchars($expediente['observaciones'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn-save">Guardar Datos Clínicos</button>
        </form>

        <hr style="margin: 40px 0; border: 1px dashed #ccc;">

        <!-- SECCIÓN 2: ARCHIVOS ADJUNTOS -->
        <h2 style="color: #f4a261; font-size: 1.5em;">2. Archivos y Documentos Adjuntos</h2>
        
        <div class="upload-box">
            <form action="crud.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tipo" value="archivos_expediente">
                <input type="hidden" name="paciente_id" value="<?= $paciente_id ?>">
                
                <label style="font-weight: bold; display:block; margin-bottom: 15px; color: #0077b6; font-size: 16px;">
                    Subir nuevos archivos (Puedes seleccionar múltiples archivos a la vez)
                </label>
                <input type="file" name="archivos[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" required style="margin-bottom: 20px; font-size: 15px; border: none;">
                <br>
                <button type="submit" style="background: #e76f51; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold;">Subir Archivos</button>
            </form>
        </div>

        <div class="archivos-grid">
            <?php if($query_archivos->num_rows > 0): ?>
                <?php while($archivo = $query_archivos->fetch_assoc()): 
                    $ext = strtolower($archivo['tipo']);
                    if(in_array($ext, ['pdf'])) { $icon = '📄'; } 
                    elseif(in_array($ext, ['doc', 'docx'])) { $icon = '📝'; } 
                    else { $icon = '🖼️'; }
                ?>
                    <div class="archivo-card">
                        <div class="archivo-icon"><?= $icon ?></div>
                        <p title="<?= htmlspecialchars($archivo['nombre_archivo']) ?>"><?= (strlen($archivo['nombre_archivo']) > 15) ? substr(htmlspecialchars($archivo['nombre_archivo']), 0, 15) . '...' : htmlspecialchars($archivo['nombre_archivo']) ?></p>
                        <a href="<?= htmlspecialchars($archivo['ruta']) ?>" target="_blank">Abrir</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: #777; grid-column: 1 / -1; text-align: center; margin-top: 20px; font-style: italic;">Aún no hay documentos adjuntos.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>