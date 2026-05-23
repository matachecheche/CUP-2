@extends('layouts.ap')
@section('title', 'Bitácora del Sistema')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
/* ── Chips de módulo ─────────────────────────── */
.chip-modulo {
    display: inline-block;
    font-size: .63rem; font-weight: 700;
    padding: 2px 7px; border-radius: 3px;
    text-transform: uppercase; letter-spacing: .05em;
    white-space: nowrap;
}
.chip-Seguridad        { background:#dbeafe; color:#1d4f8f; }
.chip-Usuarios         { background:#d4e8dc; color:#1a5c38; }
.chip-Roles            { background:#d8f0e6; color:#1a5c38; }
.chip-Bitácora         { background:#ede0f7; color:#5b2a8a; }
.chip-GestiónAcadémica { background:#fef9c3; color:#78350f; }
.chip-Postulantes      { background:#fde8cc; color:#8a4300; }
.chip-Docentes         { background:#fff8e1; color:#7a5c00; }
.chip-Grupos           { background:#fce4e4; color:#8a1f1f; }
.chip-Evaluación       { background:#e0f7fa; color:#006064; }
.chip-Admisión         { background:#f3e5f5; color:#6a1b9a; }
.chip-Reportes         { background:#e8f5e9; color:#2e7d32; }
.chip-Sistema          { background:#f0f0f0; color:#555; }

/* ── Chips de método HTTP ────────────────────── */
.chip-http {
    font-size: .65rem; font-weight: 800;
    padding: 1px 5px; border-radius: 3px;
    font-family: 'Courier New', monospace;
    white-space: nowrap;
}
.chip-GET    { background:#e8f4fd; color:#0369a1; }
.chip-POST   { background:#d4edda; color:#155724; }
.chip-PUT    { background:#fff3cd; color:#856404; }
.chip-PATCH  { background:#fff3cd; color:#856404; }
.chip-DELETE { background:#fde8e3; color:#a3290c; }
</style>
@endpush

@section('content')

<div class="page-header">
    <h1>Bitácora del Sistema</h1>
    <p class="subtitle">Registro completo de todas las acciones realizadas por los usuarios</p>
    <ol class="breadcrumb">
        <li><a href="{{ route('panel') }}">Inicio</a></li>
        <li>Bitácora</li>
    </ol>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-journal-whills"></i>
        Bitácora del Sistema
        <span style="margin-left:auto; font-size:.8rem; color:var(--txt-3); font-weight:400;">
            {{ $bitacoras->count() }} registros cargados
        </span>
    </div>
    <div class="card-body" style="padding: .75rem;">
        <div class="table-wrapper">
            <table id="tablaBitacora" class="cup-table" style="width:100%;">
                <thead>
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Usuario</th>
                        <th>Módulo</th>
                        <th>Acción realizada</th>
                        <th>Método</th>
                        <th>Ruta</th>
                        <th>Dirección IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bitacoras as $log)
                    @php
                        $modKey = str_replace([' ', 'é', 'ó', 'á', 'í', 'ú', 'ñ'], ['', 'é', 'ó', 'á', 'í', 'ú', 'ñ'], $log->modulo ?? 'Sistema');
                        $chipClass = 'chip-' . str_replace(' ', '', $log->modulo ?? 'Sistema');
                    @endphp
                    <tr>
                        <td style="white-space:nowrap; font-size:.8rem; color:var(--txt-3);">
                            {{ \Carbon\Carbon::parse($log->fecha_hora)->format('d/m/Y H:i:s') }}
                        </td>
                        <td>
                            <span style="font-weight:600; font-size:.87rem;">
                                {{ $log->usuario ?? '—' }}
                            </span>
                        </td>
                        <td>
                            @if($log->modulo)
                                <span class="chip-modulo {{ $chipClass }}">{{ $log->modulo }}</span>
                            @else
                                <span style="color:var(--txt-3); font-size:.8rem;">—</span>
                            @endif
                        </td>
                        <td style="font-size:.86rem; color:var(--txt);">
                            {{ $log->accion }}
                        </td>
                        <td>
                            @if($log->metodo_http)
                                <span class="chip-http chip-{{ $log->metodo_http }}">
                                    {{ $log->metodo_http }}
                                </span>
                            @endif
                        </td>
                        <td style="font-size:.78rem; font-family:'Courier New',monospace; color:var(--txt-3);">
                            {{ $log->ruta ?? '—' }}
                        </td>
                        <td style="font-size:.78rem; font-family:'Courier New',monospace; color:var(--txt-3);">
                            {{ $log->ip ?? '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:2rem; color:var(--txt-3);">
                            <i class="fas fa-journal-whills" style="font-size:2rem; display:block; margin-bottom:.5rem; opacity:.3;"></i>
                            No hay registros en la bitácora todavía.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('js')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function(){
    $('#tablaBitacora').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        order: [[0, 'desc']],          // más reciente primero
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        columnDefs: [
            { targets: [4, 5], orderable: false }
        ]
    });
});
</script>
@endpush

@endsection
