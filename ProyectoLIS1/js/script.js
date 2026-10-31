// FUNCIÓN PARA AUTO-FORMATEAR EL TELÉFONO EN EL CRUD
function formatearTelefono(input) {
    let valor = input.value.replace(/\D/g, ''); // Borra todo lo que no sea número
    if (valor.length > 4) {
        valor = valor.substring(0, 4) + '-' + valor.substring(4, 8);
    }
    input.value = valor;
}

// PACIENTES
const formularioPaciente = document.getElementById('formulario-paciente');
const pacienteForm = document.getElementById('paciente-form');
const agregarPacienteBtn = document.getElementById('agregar-paciente');
const editarPacienteBtn = document.getElementById('editar-paciente');
const eliminarPacienteBtn = document.getElementById('eliminar-paciente');
const verExpedienteBtn = document.getElementById('ver-expediente');
let pacientesTable = document.getElementById('pacientes-table');
let pacienteSeleccionado = null;
const cancelarFormularioPacienteBtn = document.getElementById('cancelar-formulario-paciente');

// CITAS
const formularioCita = document.getElementById('formulario-cita');
const citaForm = document.getElementById('cita-form');
const agregarCitaBtn = document.getElementById('agregar-cita');
const editarCitaBtn = document.getElementById('editar-cita');
const eliminarCitaBtn = document.getElementById('eliminar-cita');
let citasTable = document.getElementById('citas-table');
let citaSeleccionada = null;
const cancelarFormularioCitaBtn = document.getElementById('cancelar-formulario-cita');

// TRATAMIENTOS
const formularioTratamiento = document.getElementById('formulario-tratamiento');
const tratamientoForm = document.getElementById('tratamiento-form');
const agregarTratamientoBtn = document.getElementById('agregar-tratamiento');
const editarTratamientoBtn = document.getElementById('editar-tratamiento');
const eliminarTratamientoBtn = document.getElementById('eliminar-tratamiento');
let tratamientosTable = document.getElementById('tratamientos-table');
let tratamientoSeleccionado = null;
const cancelarFormularioTratamientoBtn = document.getElementById('cancelar-formulario-tratamiento');

// ==================== PACIENTES ====================

if(agregarPacienteBtn) {
    agregarPacienteBtn.addEventListener('click', () => {
        formularioPaciente.style.display = 'block';
        pacienteForm.reset();
        document.getElementById('paciente-id').value = '';
        formularioPaciente.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
}

if(editarPacienteBtn) {
    editarPacienteBtn.addEventListener('click', () => {
        if (pacienteSeleccionado) {
            formularioPaciente.style.display = 'block';
            document.getElementById('paciente-id').value = pacienteSeleccionado.id;
            
            // Asignación ultra-precisa a los inputs del formulario
            document.querySelector('#paciente-form input[name="nombre"]').value = pacienteSeleccionado.nombre;
            document.querySelector('#paciente-form input[name="apellido"]').value = pacienteSeleccionado.apellido;
            document.querySelector('#paciente-form input[name="telefono"]').value = pacienteSeleccionado.telefono;
            document.querySelector('#paciente-form input[name="correo"]').value = pacienteSeleccionado.correo;
            
            formularioPaciente.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            alert("Por favor, selecciona un paciente en la tabla primero.");
        }
    });
}

if (cancelarFormularioPacienteBtn) {
    cancelarFormularioPacienteBtn.addEventListener('click', () => {
        formularioPaciente.style.display = 'none';
    });
}

if(eliminarPacienteBtn) {
    eliminarPacienteBtn.addEventListener('click', () => {
        if (pacienteSeleccionado) {
            if(confirm(`¿Estás seguro de eliminar al paciente ${pacienteSeleccionado.nombre}?`)) {
                window.location.href = `crud.php?delete_id=${pacienteSeleccionado.id}&tipo=paciente`;
            }
        } else {
            alert("Por favor, selecciona un paciente en la tabla para eliminar.");
        }
    });
}

if(verExpedienteBtn) {
    verExpedienteBtn.addEventListener('click', () => {
        if (pacienteSeleccionado) {
            window.location.href = `expediente.php?paciente_id=${pacienteSeleccionado.id}`;
        } else {
            alert("Por favor, selecciona un paciente en la tabla para ver su expediente.");
        }
    });
}

if(pacientesTable) {
    pacientesTable.addEventListener('click', (event) => {
        if (event.target.tagName === 'TD') {
            const row = event.target.parentNode;
            const filas = pacientesTable.querySelectorAll('tbody tr');
            filas.forEach(f => f.classList.remove('fila-seleccionada'));
            row.classList.add('fila-seleccionada');

            const cells = row.getElementsByTagName('TD');
            pacienteSeleccionado = {
                id: cells[0].textContent.trim(),
                nombre: cells[1].textContent.trim(),
                apellido: cells[2].textContent.trim(),
                telefono: cells[3].textContent.trim(),
                correo: cells[4].textContent.trim()
            };
        }
    });
}

// ==================== CITAS ====================

if(agregarCitaBtn) {
    agregarCitaBtn.addEventListener('click', () => {
        formularioCita.style.display = 'block';
        citaForm.reset();
        document.getElementById('cita-id').value = '';
        formularioCita.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
}

if(editarCitaBtn) {
    editarCitaBtn.addEventListener('click', () => {
        if (citaSeleccionada) {
            formularioCita.style.display = 'block';
            document.getElementById('cita-id').value = citaSeleccionada.id;
            
            // Lógica inteligente para buscar el nombre en la lista desplegable y autoseleccionarlo
            let selectPaciente = document.querySelector('#cita-form select[name="paciente_id"]');
            for(let i = 0; i < selectPaciente.options.length; i++) {
                if(selectPaciente.options[i].text === citaSeleccionada.paciente_nombre) {
                    selectPaciente.selectedIndex = i;
                    break;
                }
            }

            document.querySelector('#cita-form input[name="fecha"]').value = citaSeleccionada.fecha;
            document.querySelector('#cita-form input[name="hora"]').value = citaSeleccionada.hora;
            document.querySelector('#cita-form textarea[name="motivo"]').value = citaSeleccionada.motivo;
            document.querySelector('#cita-form select[name="estado"]').value = citaSeleccionada.estado;
            
            formularioCita.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            alert("Por favor, selecciona una cita en la tabla primero.");
        }
    });
}

if (cancelarFormularioCitaBtn) {
    cancelarFormularioCitaBtn.addEventListener('click', () => {
        formularioCita.style.display = 'none';
    });
}

if(eliminarCitaBtn) {
    eliminarCitaBtn.addEventListener('click', () => {
        if (citaSeleccionada) {
            if(confirm(`¿Estás seguro de eliminar esta cita?`)) {
                window.location.href = `crud.php?delete_id=${citaSeleccionada.id}&tipo=cita`;
            }
        } else {
            alert("Por favor, selecciona una cita en la tabla para eliminar.");
        }
    });
}

if(citasTable) {
    citasTable.addEventListener('click', (event) => {
        if (event.target.tagName === 'TD') {
            const row = event.target.parentNode;
            const filas = citasTable.querySelectorAll('tbody tr');
            filas.forEach(f => f.classList.remove('fila-seleccionada'));
            row.classList.add('fila-seleccionada');

            const cells = row.getElementsByTagName('TD');
            citaSeleccionada = {
                id: cells[0].textContent.trim(),
                paciente_nombre: cells[1].textContent.trim(),
                fecha: cells[2].textContent.trim(),
                hora: cells[3].textContent.trim(),
                motivo: cells[4].textContent.trim(),
                estado: cells[5].textContent.trim().toLowerCase() // Adaptado a minúsculas para igualar la opción
            };
        }
    });
}

// ==================== TRATAMIENTOS ====================

if(agregarTratamientoBtn) {
    agregarTratamientoBtn.addEventListener('click', () => {
        formularioTratamiento.style.display = 'block';
        tratamientoForm.reset();
        document.getElementById('tratamiento-id').value = '';
        formularioTratamiento.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
}

if(editarTratamientoBtn) {
    editarTratamientoBtn.addEventListener('click', () => {
        if (tratamientoSeleccionado) {
            formularioTratamiento.style.display = 'block';
            document.getElementById('tratamiento-id').value = tratamientoSeleccionado.id;
            
            document.querySelector('#tratamiento-form select[name="cita_id"]').value = tratamientoSeleccionado.cita_id;
            document.querySelector('#tratamiento-form input[name="nombre"]').value = tratamientoSeleccionado.nombre;
            document.querySelector('#tratamiento-form textarea[name="descripcion"]').value = tratamientoSeleccionado.descripcion;
            
            formularioTratamiento.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            alert("Por favor, selecciona un tratamiento en la tabla primero.");
        }
    });
}

if (cancelarFormularioTratamientoBtn) {
    cancelarFormularioTratamientoBtn.addEventListener('click', () => {
        formularioTratamiento.style.display = 'none';
    });
}

if(eliminarTratamientoBtn) {
    eliminarTratamientoBtn.addEventListener('click', () => {
        if (tratamientoSeleccionado) {
            if(confirm(`¿Estás seguro de eliminar este tratamiento?`)) {
                window.location.href = `crud.php?delete_id=${tratamientoSeleccionado.id}&tipo=tratamiento`;
            }
        } else {
            alert("Por favor, selecciona un tratamiento en la tabla para eliminar.");
        }
    });
}

if(tratamientosTable) {
    tratamientosTable.addEventListener('click', (event) => {
        if (event.target.tagName === 'TD') {
            const row = event.target.parentNode;
            const filas = tratamientosTable.querySelectorAll('tbody tr');
            filas.forEach(f => f.classList.remove('fila-seleccionada'));
            row.classList.add('fila-seleccionada');

            const cells = row.getElementsByTagName('TD');
            tratamientoSeleccionado = {
                id: cells[0].textContent.trim(),
                cita_id: cells[1].textContent.trim(),
                nombre: cells[5].textContent.trim(),
                descripcion: cells[6].textContent.trim()
            };
        }
    });
}