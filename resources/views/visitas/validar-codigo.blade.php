
{{-- resources/views/visitas/validar-codigo.blade.php --}}
@extends('layouts.ap')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-key text-primary"></i> Validar Código de Visitante
                </h2>
                <div class="d-flex gap-2">
                    @can('operar porteria')
                        <a href="{{ route('visitas.panel-guardia') }}" class="btn btn-outline-primary">
                            <i class="fas fa-shield-alt"></i> Panel Guardia
                        </a>
                    @endcan
                    <a href="{{ route('visitas.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list"></i> Todas las Visitas
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Alerta de acceso según permisos --}}
            @can('operar porteria')
                <div class="alert alert-info border-info">
                    <i class="fas fa-shield-alt"></i>
                    <strong>Acceso de Portería:</strong> Tienes permisos para validar códigos y registrar entradas/salidas.
                </div>
            @else
                <div class="alert alert-warning border-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Acceso Limitado:</strong> Solo puedes validar códigos. No puedes registrar entradas.
                </div>
            @endcan

            {{-- Formulario para validar código --}}
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-center">
                        <i class="fas fa-user-check"></i> Ingrese los Datos del Visitante
                    </h5>
                </div>
                <div class="card-body">
                    <form id="validarCodigoForm">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="codigo" class="form-label fw-bold">
                                        <i class="fas fa-hashtag text-primary"></i> Código de Visita (6 dígitos)
                                    </label>
                                    <input type="text"
                                           id="codigo"
                                           name="codigo"
                                           class="form-control form-control-lg text-center"
                                           placeholder="123456"
                                           maxlength="6"
                                           style="font-size: 2rem; letter-spacing: 0.5rem; background: #f8f9fa;"
                                           pattern="[0-9]{6}"
                                           required>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle"></i> Código proporcionado por el residente
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="ci_visitante" class="form-label fw-bold">
                                        <i class="fas fa-id-card text-info"></i> Cédula de Identidad del Visitante
                                    </label>
                                    <input type="text"
                                           id="ci_visitante"
                                           name="ci_visitante"
                                           class="form-control form-control-lg"
                                           placeholder="12345678"
                                           required>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle"></i> CI que debe coincidir con el registro
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <button type="submit" class="btn btn-primary btn-lg me-md-2" id="btnValidar">
                                <i class="fas fa-search"></i> Validar Código
                            </button>
                            <button type="button" class="btn btn-secondary btn-lg" onclick="limpiarFormulario()">
                                <i class="fas fa-eraser"></i> Limpiar
                            </button>
                        </div>
                    </form>

                    {{-- Resultado de la validación --}}
                    <div id="resultadoValidacion" class="mt-4" style="display: none;">
                        <div class="alert alert-success border-success">
                            <h5 class="alert-heading">
                                <i class="fas fa-user-check text-success"></i> Visitante Validado
                            </h5>
                            <div id="datosVisitante" class="mb-3"></div>
                            <hr>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                @can('operar porteria')
                                    <button id="btnRegistrarEntrada" class="btn btn-success btn-lg me-md-2">
                                        <i class="fas fa-sign-in-alt"></i> Registrar Entrada
                                    </button>
                                @else
                                    <div class="alert alert-warning mb-2">
                                        <i class="fas fa-lock"></i> 
                                        <strong>Sin permisos:</strong> No puedes registrar entradas. 
                                        Contacta al personal de portería.
                                    </div>
                                @endcan
                                <button onclick="limpiarFormulario()" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Mensaje de error --}}
                    <div id="errorValidacion" class="mt-4" style="display: none;">
                        <div class="alert alert-danger border-danger">
                            <h5 class="alert-heading">
                                <i class="fas fa-exclamation-triangle text-danger"></i> Error de Validación
                            </h5>
                            <p id="mensajeError" class="mb-3"></p>
                            <button onclick="limpiarFormulario()" class="btn btn-outline-danger">
                                <i class="fas fa-redo"></i> Intentar Nuevamente
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel de información --}}
            <div class="row mt-4 g-4">
                <div class="col-md-4">
                    <div class="card border-info h-100">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-clipboard-list"></i> Instrucciones
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0">
                                <li><i class="fas fa-check text-success"></i> Solicite el código de 6 dígitos al visitante</li>
                                <li><i class="fas fa-check text-success"></i> Verifique la cédula de identidad</li>
                                <li><i class="fas fa-check text-success"></i> Los datos deben coincidir exactamente</li>
                                <li><i class="fas fa-check text-success"></i> Valide que esté dentro del horario autorizado</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card border-warning h-100">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0">
                                <i class="fas fa-link"></i> Accesos Rápidos
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @can('operar porteria')
                                    <a href="{{ route('visitas.panel-guardia') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-shield-alt"></i> Panel de Guardia
                                    </a>
                                @endcan
                                <a href="{{ route('visitas.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-list"></i> Listado de Visitas
                                </a>
                                @can('gestionar visitas')
                                    <a href="{{ route('visitas.create') }}" class="btn btn-outline-success">
                                        <i class="fas fa-plus"></i> Nueva Visita
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-success h-100">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-clock"></i> Horarios de Tolerancia
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0">
                                <li><i class="fas fa-clock text-primary"></i> <strong>Entrada:</strong> 30 min antes del horario</li>
                                <li><i class="fas fa-clock text-info"></i> <strong>Salida:</strong> Hasta fin del horario programado</li>
                                <li><i class="fas fa-exclamation-circle text-warning"></i> <strong>Fuera de horario:</strong> Validar con residente</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Estadísticas del día (solo para portería) --}}
            @can('operar porteria')
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card border-secondary">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-chart-bar"></i> Resumen del Día - {{ now()->format('d/m/Y') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center g-3">
                                    <div class="col-md-3">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body py-3">
                                                <i class="fas fa-users fa-2x mb-2"></i>
                                                <h4 class="mb-1">
                                                    {{ \App\Models\Visita::where('estado', 'en_curso')->count() }}
                                                </h4>
                                                <small>Dentro Ahora</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-warning text-dark">
                                            <div class="card-body py-3">
                                                <i class="fas fa-clock fa-2x mb-2"></i>
                                                <h4 class="mb-1">
                                                    {{ \App\Models\Visita::where('estado', 'pendiente')
                                                        ->whereBetween('fecha_inicio', [now(), now()->addHours(2)])
                                                        ->count() }}
                                                </h4>
                                                <small>Próximas 2h</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-success text-white">
                                            <div class="card-body py-3">
                                                <i class="fas fa-sign-in-alt fa-2x mb-2"></i>
                                                <h4 class="mb-1">
                                                    {{ \App\Models\Visita::whereDate('hora_entrada', today())->count() }}
                                                </h4>
                                                <small>Entradas Hoy</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-info text-white">
                                            <div class="card-body py-3">
                                                <i class="fas fa-sign-out-alt fa-2x mb-2"></i>
                                                <h4 class="mb-1">
                                                    {{ \App\Models\Visita::whereDate('hora_salida', today())->count() }}
                                                </h4>
                                                <small>Salidas Hoy</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
</div>

<script>
document.getElementById('validarCodigoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const codigo = document.getElementById('codigo').value;
    const ci = document.getElementById('ci_visitante').value;
    const btnValidar = document.getElementById('btnValidar');
    
    // Validaciones del lado cliente
    if (codigo.length !== 6) {
        mostrarError('El código debe tener exactamente 6 dígitos');
        return;
    }
    
    if (!ci.trim()) {
        mostrarError('Debe ingresar la cédula de identidad');
        return;
    }
    
    // Mostrar loading
    btnValidar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Validando...';
    btnValidar.disabled = true;
    
    // Ocultar resultados anteriores
    document.getElementById('resultadoValidacion').style.display = 'none';
    document.getElementById('errorValidacion').style.display = 'none';
    
    fetch('{{ route("visitas.validar-codigo") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            codigo: codigo,
            ci_visitante: ci
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarDatosVisitante(data.visita);
            showNotification('Código validado correctamente', 'success');
        } else {
            mostrarError(data.message);
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarError('Error de conexión. Intente nuevamente.');
        showNotification('Error de conexión', 'error');
    })
    .finally(() => {
        // Restaurar botón
        btnValidar.innerHTML = '<i class="fas fa-search"></i> Validar Código';
        btnValidar.disabled = false;
    });
});

function mostrarDatosVisitante(visita) {
    const datosDiv = document.getElementById('datosVisitante');
    datosDiv.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body py-2">
                        <p class="mb-1"><i class="fas fa-user text-primary"></i> <strong>Nombre:</strong> ${visita.nombre_visitante}</p>
                        <p class="mb-1"><i class="fas fa-id-card text-info"></i> <strong>CI:</strong> ${visita.ci_visitante}</p>
                        <p class="mb-0"><i class="fas fa-comment text-warning"></i> <strong>Motivo:</strong> ${visita.motivo}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body py-2">
                        <p class="mb-1"><i class="fas fa-home text-success"></i> <strong>Residente:</strong> ${visita.residente}</p>
                        ${visita.placa_vehiculo ? `<p class="mb-1"><i class="fas fa-car text-info"></i> <strong>Vehículo:</strong> ${visita.placa_vehiculo}</p>` : ''}
                        <p class="mb-0"><i class="fas fa-key text-primary"></i> <strong>Código:</strong> <span class="fw-bold text-primary">${document.getElementById('codigo').value}</span></p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('resultadoValidacion').style.display = 'block';
    
    // Configurar botón de registrar entrada (solo si tiene permisos)
    const btnEntrada = document.getElementById('btnRegistrarEntrada');
    if (btnEntrada) {
        btnEntrada.onclick = function() {
            if (confirm(`¿Registrar entrada de ${visita.nombre_visitante}?`)) {
                // Mostrar loading en el botón
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registrando...';
                this.disabled = true;
                
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("visitas.entrada", ":id") }}'.replace(':id', visita.id);
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        };
    }
}

function mostrarError(mensaje) {
    document.getElementById('mensajeError').textContent = mensaje;
    document.getElementById('errorValidacion').style.display = 'block';
}

function limpiarFormulario() {
    document.getElementById('codigo').value = '';
    document.getElementById('ci_visitante').value = '';
    document.getElementById('resultadoValidacion').style.display = 'none';
    document.getElementById('errorValidacion').style.display = 'none';
    document.getElementById('codigo').focus();
}

function showNotification(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Auto-focus en el campo código al cargar
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('codigo').focus();
});

// Solo números en el campo código
document.getElementById('codigo').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
    
    // Auto-submit cuando se completen 6 dígitos
    if (this.value.length === 6) {
        document.getElementById('ci_visitante').focus();
    }
});

// Validación en tiempo real
document.getElementById('ci_visitante').addEventListener('input', function() {
    const codigo = document.getElementById('codigo').value;
    const btnValidar = document.getElementById('btnValidar');
    
    if (codigo.length === 6 && this.value.trim().length > 0) {
        btnValidar.classList.remove('btn-primary');
        btnValidar.classList.add('btn-success');
    } else {
        btnValidar.classList.remove('btn-success');
        btnValidar.classList.add('btn-primary');
    }
});

// Shortcuts de teclado
document.addEventListener('keydown', function(e) {
    // Ctrl + L = Limpiar
    if (e.ctrlKey && e.key === 'l') {
        e.preventDefault();
        limpiarFormulario();
    }
    
    // Enter en CI = Validar
    if (e.key === 'Enter' && document.activeElement.id === 'ci_visitante') {
        document.getElementById('validarCodigoForm').dispatchEvent(new Event('submit'));
    }
});
</script>
@endsection