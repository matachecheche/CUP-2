@extends('layouts.ap')
@section('title','Proceso de Admisión')
@section('content')
<div class="ph">
  <h1>Proceso de Admisión</h1>
  <p class="sub">CU-16 Procesar · CU-17 Reasignar · CU-18 Publicar resultados</p>
  <ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li>Admisión</li></ol>
</div>

@if(session('success'))<div class="al al-v"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif

@if($gestion)
{{-- Tarjetas resumen --}}
<div class="sg" style="margin-bottom:1.5rem">
  <div class="sc" style="cursor:default"><div class="si c1"><i class="fas fa-users"></i></div><div><div class="sv">{{ $resumen['total'] }}</div><div class="sl">Total inscritos</div></div></div>
  <div class="sc" style="cursor:default"><div class="si c2"><i class="fas fa-check"></i></div><div><div class="sv">{{ $resumen['aprobados'] }}</div><div class="sl">Aprobados</div></div></div>
  <div class="sc" style="cursor:default"><div class="si" style="background:#d1fae5;color:#065f46"><i class="fas fa-star"></i></div><div><div class="sv">{{ $resumen['admitidos_1'] }}</div><div class="sl">Admitidos 1ª opción</div></div></div>
  <div class="sc" style="cursor:default"><div class="si" style="background:#fef3c7;color:#92400e"><i class="fas fa-exchange-alt"></i></div><div><div class="sv">{{ $resumen['admitidos_2'] }}</div><div class="sl">Admitidos 2ª opción</div></div></div>
  <div class="sc" style="cursor:default"><div class="si" style="background:#fee2e2;color:#991b1b"><i class="fas fa-times"></i></div><div><div class="sv">{{ $resumen['no_admitidos'] }}</div><div class="sl">No admitidos</div></div></div>
</div>

<div style="display:flex;gap:.75rem;margin-bottom:1.25rem">
  <form action="{{ route('admision.procesar') }}" method="POST">@csrf
    <button type="submit" class="btn bp" onclick="return confirm('¿Procesar admisión? Esto sobreescribirá resultados previos.')"><i class="fas fa-cogs"></i> Procesar admisión (CU-16/17)</button>
  </form>
  <form action="{{ route('admision.publicar') }}" method="POST">@csrf
    <button type="submit" class="btn" style="background:#10b981;color:#fff"><i class="fas fa-bullhorn"></i> Publicar resultados (CU-18)</button>
  </form>
</div>

@if($admisiones->isNotEmpty())
<div class="card">
  <div class="card-hd"><i class="fas fa-list"></i>Resultados — {{ $gestion->descripcion }}</div>
  <div class="card-bd">
  <table class="ct">
    <thead><tr><th>Postulante</th><th>CI</th><th>Promedio</th><th>Carrera asignada</th><th>Resultado</th><th>Publicado</th></tr></thead>
    <tbody>
    @foreach($admisiones as $a)
    <tr>
      <td>{{ $a->postulante?->nombre_completo }}</td>
      <td>{{ $a->postulante?->ci }}</td>
      <td><strong>{{ number_format($a->promedio_general,2) }}</strong></td>
      <td>{{ $a->carreraAsignada?->sigla ?? '—' }}</td>
      <td>
        @php $cls = match($a->resultado){'admitido_primera'=>'bv','admitido_segunda'=>'bna','no_admitido'=>'bd',default=>'bg2'}; @endphp
        <span class="bg {{ $cls }}">{{ str_replace('_',' ',ucfirst($a->resultado)) }}</span>
      </td>
      <td><span class="bg {{ $a->publicado?'bv':'bg2' }}">{{ $a->publicado?'Sí':'No' }}</span></td>
    </tr>
    @endforeach
    </tbody>
  </table>
  </div>
</div>
@endif

@else
<div class="al al-w"><i class="fas fa-exclamation-triangle"></i> No hay gestión activa. Crea o activa una gestión en el módulo de Gestiones.</div>
@endif
@endsection
