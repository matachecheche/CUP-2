@extends('layouts.ap')

@section('content')
<div class="container">
    <h4>Verificación de inventario - {{ $reserva->areaComun->nombre }}</h4>
    <p><strong>Fecha de la reserva:</strong> {{ $reserva->fecha }}</p>

    <form method="POST" action="{{ route('reservas.guardar-verificacion', $reserva->id) }}">
        @csrf
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Activo</th>
                    <th>Estado actual</th>
                    <th>¿Presente?</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reserva->areaComun->inventarios as $item)
                    <tr>
                        <td>{{ $item->nombre }}</td>
                        <td>{{ $item->estado }}</td>
                        <td>
                            <select name="verificaciones[{{ $item->id }}][estado]" class="form-control" required>
                                <option value="ok">Ok</option>
                                <option value="faltante">Faltante</option>
                                <option value="roto">Roto</option>
                                <option value="otro">Otro</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="verificaciones[{{ $item->id }}][observacion]" class="form-control" placeholder="Observaciones">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-success">Guardar Verificación</button>
        <a href="{{ route('reservas.index') }}" class="btn btn-secondary">Volver</a>
    </form>
</div>
@endsection
