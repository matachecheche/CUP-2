@extends('plantilla')

@section('title', 'Panel de Reservas')

@push('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
    @if (session('success'))
        <script>
            Swal.fire({
                toast: true,
                position: "top-end",
                icon: "success",
                title: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 1500
            });
        </script>
    @endif

    <div class="container-fluid px-4">
        <h1 class="mt-4">Panel de Reservas</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
            <li class="breadcrumb-item active">Reservas</li>
        </ol>

        {{-- Solo residentes pueden agendar --}}
        @if(auth()->check() && auth()->user()->residente_id)
            <div class="mb-4">
                <a href="{{ route('reservas.create') }}" class="btn btn-primary btn-sm">
                    Agendar Nueva Reserva
                </a>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
            </div>
            <div class="card-body table-responsive">
                <table id="datatablesReservas" class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID Reserva</th>
                            <th>Área Común</th>
                            <th>Monto (Bs.)</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Residente</th>
                            <th>Acciones</th>
                            <th>Verificación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservas as $reserva)
                            <tr>
                                <td>{{ $reserva->id }}</td>
                                <td>{{ $reserva->areaComun->nombre ?? 'N/D' }}</td>
                                <td>{{ number_format($reserva->areaComun->monto ?? 0, 2) }}</td>
                                <td>
                                    <span class="badge
                                        @if(strtolower($reserva->estado ?? '') == 'pendiente') bg-warning text-dark
                                        @elseif(strtolower($reserva->estado ?? '') == 'confirmada') bg-success
                                        @elseif(strtolower($reserva->estado ?? '') == 'cancelado') bg-secondary
                                        @else bg-light text-dark
                                        @endif
                                    ">
                                        {{ ucfirst($reserva->estado ?? 'N/D') }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }}
                                    -
                                    {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') }}
                                </td>
                                <td>{{ $reserva->residente->nombre ?? 'N/D' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        {{-- Editar sólo para residentes --}}
                                        @if(auth()->check() && auth()->user()->residente_id)
                                            <a href="{{ route('reservas.edit', $reserva->id) }}"
                                               class="btn btn-sm btn-warning me-1">
                                                Editar
                                            </a>
                                        @endif
                                        {{-- Eliminar --}}
                                        <form action="{{ route('reservas.destroy', $reserva->id) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#confirmarEliminar-{{ $reserva->id }}">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('reservas.verificar-inventario', $reserva->id) }}"
                                       class="btn btn-sm btn-info">
                                        Verificar
                                    </a>
                                </td>
                            </tr>
                            {{-- Confirmación de eliminación --}}
                            <div class="modal fade" id="confirmarEliminar-{{ $reserva->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmarEliminarLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="confirmarEliminarLabel">Confirmar Eliminación</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            ¿Estás seguro de que deseas eliminar esta reserva?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                Cancelar
                                            </button>
                                            <form action="{{ route('reservas.destroy', $reserva->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
            crossorigin="anonymous"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            new simpleDatatables.DataTable("#datatablesReservas");
        });
    </script>
@endpush
