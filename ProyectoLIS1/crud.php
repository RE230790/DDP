<?php

include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'];

    if ($tipo == 'paciente') {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $telefono = $_POST['telefono'];
        $correo = $_POST['correo'];

        if (empty($id)) {
            $sql = "INSERT INTO pacientes (nombre, apellido, telefono, correo) 
                    VALUES ('$nombre', '$apellido', '$telefono', '$correo')";
        } else {
            $sql = "UPDATE pacientes 
                    SET nombre='$nombre', apellido='$apellido', telefono='$telefono', correo='$correo' 
                    WHERE id=$id";
        }
        if ($conn->query($sql) === TRUE) {
            header("Location: indexcrud.php");  
            exit(); 
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

    } elseif ($tipo == 'cita') {
        $id = $_POST['id'];
        $paciente_id = $_POST['paciente_id'];
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        $motivo = $_POST['motivo'];
        $estado = $_POST['estado'];

        if (empty($id)) {
            $sql = "INSERT INTO citas (paciente_id, fecha, hora, motivo, estado) 
                    VALUES ($paciente_id, '$fecha', '$hora', '$motivo', '$estado')";
        } else {
            $sql = "UPDATE citas 
                    SET paciente_id=$paciente_id, fecha='$fecha', hora='$hora', motivo='$motivo', estado='$estado' 
                    WHERE id=$id";
        }
        if ($conn->query($sql) === TRUE) {
            header("Location: indexcrud.php");  
            exit(); 
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

    } elseif ($tipo == 'expediente') {
        $paciente_id = $_POST['paciente_id'];
        $fecha_nacimiento = $_POST['fecha_nacimiento']; 
        $antecedentes = $_POST['antecedentes'];
        $alergias = $_POST['alergias'];
        $observaciones = $_POST['observaciones'];

        if (!empty($fecha_nacimiento)) {
            $conn->query("UPDATE pacientes SET fecha_nacimiento='$fecha_nacimiento' WHERE id=$paciente_id");
        } else {
            $conn->query("UPDATE pacientes SET fecha_nacimiento=NULL WHERE id=$paciente_id");
        }

        $check_expediente = $conn->query("SELECT id FROM expedientes WHERE paciente_id = $paciente_id");
        if ($check_expediente->num_rows > 0) {
            $sql = "UPDATE expedientes 
                    SET antecedentes='$antecedentes', alergias='$alergias', observaciones='$observaciones', 
                    ultima_actualizacion=NOW() 
                    WHERE paciente_id=$paciente_id";
        } else {
            $sql = "INSERT INTO expedientes (paciente_id, antecedentes, alergias, observaciones) 
                    VALUES ($paciente_id, '$antecedentes', '$alergias', '$observaciones')";
        }
        
        if ($conn->query($sql) === TRUE) {
            header("Location: expediente.php?paciente_id=$paciente_id"); 
            exit(); 
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

    } elseif ($tipo == 'archivos_expediente') {
        $paciente_id = $_POST['paciente_id'];

        if (!empty($_FILES['archivos']['name'][0])) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

            $count = count($_FILES['archivos']['name']);
            for ($i = 0; $i < $count; $i++) {
                $filename = basename($_FILES['archivos']['name'][$i]);
                $clean_filename = preg_replace("/[^a-zA-Z0-9.-]/", "_", $filename); 
                $target_file = $target_dir . time() . "_" . $clean_filename;
                $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $allowed_types = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];

                if (in_array($fileType, $allowed_types)) {
                    if (move_uploaded_file($_FILES['archivos']['tmp_name'][$i], $target_file)) {
                        $sql = "INSERT INTO archivos_expediente (paciente_id, nombre_archivo, ruta, tipo) 
                                VALUES ($paciente_id, '$filename', '$target_file', '$fileType')";
                        $conn->query($sql);
                    }
                }
            }
        }
        header("Location: expediente.php?paciente_id=$paciente_id");
        exit();

    } elseif ($tipo == 'tratamiento') {
        $id = $_POST['id'];
        $cita_id = $_POST['cita_id'];
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];

        if (empty($id)) {
            $sql = "INSERT INTO tratamientos (cita_id, nombre, descripcion) VALUES ($cita_id, '$nombre', '$descripcion')";
        } else {
            $sql = "UPDATE tratamientos SET cita_id=$cita_id, nombre='$nombre', descripcion='$descripcion' WHERE id=$id";
        }
        if ($conn->query($sql) === TRUE) { header("Location: indexcrud.php"); exit(); }
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    if (isset($_GET['delete_id']) && isset($_GET['tipo'])) {
        $id = $_GET['delete_id'];
        $tipo = $_GET['tipo'];
        if ($tipo == 'paciente') { $sql = "DELETE FROM pacientes WHERE id=$id"; } 
        elseif ($tipo == 'cita') { $sql = "DELETE FROM citas WHERE id=$id"; } 
        elseif ($tipo == 'tratamiento') { $sql = "DELETE FROM tratamientos WHERE id=$id"; }
        if ($conn->query($sql) === TRUE) { header("Location: indexcrud.php"); exit(); }
    }
    
    elseif (isset($_GET['estado_solicitud']) && isset($_GET['id_solicitud'])) {
        $nuevo_estado = $_GET['estado_solicitud'];
        $solicitud_id = $_GET['id_solicitud'];
        
        if ($nuevo_estado === 'aprobada') {
            $result_sol = $conn->query("SELECT * FROM solicitudes_web WHERE id = $solicitud_id");
            if ($result_sol->num_rows > 0) {
                $sol = $result_sol->fetch_assoc();
                $nombre = $sol['nombre']; 
                $apellido = $sol['apellido']; 
                $telefono = $sol['telefono']; 
                $correo = $sol['correo'];
                $fecha = $sol['fecha_deseada']; 
                $hora = $sol['hora_deseada']; 
                $duracion = $sol['duracion_minutos']; 
                $motivo = $sol['motivo'];

                // 1. Verificar paciente
                $check_paciente = $conn->query("SELECT id FROM pacientes WHERE telefono = '$telefono'");
                $paciente_id = null;

                if ($check_paciente->num_rows > 0) {
                    $row_paciente = $check_paciente->fetch_assoc(); 
                    $paciente_id = $row_paciente['id'];
                } else {
                    $conn->query("INSERT INTO pacientes (nombre, apellido, telefono, correo) VALUES ('$nombre', '$apellido', '$telefono', '$correo')");
                    $paciente_id = $conn->insert_id;
                }
                
                // 2. Crear cita
                $conn->query("INSERT INTO citas (paciente_id, fecha, hora, duracion_minutos, motivo, estado) VALUES ($paciente_id, '$fecha', '$hora', $duracion, '$motivo', 'confirmada')");
                $cita_id = $conn->insert_id; // Obtenemos el ID de la nueva cita

                // 3. AUTO-CREAR EL TRATAMIENTO
                // Extraemos el nombre del tratamiento del texto enviado por el formulario
                $nombre_tratamiento = "Tratamiento Dental";
                if (preg_match('/Tratamiento: (.*?)\n/', $motivo, $coincidencias)) {
                    $nombre_tratamiento = trim($coincidencias[1]);
                }
                
                $descripcion_inicial = "Tratamiento programado desde la web. (Pendiente de realizar)";
                $conn->query("INSERT INTO tratamientos (cita_id, nombre, descripcion) VALUES ($cita_id, '$nombre_tratamiento', '$descripcion_inicial')");
            }
        }
        
        if ($nuevo_estado === 'aprobada' || $nuevo_estado === 'rechazada') {
            $sql = "UPDATE solicitudes_web SET estado = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $nuevo_estado, $solicitud_id);
            $stmt->execute();
            $stmt->close();
        }
        header("Location: solicitudes.php");
        exit();
    }
}

$conn->close();
?>