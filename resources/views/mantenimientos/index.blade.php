@extends('layouts.ap')

@section('content')
<div class="container">
    <h2 class="mb-4">Lista de Mantenimientos</h2>

    <a href="{{ route('mantenimientos.create') }}" class="btn btn-primary mb-3">Nuevo Mantenimiento</a>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Buscador con selector de criterio --}}
    <form method="GET" action="{{ route('mantenimientos.index') }}" class="mb-3">
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Buscar..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="filter" class="form-select">
                    <option value="descripcion" {{ request('filter') == 'descripcion' ? 'selected' : '' }}>Descripción</option>
                    <option value="usuario" {{ request('filter') == 'usuario' ? 'selected' : '' }}>Usuario</option>
                    <option value="empresa" {{ request('filter') == 'empresa' ? 'selected' : '' }}>Empresa</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-primary w-100" type="submit">Buscar</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered table-striped table-responsive">
        <thead class="table-dark">
            <tr>
                @php
                    $currentSort = request('sort');
                    $direction = request('direction') === 'asc' ? 'desc' : 'asc';
                    $arrow = fn($field) => $currentSort === $field ? (request('direction') === 'asc' ? '↑' : '↓') : '';
                @endphp

                <th style="width: 70px;">
                    <a href="{{ route('mantenimientos.index', array_merge(request()->all(), ['sort' => 'id', 'direction' => $direction])) }}">
                        ID {!! $arrow('id') !!}
                    </a>
                </th>
                <th style="width: 200px;">
                    <a href="{{ route('mantenimientos.index', array_merge(request()->all(), ['sort' => 'descripcion', 'direction' => $direction])) }}">
                        Descripción {!! $arrow('descripcion') !!}
                    </a>
                </th>
                <th style="width: 100px;">
                    <a href="{{ route('mantenimientos.index', array_merge(request()->all(), ['sort' => 'monto', 'direction' => $direction])) }}">
                        Monto {!! $arrow('monto') !!}
                    </a>
                </th>
                <th style="width: 150px;">
                    <a href="{{ route('mantenimientos.index', array_merge(request()->all(), ['sort' => 'fecha_hora', 'direction' => $direction])) }}">
                        Fecha {!! $arrow('fecha_hora') !!}
                    </a>
                </th>
                <th style="width: 150px;">
                    <a href="{{ route('mantenimientos.index', array_merge(request()->all(), ['sort' => 'usuario', 'direction' => $direction])) }}">
                        Usuario {!! $arrow('usuario') !!}
                    </a>
                </th>
                <th style="width: 200px;">
                    <a href="{{ route('mantenimientos.index', array_merge(request()->all(), ['sort' => 'empresa', 'direction' => $direction])) }}">
                        Empresa {!! $arrow('empresa') !!}
                    </a>
                </th>
                <th style="width: 100px;">
                    Estado
                </th>
                <th style="width: 160px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mantenimientos as $m)
            <tr>
                <td>{{ $m->id }}</td>
                <td>{{ $m->descripcion }}</td>
                <td>{{ $m->monto }}</td>
                <td>{{ $m->fecha_hora }}</td>
                <td>{{ $m->usuario->name ?? '-' }}</td>
                <td>{{ $m->empresa?->nombre ?? '-' }}</td>
                <td>{{ $m->estado == 1 ? 'Activo' : 'Inactivo' }}</td>
                <td>
                    <a href="{{ route('mantenimientos.edit', $m->id) }}" class="btn btn-sm btn-warning">Editar</a>

                    <form action="{{ route('mantenimientos.destroy', $m->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este mantenimiento?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No hay mantenimientos registrados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $mantenimientos->appends(request()->all())->links() }}
    </div>
</div>
@endsection
