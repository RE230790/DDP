<?php
@include 'conexion.php'; 
$mensaje_estado = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['consultar_estado'])) {
    $ticket_id = $_POST['ticket_id'];
    $telefono = $_POST['telefono'];

    if (isset($conn)) {
        $sql = "SELECT estado, fecha_deseada, hora_deseada FROM solicitudes_web WHERE id = ? AND telefono = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $ticket_id, $telefono);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $color = "#0077b6"; 
            if($row['estado'] == 'aprobada') $color = "#2a9d8f"; 
            if($row['estado'] == 'rechazada') $color = "#e63946"; 
            if($row['estado'] == 'pendiente') $color = "#f4a261"; 

            $mensaje_estado = "<div style='background-color: #f1f9fa; padding: 15px; border-left: 5px solid $color; margin-top: 15px; border-radius: 5px;'>
                                Tu solicitud para el <strong>{$row['fecha_deseada']}</strong> a las <strong>{$row['hora_deseada']}</strong> se encuentra: 
                                <span style='color: $color; font-weight: bold; text-transform: uppercase;'>{$row['estado']}</span>.
                               </div>";
        } else {
            $mensaje_estado = "<div style='color: #e63946; margin-top: 15px; font-weight: 500;'>No encontramos ninguna solicitud con ese ticket y teléfono. Verifica tus datos.</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OdontoWeb | Solicitar Cita</title>
    <!-- Aseguramos la carga del CSS más reciente -->
    <link rel="stylesheet" href="css/portal.css?v=4">
</head>
<body>

    <nav class="navbar">
        <div class="logo">
            <img src="logos/dental.png" alt="Logo OdontoWeb" style="height: 40px; margin-right: 10px;">
            <span style="font-weight: bold; color: #0077b6;">Dra. Patricia Eguizábal</span>
        </div>
        <div class="nav-links">
            <a href="#servicios">Servicios</a>
            <a href="#instalaciones">Instalaciones</a>
            <a href="#ubicacion">Ubicación</a>
            <a href="#faq">FAQ</a>
            <a href="#estado-cita">Estado de Cita</a>
            <a href="#agendar" class="btn-nav">Agendar Cita</a>
        </div>
    </nav>

    <header class="hero">
        <div class="hero-content">
            <h1>Tu sonrisa, nuestra prioridad</h1>
            <p>Atención odontológica de primera calidad. Solicita tu cita en línea.</p>
            <a href="#agendar" class="btn-main">Solicitar Consulta</a>
        </div>
    </header>

    <section id="servicios" class="section">
        <h2>Nuestros Servicios</h2>
        <div class="grid-container">
            <div class="card">
                <h3>Odontología General</h3>
                <p>Limpiezas, rellenos, extracciones y cuidado preventivo para mantener tu salud bucal impecable.</p>
            </div>
            <div class="card">
                <h3>Prostodoncia</h3>
                <p>Protesis fijas y removibles, protesis completas , coronas.</p>
            </div>
            <div class="card">
                <h3>Estética Dental</h3>
                <p>Blanqueamientos y carillas para que luzcas una sonrisa brillante y perfecta.</p>
            </div>
        </div>

        <!-- CASOS DE ÉXITO INTEGRADOS DENTRO DE SERVICIOS -->
        <h3 style="text-align: center; color: #0077b6; margin-top: 70px; font-size: 1.8em;">Casos de Éxito</h3>
        <p class="text-center">Descubre cómo hemos transformado las sonrisas de nuestros pacientes.</p>
        
        <div class="resultados-grid">
            
            <!-- Caso 1 -->
            <div class="resultado-card">
                <div class="resultado-imagenes">
                    <div class="img-wrapper">
                        <span class="etiqueta antes">Antes</span>
                        <img src="portal/Casos/antes1.jpeg" alt="Antes" onerror="this.src='https://images.unsplash.com/photo-1606811841689-23dfddce3e95?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'">
                    </div>
                    <div class="img-wrapper">
                        <span class="etiqueta despues">Después</span>
                        <img src="portal/Casos/despues1.jpeg" alt="Después" onerror="this.src='https://images.unsplash.com/photo-1609840114035-3c981b782dfe?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'">
                    </div>
                </div>
                <div class="resultado-info">
                    <h3 style="color: #0077b6; margin-bottom: 5px;">Prostodoncia</h3>
                    <p style="color: #666; font-size: 0.95em;">Rehabilitación oral completa.</p>
                </div>
            </div>

            <!-- Caso 2 -->
            <div class="resultado-card">
                <div class="resultado-imagenes">
                    <div class="img-wrapper">
                        <span class="etiqueta antes">Antes</span>
                        <img src="portal/Casos/antes2.jpeg" alt="Antes" onerror="this.src='https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'">
                    </div>
                    <div class="img-wrapper">
                        <span class="etiqueta despues">Después</span>
                        <img src="portal/Casos/despues2.jpeg" alt="Después" onerror="this.src='https://images.unsplash.com/photo-1606811841689-23dfddce3e95?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'">
                    </div>
                </div>
                <div class="resultado-info">
                    <h3 style="color: #0077b6; margin-bottom: 5px;">Blanqueamiento dental</h3>
                    <p style="color: #666; font-size: 0.95em;">Aclaramiento dental profesional.</p>
                </div>
            </div>

        </div>
    </section>

    <!-- GALERÍA DE INSTALACIONES -->
    <section id="instalaciones" class="section bg-light">
        <h2>Nuestras Instalaciones</h2>
        <p class="text-center">Conoce nuestra clínica. Un ambiente cómodo, moderno y equipado con tecnología de punta.</p>
        
        <div class="galeria-grid">
            <div class="galeria-item">
                <img src="portal/foto1.jpeg" alt="Recepción de la clínica" onerror="this.src='https://images.unsplash.com/photo-1606811841689-23dfddce3e95?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'">
            </div>
            <div class="galeria-item">
                <img src="portal/foto2.jpeg" alt="Consultorio dental" onerror="this.src='https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'">
            </div>
            <div class="galeria-item">
                <img src="portal/foto3.jpeg" alt="Equipo odontológico" onerror="this.src='https://images.unsplash.com/photo-1609840114035-3c981b782dfe?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'">
            </div>
        </div>
    </section>

    <!-- UBICACIÓN Y MAPA -->
    <section id="ubicacion" class="section">
        <h2>¿Dónde estamos?</h2>
        <p class="text-center">Visítanos en nuestra clínica en San Salvador. ¡Te esperamos con los brazos abiertos!</p>
        
        <div class="mapa-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3876.5971531124496!2d-89.21203922491098!3d13.682242586702415!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMTPCsDQwJzU2LjEiTiA4OcKwMTInMzQuMSJX!5e0!3m2!1sen!2ssv!4v1792863450313!5m2!1sen!2ssv" 
                width="100%" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </section>

    <section id="faq" class="section bg-light">
        <h2>Preguntas Frecuentes</h2>
        <div class="faq-container">
            <details class="faq-item">
                <summary>¿Cuáles son sus horarios de atención?</summary>
                <p>Atendemos de lunes a viernes de 8:00 a.m. a 5:00 p.m., y los sábados de 8:00 a.m. a 12:00 p.m.</p>
            </details>
            <details class="faq-item">
                <summary>¿Qué métodos de pago aceptan?</summary>
                <p>Aceptamos pagos en efectivo y transferencias bancarias.</p>
            </details>
            <details class="faq-item">
                <summary>¿Cómo funciona la solicitud de citas por internet?</summary>
                <p>Al llenar el formulario, tu solicitud queda en estado "Pendiente". Nuestro equipo la revisará y la aprobará según disponibilidad. Te daremos un número de ticket para que consultes si ya fue confirmada.</p>
            </details>
            <details class="faq-item">
                <summary>¿Cuáles son sus canales de atención telefónica?</summary>
                <p>Para consultas generales o atención personalizada, por favor comuníquese al teléfono 2513-3309. En nuestro horario de atención.</p>
            </details>
        </div>
    </section>

    <section id="estado-cita" class="section">
        <h2>Consultar Estado de tu Cita</h2>
        <div class="form-container" style="background: #f8fbfa; border: 2px dashed #00B4D8;">
            <form action="portal.php#estado-cita" method="POST">
                <input type="hidden" name="consultar_estado" value="1">
                <div class="form-group"><label>Ticket ID:</label><input type="number" name="ticket_id" required></div>
                
                <div class="form-group">
                    <label>Teléfono:</label>
                    <input type="tel" name="telefono" placeholder=" " pattern="[0-9]{4}-[0-9]{4}" maxlength="9" oninput="formatearTelefono(this)" required>
                </div>
                
                <button type="submit" class="btn-main w-100" style="background: #2a9d8f;">Consultar</button>
            </form>
            <?php if($mensaje_estado != null) echo $mensaje_estado; ?>
        </div>
    </section>

    <!-- FORMULARIO INTELIGENTE DE CITA -->
    <section id="agendar" class="section bg-light">
        <h2>Solicitar Cita en Línea</h2>
        <div class="form-container">
            <form action="guardar_solicitud.php" method="POST">
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;"><label>Nombre(s):</label><input type="text" name="nombre" required></div>
                    <div class="form-group" style="flex: 1;"><label>Apellido(s):</label><input type="text" name="apellido" required></div>
                </div>
                
                <div class="form-group">
                    <label>Teléfono:</label>
                    <input type="tel" name="telefono" placeholder=" " pattern="[0-9]{4}-[0-9]{4}" maxlength="9" oninput="formatearTelefono(this)" title="Debe tener 8 números (Ej. 7777-8888)" required>
                </div>
                <div class="form-group">
                    <label>Correo (Opcional):</label>
                    <input type="email" name="correo" placeholder=" " pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$" title="Ingresa un correo válido. Ej: nombre@gmail.com">
                </div>
                
                <hr style="margin: 20px 0; border: 0; border-top: 1px dashed #ccc;">

                <div class="form-group">
                    <label>Tratamiento Principal:</label>
                    <select id="servicio_select" name="servicio_crudo" required style="width: 100%; padding: 12px; border-radius: 8px;">
                        <option value="">-- Selecciona el tipo de consulta --</option>
                        <option value="30|Consulta General / Revisión">Consulta General / Revisión (30 min)</option>
                        <option value="45|Limpieza Dental">Limpieza Dental (45 min)</option>
                        <option value="60|Extracción Dental">Extracción Dental (1 hora)</option>
                        <option value="90|Blanqueamiento">Blanqueamiento Estético (1.5 horas)</option>
                        <option value="45|Ortodoncia (Ajuste)">Ortodoncia Ajuste (45 min)</option>
                        <option value="60|Relleno / Resina">Relleno / Resina (1 hora)</option>
                        <option value="30|Otro (Evalucación)">Otro Tratamiento (Evaluación 30 min)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Fecha de preferencia:</label>
                    <input type="date" id="fecha_select" name="fecha" required min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label>Horarios Disponibles:</label>
                    <div id="horarios-disponibles" class="horarios-grid">
                        <p style="color: #666; font-size: 14px;">Selecciona un tratamiento y una fecha primero para ver los espacios libres.</p>
                    </div>
                </div>

                <input type="hidden" name="hora" id="hora_seleccionada" required>
                <input type="hidden" name="duracion" id="duracion_seleccionada">
                <input type="hidden" name="servicio_nombre" id="servicio_nombre">

                <div class="form-group">
                    <label>Detalles adicionales (Opcional):</label>
                    <textarea name="detalles" rows="2" placeholder=" "></textarea>
                </div>
                
                <button type="submit" class="btn-main w-100" id="btn_enviar">Agendar Cita</button>
            </form>
        </div>
    </section>

    <script>
        function formatearTelefono(input) {
            let valor = input.value.replace(/\D/g, ''); 
            if (valor.length > 4) {
                valor = valor.substring(0, 4) + '-' + valor.substring(4, 8);
            }
            input.value = valor;
        }

        document.getElementById('fecha_select').addEventListener('change', cargarHorarios);
        document.getElementById('servicio_select').addEventListener('change', cargarHorarios);

        function cargarHorarios() {
            let fecha = document.getElementById('fecha_select').value;
            let servicioCrudo = document.getElementById('servicio_select').value;
            let contenedor = document.getElementById('horarios-disponibles');
            let inputHora = document.getElementById('hora_seleccionada');
            
            inputHora.value = "";

            if(fecha && servicioCrudo) {
                let datosServicio = servicioCrudo.split('|');
                let duracion = datosServicio[0];
                
                document.getElementById('duracion_seleccionada').value = duracion;
                document.getElementById('servicio_nombre').value = datosServicio[1];
                
                contenedor.innerHTML = "<p>⏳ Buscando espacios en la agenda...</p>";
                
                fetch(`obtener_horarios.php?fecha=${fecha}&duracion=${duracion}`)
                .then(res => res.json())
                .then(data => {
                    if(data.error) {
                        contenedor.innerHTML = `<p style="color:#e63946; font-weight:bold;">${data.error}</p>`;
                        return;
                    }
                    
                    contenedor.innerHTML = "";
                    if(data.horarios.length === 0) {
                        contenedor.innerHTML = "<p style='color:#e63946; font-weight:bold;'>El día está lleno o no hay un bloque suficientemente largo para este tratamiento. Selecciona otro día.</p>";
                    } else {
                        data.horarios.forEach(h => {
                            let btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'btn-horario';
                            btn.textContent = h.mostrar;
                            btn.onclick = function() {
                                document.querySelectorAll('.btn-horario').forEach(b => b.classList.remove('selected'));
                                this.classList.add('selected');
                                inputHora.value = h.valor;
                            };
                            contenedor.appendChild(btn);
                        });
                    }
                }).catch(() => {
                    contenedor.innerHTML = "<p style='color:red;'>Error de conexión.</p>";
                });
            }
        }
        
        document.getElementById('btn_enviar').addEventListener('click', function(e) {
            if(!document.getElementById('hora_seleccionada').value) {
                e.preventDefault();
                alert("Por favor, haz clic en un botón de hora verde en los Horarios Disponibles antes de enviar.");
            }
        });
    </script>
</body>
</html>