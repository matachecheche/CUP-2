
{{-- resources/views/visitas/panel-guardia.blade.php --}}
@extends('layouts.ap')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-shield-alt text-primary"></i>
            Panel de Control - Guardia de Seguridad
        </h2>
        <div class="d-flex gap-2">
            <a href="{{ route('visitas.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list"></i> Todas las Visitas
            </a>
            <span class="badge bg-light text-dark fs-6">
                <i class="fas fa-clock"></i>
                Última actualización: {{ now()->format('d/m/Y H:i:s') }}
            </span>
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

    {{-- Formulario para validar código --}}
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-key"></i> Validar Código de Visitante
            </h5>
        </div>
        <div class="card-body">
            <form id="validarCodigoForm">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="codigo" class="form-label">
                            <i class="fas fa-hashtag"></i> Código (6 dígitos)
                        </label>
                        <input type="text"
                               id="codigo"
                               name="codigo"
                               class="form-control form-control-lg text-center"
                               placeholder="123456"
                               maxlength="6"
                               pattern="[0-9]{6}"
                               required>
                    </div>
                    <div class="col-md-4">
                        <label for="ci_visitante" class="form-label">
                            <i class="fas fa-id-card"></i> CI del Visitante
                        </label>
                        <input type="text"
                               id="ci_visitante"
                               name="ci_visitante"
                               class="form-control form-control-lg"
                               placeholder="12345678"
                               required>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-search"></i> Validar Código
                        </button>
                    </div>
                </div>
            </form>

            {{-- Resultado de la validación --}}
            <div id="resultadoValidacion" class="mt-4" style="display: none;">
                <div class="alert alert-info border-info">
                    <h6><i class="fas fa-user-check"></i> Datos del Visitante:</h6>
                    <div id="datosVisitante" class="mt-3"></div>
                    <div class="mt-3 d-flex gap-2">
                        <button id="btnRegistrarEntrada" class="btn btn-success">
                            <i class="fas fa-sign-in-alt"></i> Registrar Entrada
                        </button>
                        <button id="btnCancelar" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Visitas En Curso --}}
        <div class="col-md-6">
            <div class="card h-100 border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users"></i>
                        Visitantes Dentro del Condominio 
                        <span class="badge bg-light text-info">{{ $visitasEnCurso->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="overflow-auto" style="max-height: 400px;">
                        @forelse($visitasEnCurso as $visita)
                            <div class="card mb-3 border-start border-info border-4">
                                <div class="card-body py-3">
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-user-circle text-info me-2"></i>
                                                <strong class="fs-6">{{ $visita->nombre_visitante }}</strong>
                                            </div>
                                            <div class="small text-muted">
                                                <div><i class="fas fa-id-badge me-1"></i> CI: {{ $visita->ci_visitante }}</div>
                                                <div><i class="fas fa-home me-1"></i> Visitando: {{ $visita->residente->nombre_completo }}</div>
                                                <div class="text-success">
                                                    <i class="fas fa-sign-in-alt me-1"></i>
                                                    @if($visita->hora_entrada)
                                                        Entrada: {{ $visita->hora_entrada->format('H:i') }}
                                                    @else
                                                        Entrada: No registrada
                                                    @endif
                                                </div>
                                                @if($visita->placa_vehiculo)
                                                    <div class="text-info">
                                                        <i class="fas fa-car me-1"></i> Vehículo: {{ $visita->placa_vehiculo }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <form action="{{ route('visitas.salida', $visita) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-primary mb-1"
                                                        onclick="return confirm('¿Registrar salida de {{ $visita->nombre_visitante }}?')">
                                                    <i class="fas fa-sign-out-alt"></i> Salida
                                                </button>
                                            </form>
                                            <a href="{{ route('visitas.show', $visita) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay visitantes dentro del condominio.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Visitas Pendientes (próximas 2 horas) --}}
        <div class="col-md-6">
            <div class="card h-100 border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-clock"></i>
                        Visitas Pendientes - Próximas 2 Horas 
                        <span class="badge bg-dark">{{ $visitasPendientes->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="overflow-auto" style="max-height: 400px;">
                        @forelse($visitasPendientes as $visita)
                            <div class="card mb-3 border-start border-warning border-4">
                                <div class="card-body py-3">
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-user-clock text-warning me-2"></i>
                                                <strong class="fs-6">{{ $visita->nombre_visitante }}</strong>
                                            </div>
                                            <div class="small text-muted">
                                                <div><i class="fas fa-id-badge me-1"></i> CI: {{ $visita->ci_visitante }}</div>
                                                <div><i class="fas fa-home me-1"></i> Visitando: {{ $visita->residente->nombre_completo }}</div>
                                                <div class="text-primary">
                                                    <i class="fas fa-key me-1"></i> Código: <strong>{{ $visita->codigo }}</strong>
                                                </div>
                                                <div class="text-warning">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Horario: {{ $visita->fecha_inicio->format('H:i') }} - {{ $visita->fecha_fin->format('H:i') }}
                                                </div>
                                                @if($visita->placa_vehiculo)
                                                    <div class="text-info">
                                                        <i class="fas fa-car me-1"></i> Vehículo: {{ $visita->placa_vehiculo }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <form action="{{ route('visitas.entrada', $visita) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-success mb-1"
                                                        onclick="return confirm('¿Registrar entrada de {{ $visita->nombre_visitante }}?')">
                                                    <i class="fas fa-sign-in-alt"></i> Entrada
                                                </button>
                                            </form>
                                            <a href="{{ route('visitas.show', $visita) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay visitas programadas para las próximas 2 horas.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Estadísticas del día --}}
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card border-secondary">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar"></i>
                        Resumen del Día - {{ now()->format('d/m/Y') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center g-3">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white h-100">
                                <div class="card-body">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <h3 class="mb-1">{{ $visitasEnCurso->count() }}</h3>
                                    <small>Visitantes Dentro</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark h-100">
                                <div class="card-body">
                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                    <h3 class="mb-1">{{ $visitasPendientes->count() }}</h3>
                                    <small>Visitas Pendientes</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body">
                                    <i class="fas fa-sign-in-alt fa-2x mb-2"></i>
                                    <h3 class="mb-1">
                                        {{ \App\Models\Visita::whereDate('hora_entrada', today())->count() }}
                                    </h3>
                                    <small>Entradas Hoy</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white h-100">
                                <div class="card-body">
                                    <i class="fas fa-sign-out-alt fa-2x mb-2"></i>
                                    <h3 class="mb-1">
                                        {{ \App\Models\Visita::whereDate('hora_salida', today())->count() }}
                                    </h3>
                                    <small>Salidas Hoy</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('validarCodigoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const codigo = document.getElementById('codigo').value;
    const ci = document.getElementById('ci_visitante').value;
    
    if (codigo.length !== 6) {
        showAlert('El código debe tener 6 dígitos', 'warning');
        return;
    }
    
    // Mostrar loading
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Validando...';
    submitBtn.disabled = true;
    
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
            showAlert('Código validado correctamente', 'success');
        } else {
            showAlert(data.message, 'danger');
            limpiarFormulario();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error al validar el código', 'danger');
    })
    .finally(() => {
        // Restaurar botón
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

function mostrarDatosVisitante(visita) {
    const datosDiv = document.getElementById('datosVisitante');
    datosDiv.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <p class="mb-2"><i class="fas fa-user text-primary"></i> <strong>Nombre:</strong> ${visita.nombre_visitante}</p>
                <p class="mb-2"><i class="fas fa-id-card text-info"></i> <strong>CI:</strong> ${visita.ci_visitante}</p>
                <p class="mb-2"><i class="fas fa-home text-success"></i> <strong>Residente:</strong> ${visita.residente}</p>
            </div>
            <div class="col-md-6">
                <p class="mb-2"><i class="fas fa-comment-alt text-warning"></i> <strong>Motivo:</strong> ${visita.motivo}</p>
                ${visita.placa_vehiculo ? `<p class="mb-2"><i class="fas fa-car text-info"></i> <strong>Vehículo:</strong> ${visita.placa_vehiculo}</p>` : ''}
            </div>
        </div>
    `;
    
    document.getElementById('resultadoValidacion').style.display = 'block';
    
    // Configurar botón de registrar entrada
    document.getElementById('btnRegistrarEntrada').onclick = function() {
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
    };
}

function limpiarFormulario() {
    document.getElementById('codigo').value = '';
    document.getElementById('ci_visitante').value = '';
    document.getElementById('resultadoValidacion').style.display = 'none';
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'times-circle'}"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

document.getElementById('btnCancelar').addEventListener('click', limpiarFormulario);

// Format código input to only numbers
document.getElementById('codigo').addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});

// Auto-refresh cada 30 segundos
setTimeout(function() {
    location.reload();
}, 30000);

// Countdown timer
let countdown = 30;
const updateCountdown = () => {
    const badge = document.querySelector('.badge.bg-light');
    if (badge) {
        badge.innerHTML = `<i class="fas fa-clock"></i> Recarga en: ${countdown}s`;
    }
    countdown--;
    if (countdown < 0) countdown = 30;
};

setInterval(updateCountdown, 1000);
</script>
@endsection