<?php
include 'conexion.php';
header('Content-Type: application/json');

if(isset($_GET['fecha']) && isset($_GET['duracion'])) {
    $fecha = $_GET['fecha'];
    $duracion_requerida = (int)$_GET['duracion']; // en minutos
    
    // Si es domingo, no hay citas
    $dia_semana = date('N', strtotime($fecha)); // 1 (Lun) a 7 (Dom)
    if ($dia_semana == 7) {
        echo json_encode(['error' => 'No atendemos los domingos. Por favor selecciona otro día.']); 
        exit;
    }
    
    // Si es sábado cerramos a las 12, entre semana a las 5pm
    $hora_fin_dia = ($dia_semana == 6) ? "12:00:00" : "17:00:00";
    
    $ocupados = [];
    
    // 1. Buscamos citas ya confirmadas en el CRUD
    $res1 = $conn->query("SELECT hora, duracion_minutos FROM citas WHERE fecha = '$fecha' AND estado IN ('confirmada', 'completada')");
    if($res1) { while($r = $res1->fetch_assoc()) { $ocupados[] = $r; } }
    
    // 2. Buscamos solicitudes web que alguien más ya pidió y están pendientes de aprobar
    $res2 = $conn->query("SELECT hora_deseada as hora, duracion_minutos FROM solicitudes_web WHERE fecha_deseada = '$fecha' AND estado IN ('pendiente', 'aprobada')");
    if($res2) { while($r = $res2->fetch_assoc()) { $ocupados[] = $r; } }

    $horarios_disponibles = [];
    $actual = strtotime("$fecha 08:00:00");
    $fin_dia = strtotime("$fecha $hora_fin_dia");
    
    // Evaluamos cada bloque de 30 minutos
    while ($actual + ($duracion_requerida * 60) <= $fin_dia) {
        $hora_fin_propuesta = $actual + ($duracion_requerida * 60);
        $choque = false;
        
        foreach($ocupados as $oc) {
            $oc_inicio = strtotime("$fecha " . $oc['hora']);
            $oc_fin = $oc_inicio + ($oc['duracion_minutos'] * 60);
            
            // Fórmula para detectar cruce de horarios
            if ($actual < $oc_fin && $hora_fin_propuesta > $oc_inicio) {
                $choque = true;
                break;
            }
        }
        
        // Si no chocó con ninguna cita, agregamos el botón
        if (!$choque) {
            $horarios_disponibles[] = [
                'mostrar' => date('h:i A', $actual),
                'valor' => date('H:i:s', $actual)
            ];
        }
        
        $actual += (30 * 60); // Avanzamos 30 mins para evaluar el siguiente hueco
    }
    
    echo json_encode(['horarios' => $horarios_disponibles]);
}
?>