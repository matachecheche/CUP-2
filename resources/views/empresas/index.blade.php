@extends('plantilla')

@section('title', 'Empresas Externas')

@push('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
@if (session('success'))
    <script>
        let message = "{{ session('success') }}"
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        Toast.fire({
            icon: "success",
            title: message
        });
    </script>
@endif

<div class="container-fluid px-4">
    <h1 class="mt-4">Empresas Externas</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Empresas</li>
    </ol>

    @can('crear empresas')
    <div class="mb-4">
        <a href="{{ route('empresas.create') }}"><button type="button" class="btn btn-primary btn-sm">Registrar Nueva Empresa</button></a>
    </div>
    @endcan

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Tabla de Empresas Externas
        </div>
        <div class="card-body table-responsive">
            <form method="GET" action="{{ route('empresas.index') }}" class="mb-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">Buscar por nombre o servicio</label>
                        <input type="text" name="search" class="form-control" placeholder="Ej: Jardinería, Seguridad" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 d-grid">
                        <button class="btn btn-outline-primary" type="submit">Filtrar</button>
                    </div>
                </div>
            </form>

            <table id="datatablesSimple" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Servicio</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($empresas as $empresa)
                    <tr>
                        <td>{{ $empresa->id }}</td>
                        <td>{{ $empresa->nombre }}</td>
                        <td>{{ $empresa->servicio }}</td>
                        <td>{{ $empresa->telefono }}</td>
                        <td>{{ $empresa->correo }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                @can('ver empresas')
                                <a href="{{ route('empresas.show', $empresa->id) }}" class="btn btn-info btn-sm">Ver</a>
                                @endcan
                                @can('editar empresas')
                                <a href="{{ route('empresas.edit', $empresa->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                @endcan
                                @can('eliminar empresas')
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmarEliminarEmpresa-{{ $empresa->id }}">Eliminar</button>
                                @endcan
                            </div>
                        </td>
                    </tr>

                    @can('eliminar empresas')
                    <!-- Modal -->
                    <div class="modal fade" id="confirmarEliminarEmpresa-{{ $empresa->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmarEliminarEmpresaLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Eliminar Empresa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ¿Desea eliminar la empresa: {{ $empresa->nombre }}?
                                </div>
                                <div class="modal-footer">
                                    <form action="{{ route('empresas.destroy', $empresa->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-primary btn-sm">Aceptar</button>
                                    </form>
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endcan
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-center mt-3">
                {{ $empresas->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
@endpush
