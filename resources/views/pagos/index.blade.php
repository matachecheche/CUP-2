@extends('layouts.ap')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filtro = document.getElementById('filtro_tiempo');

        const fechaDesde = document.getElementById('fechaDesdeContainer');
        const fechaHasta = document.getElementById('fechaHastaContainer');
        const mes = document.getElementById('mesContainer');
        const semana = document.getElementById('semanaContainer');
        const anio = document.getElementById('anioContainer');

        function actualizarCampos() {
            fechaDesde.style.display = 'none';
            fechaHasta.style.display = 'none';
            mes.style.display = 'none';
            semana.style.display = 'none';
            anio.style.display = 'none';

            const tipo = filtro.value;

            if (tipo === 'fecha') {
                fechaDesde.style.display = 'block';
                fechaHasta.style.display = 'block';
            } else if (tipo === 'mes') {
                mes.style.display = 'block';
            } else if (tipo === 'semana') {
                semana.style.display = 'block';
            } else if (tipo === 'anio') {
                anio.style.display = 'block';
            }
        }

        filtro.addEventListener('change', actualizarCampos);
        actualizarCampos(); // por si viene con valor
    });
</script>


@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Listado de Pagos</h2>
        <div class="position-relative">
            <a href="#" id="notificacionesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" class="text-dark">
                <i class="fas fa-bell fa-lg"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    3
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificacionesDropdown">
                <li>
                    <h6 class="dropdown-header">Notificaciones</h6>
                </li>
                <li><a class="dropdown-item" href="#">Nueva cuota generada</a></li>
                <li><a class="dropdown-item" href="#">Tu pago fue registrado</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-center" href="#">Ver todas</a></li>
            </ul>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- filtros --}}
    <div class="container mb-4">
        <div class="card shadow">
            <div class="card-body">
                <form method="GET" action="{{ route('pagos.index') }}">
                    <div class="row g-3 align-items-end">
                        {{-- Búsqueda por residente o número de cuota --}}
                        <div class="col-md-4">
                            <label class="form-label">Buscar por residente o cuota</label>
                            <input type="text" name="search" class="form-control" placeholder="Ej: Juan Pérez, Cuota #12" value="{{ request('search') }}">
                        </div>

                        {{-- Filtro por método de pago --}}
                        <div class="col-md-2">
                            <label class="form-label">Método</label>
                            <select name="metodo" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <option value="efectivo" {{ request('metodo') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="transferencia" {{ request('metodo') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                <option value="qr" {{ request('metodo') == 'qr' ? 'selected' : '' }}>QR</option>
                            </select>
                        </div>

                        {{-- Tipo de filtro por tiempo --}}
                        <div class="col-md-2">
                            <label class="form-label">Tipo de filtro</label>
                            <select name="filtro_tiempo" id="filtro_tiempo" class="form-select">
                                <option value="">-- Tipo --</option>
                                <option value="fecha" {{ request('filtro_tiempo') == 'fecha' ? 'selected' : '' }}>Por Fecha</option>
                                <option value="mes" {{ request('filtro_tiempo') == 'mes' ? 'selected' : '' }}>Por Mes</option>
                                <option value="semana" {{ request('filtro_tiempo') == 'semana' ? 'selected' : '' }}>Por Semana</option>
                                <option value="anio" {{ request('filtro_tiempo') == 'anio' ? 'selected' : '' }}>Por Año</option>
                            </select>
                        </div>

                        {{-- Por FECHA --}}
                        <div class="col-md-2" id="fechaDesdeContainer" style="display: none;">
                            <label class="form-label">Desde</label>
                            <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                        </div>
                        <div class="col-md-2" id="fechaHastaContainer" style="display: none;">
                            <label class="form-label">Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                        </div>

                        {{-- Por MES --}}
                        <div class="col-md-2" id="mesContainer" style="display: none;">
                            <label class="form-label">Mes</label>
                            <input type="month" name="mes" class="form-control" value="{{ request('mes') }}">
                        </div>

                        {{-- Por SEMANA --}}
                        <div class="col-md-2" id="semanaContainer" style="display: none;">
                            <label class="form-label">Semana</label>
                            <input type="week" name="semana" class="form-control" value="{{ request('semana') }}">
                        </div>

                        {{-- Por AÑO --}}
                        <div class="col-md-2" id="anioContainer" style="display: none;">
                            <label class="form-label">Año</label>
                            <input type="number" name="anio" class="form-control" min="2000" max="{{ date('Y') + 1 }}" value="{{ request('anio') }}">
                        </div>

                        <div class="col-md-1 d-grid">
                            <button class="btn btn-outline-primary" type="submit">Filtrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Cuota</th>
                <th>Monto Pagado</th>
                <th>Fecha</th>
                <th>Método</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pagos as $pago)
            <tr>
                <td>{{ $pago->id }}</td>
                <td>Cuota #{{ $pago->cuota_id }}</td>
                <td>${{ number_format($pago->monto_pagado, 2) }}</td>
                <td>{{ $pago->fecha_pago }}</td>
                <td>{{ ucfirst($pago->metodo) }}</td>
                <td>{{ $pago->user->name ?? 'N/A' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No hay pagos registrados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $pagos->links() }}
</div>
@endsection