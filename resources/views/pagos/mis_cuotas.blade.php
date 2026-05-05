@extends('layouts.ap')

@section('title', 'Mis Cuotas')

@section('content')
<div class="container mt-4">
    <h3>Mis Cuotas</h3>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Concepto</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Vencimiento</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cuotas as $cuota)
            <tr>
                <td>
                    <strong>{{ $cuota->titulo }}</strong><br> {{-- Línea nueva: muestra el título --}}
                    <small class="text-muted">{{ $cuota->descripcion }}</small> {{-- Línea nueva: descripción --}}
                </td>
                <td>Bs {{ number_format($cuota->monto, 2) }}</td>
                <td>
                    @if($cuota->estaPagada())
                    <span class="badge bg-success">Pagada</span>
                    @else
                    <span class="badge bg-warning">Pendiente</span>
                    @endif
                </td>
                <td>{{ $cuota->fecha_vencimiento }}</td>
                <td>
                    @if(!$cuota->estaPagada())
                    <a href="{{ route('pagos.create.cuota', ['cuota' => $cuota->id]) }}" class="btn btn-sm btn-success">Pagar</a>
                    @else
                    @if($cuota->estaPagada())
                    <a href="{{ route('pagos.comprobante', $cuota->pagos->first()->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                        Ver Comprobante
                    </a>
                    @endif

                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $cuotas->links() }}
</div>
@endsection
