@extends('plantilla')

@section('title', 'Cuotas y Pagos')

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
    <h1 class="mt-4">Cuotas y Pagos</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Cuotas</li>
    </ol>

 
    <div class="mb-4">
        <a href="{{ route('cuotas.create') }}"><button type="button" class="btn btn-primary btn-sm">Emitir Nueva Cuota</button></a>
    </div>
   



    


    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Tabla Cuotas
        </div>
        <div class="card-body table-responsive">
            <table id="datatablesSimple" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <!-- <th>Unidad</th> -->
                        <th>Residente</th>
                        <th>Mes</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cuotas as $cuota)
                    <tr>
                        <td>{{ $cuota->id }}</td>
                        <!-- <td>{{ $cuota->unidad->codigo ?? 'N/A' }}</td> -->
                        <td>{{ $cuota->residente->nombre_completo ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($cuota->fecha)->format('F Y') }}</td>
                        <td>${{ number_format($cuota->monto, 2) }}</td>
                        <td>
                            <span class="badge
                                @if($cuota->estado == 'pagado') bg-success
                                @elseif($cuota->estado == 'pendiente') bg-warning text-dark
                                @elseif($cuota->estado == 'activa') bg-primary
                                @elseif($cuota->estado == 'cancelada') bg-danger
                                @else bg-secondary
                                @endif">
                                {{ ucfirst($cuota->estado) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                @can('ver cuotas')
                                <a href="{{ route('cuotas.show', $cuota->id) }}" class="btn btn-info btn-sm">Ver</a>
                                @endcan
                               
                                <a href="{{ route('cuotas.edit', $cuota->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmarEliminar-{{ $cuota->id }}">Eliminar</button>
                              
                            </div>
                        </td>
                    </tr>

                  
                    <!-- Modal -->
                    <div class="modal fade" id="confirmarEliminar-{{ $cuota->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmarEliminarLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Eliminar cuota</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ¿Desea eliminar la cuota de: Unidad {{ $cuota->unidad->codigo ?? 'N/A' }}?
                                </div>
                                <div class="modal-footer">
                                    <form action="{{ route('cuotas.destroy', $cuota->id) }}" method="POST">
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
