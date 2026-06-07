@extends('layouts.ap')
@section('title','Pasarela de Pago')
@push('css')<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">@endpush
@section('content')
<div class="ph"><h1>Gestión de Pasarela de Pago</h1><p class="sub">CU-20 — Pagos de inscripción al CUP (Stripe Checkout)</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li>Pagos</li></ol></div>

@if(session('success'))<div style="background:#e8f6ee;color:#14532d;border:1px solid #bbe5c8;border-radius:6px;padding:.7rem 1rem;margin-bottom:1rem">{{ session('success') }}</div>@endif

<div style="display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:1.2rem">
  <div style="flex:1;min-width:180px;background:#fff;border:1px solid #e8e3d8;border-radius:8px;padding:.9rem 1.1rem">
    <div style="font-size:.72rem;letter-spacing:.05em;text-transform:uppercase;color:var(--t3,#8a8678)">Total recaudado</div>
    <div style="font-size:1.45rem;font-weight:700">Bs {{ number_format($stats['recaudado'],2) }}</div>
  </div>
  <div style="flex:1;min-width:140px;background:#fff;border:1px solid #e8e3d8;border-radius:8px;padding:.9rem 1.1rem">
    <div style="font-size:.72rem;letter-spacing:.05em;text-transform:uppercase;color:var(--t3,#8a8678)">Pagos confirmados</div>
    <div style="font-size:1.45rem;font-weight:700">{{ $stats['pagados'] }}</div>
  </div>
  <div style="flex:1;min-width:140px;background:#fff;border:1px solid #e8e3d8;border-radius:8px;padding:.9rem 1.1rem">
    <div style="font-size:.72rem;letter-spacing:.05em;text-transform:uppercase;color:var(--t3,#8a8678)">Pendientes</div>
    <div style="font-size:1.45rem;font-weight:700">{{ $stats['pendientes'] }}</div>
  </div>
</div>

<div class="card"><div class="card-hd"><i class="fas fa-credit-card"></i>Pagos registrados</div><div class="card-bd">
<div class="tw"><table id="tpg" class="ct" style="width:100%">
<thead><tr><th>#</th><th>Comprobante</th><th>Postulante</th><th>CI</th><th>Gestión</th><th>Monto</th><th>Método</th><th>Estado</th><th>Fecha de pago</th><th>Acciones</th></tr></thead>
<tbody>@foreach($pagos as $pg)<tr>
<td style="color:var(--t3);font-size:.8rem">{{ $loop->iteration }}</td>
<td style="font-family:'Courier New',monospace;font-size:.84rem">{{ $pg->comprobante ?? '—' }}</td>
<td><strong>{{ $pg->postulante?->apellidos }}</strong>, {{ $pg->postulante?->nombres }}</td>
<td style="font-family:'Courier New',monospace;font-size:.84rem">{{ $pg->postulante?->ci }}</td>
<td style="font-size:.84rem">{{ $pg->gestion?->descripcion ?? '—' }}</td>
<td><strong>Bs {{ number_format($pg->monto,2) }}</strong></td>
<td style="font-size:.84rem">{{ ucfirst($pg->metodo) }}</td>
<td><span class="bg {{ $pg->estado_badge }}">{{ ucfirst($pg->estado) }}</span></td>
<td style="font-size:.84rem">{{ $pg->fecha_pago?->format('d/m/Y H:i') ?? '—' }}</td>
<td><div class="bg3">
@if($pg->postulante)<a href="{{ route('postulantes.show',$pg->postulante) }}" class="btn bsm bo2" title="Ver postulante"><i class="fas fa-eye"></i></a>@endif
@if($pg->estado === 'pendiente' && $pg->postulante?->estado === 'preinscrito')<a href="{{ route('pagos.pagar',$pg->postulante) }}" class="btn bsm bv" title="Retomar pago"><i class="fas fa-credit-card"></i></a>@endif
</div></td></tr>@endforeach</tbody></table></div></div></div>
@push('js')<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script>$(()=>$('#tpg').DataTable({language:{url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'},order:[[8,'desc']],pageLength:20}))</script>@endpush
@endsection
