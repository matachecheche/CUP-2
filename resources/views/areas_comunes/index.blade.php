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
        <h1 class="mt-4">Catalogo de Areas Comunes</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
            <li class="breadcrumb-item active">Areas Comunes</li>
        </ol>

        {{-- Mostrar botón NUEVA ÁREA COMÚN solo si NO es residente --}}
        @if(auth()->check() && !auth()->user()->residente_id)
            <div class="mb-4">
                <a href="{{ route('areas-comunes.create') }}" class="btn btn-primary mb-3">Nueva Área Común</a>
            </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Monto/Hora (Bs.)</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($areasComunes as $area)
                <tr>
                    <td>{{ $area->id }}</td>
                    <td>{{ $area->nombre }}</td>
                    <td>{{ number_format($area->monto, 2) }}</td>
                    <td>
                        <span class="badge
                            @if(strtolower($area->estado) == 'inactivo') bg-secondary
                            @elseif(strtolower($area->estado) == 'activo') bg-success
                            @elseif(strtolower($area->estado) == 'mantenimiento') bg-warning text-dark
                            @else bg-light text-dark
                            @endif
                        ">
                            {{ ucfirst($area->estado) }}
                        </span>
                    </td>
                    <td>
                        {{-- Mostrar botones Editar y Eliminar solo si NO es residente --}}
                        @if(auth()->check() && !auth()->user()->residente_id)
                            <a href="{{ route('areas-comunes.edit', $area->id) }}" class="btn btn-sm btn-warning">Editar</a>

                            <form action="{{ route('areas-comunes.destroy', $area->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta área común?')">Eliminar</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Para paginación si la usas --}}
        {{--<div class="mt-3">
            {{ $areasComunes->links() }}
        </div>--}}
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
