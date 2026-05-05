@extends('plantilla')

@section('title', 'Tipos de Cuotas')

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
    <h1 class="mt-4">Tipos de Cuotas</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Tipos de Cuotas</li>
    </ol>

 
    <div class="mb-4">
        <a href="{{ route('tipos-cuotas.create') }}"><button type="button" class="btn btn-primary btn-sm">Nuevo Tipo de Cuota</button></a>
    </div>
  

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Tabla Tipos de Cuotas
        </div>
        <div class="card-body table-responsive">
            <table id="datatablesSimple" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Frecuencia</th>
                        <th>Editable</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tipos as $tipo)
                    <tr>
                        <td>{{ $tipo->id }}</td>
                        <td>{{ $tipo->nombre }}</td>
                        <td>{{ ucfirst($tipo->frecuencia) }}</td>
                        <td>{{ $tipo->editable ? 'Sí' : 'No' }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                
                                <a href="{{ route('tipos-cuotas.edit', $tipo->id) }}" class="btn btn-warning btn-sm">Editar</a>
                              
                                 
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmarEliminarTipo-{{ $tipo->id }}">Eliminar</button>
                               
                            </div>
                        </td>
                    </tr>

                    
                    <!-- Modal -->
                    <div class="modal fade" id="confirmarEliminarTipo-{{ $tipo->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmarEliminarTipoLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Eliminar Tipo de Cuota</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ¿Desea eliminar el tipo de cuota: {{ $tipo->nombre }}?
                                </div>
                                <div class="modal-footer">
                                    <form action="{{ route('tipos-cuotas.destroy', $tipo->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-primary btn-sm">Aceptar</button>
                                    </form>
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
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
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
@endpush