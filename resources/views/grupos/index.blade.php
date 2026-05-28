@extends('layouts.ap')
@section('title','Grupos del CUP')
@section('content')
<div class="ph">
  <h1>Grupos del CUP</h1>
  <p class="sub">CU-11 Generar grupos e inscribir postulantes · CU-12 Asignar docentes con horarios</p>
  <ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li>Grupos</li></ol>
</div>

@if(session('success'))<div class="al al-v" style="margin-bottom:1rem"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
@if(session('error'))<div class="al al-d" style="margin-bottom:1rem"><i class="fas fa-times-circle"></i> {{ session('error') }}</div>@endif
@if($errors->any())<div class="al al-d" style="margin-bottom:1rem"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

{{-- Tarjetas resumen --}}
<div class="sg" style="margin-bottom:1.5rem">
  <div class="sc" style="cursor:default">
    <div class="si c1"><i class="fas fa-users"></i></div>
    <div><div class="sv">{{ $totalInscritos }}</div><div class="sl">Postulantes inscritos</div></div>
  </div>
  <div class="sc" style="cursor:default">
    <div class="si c2"><i class="fas fa-calculator"></i></div>
    <div><div class="sv">{{ $gruposNecesarios }}</div><div class="sl">Grupos necesarios ⌈÷70⌉</div></div>
  </div>
  <div class="sc" style="cursor:default">
    <div class="si c5"><i class="fas fa-layer-group"></i></div>
    <div><div class="sv">{{ $grupos->count() }}</div><div class="sl">Grupos generados</div></div>
  </div>
  @if($gestion)
  <div class="sc" style="cursor:default">
    <div class="si c6"><i class="fas fa-calendar-check"></i></div>
    <div><div class="sv" style="font-size:.85rem">{{ $gestion->descripcion }}</div><div class="sl">Gestión activa</div></div>
  </div>
  @endif
</div>

@if(!$gestion)
  <div class="al al-w"><i class="fas fa-exclamation-triangle"></i>
    No hay gestión activa. <a href="{{ route('gestiones.index') }}">Activar una gestión</a>.
  </div>
@else
  @can('crear grupos')
  <div style="margin-bottom:1.25rem">
    <form action="{{ route('grupos.generar') }}" method="POST" style="display:inline">
      @csrf
      <button type="submit" class="btn bp"
              onclick="return confirm('¿Generar los grupos necesarios automáticamente?\n\nFórmula: ⌈{{ $totalInscritos }} ÷ 70⌉ = {{ $gruposNecesarios }} grupo(s)')">
        <i class="fas fa-magic"></i> CU-11: Generar grupos automáticamente
      </button>
    </form>
  </div>
  @endcan

  @if($grupos->isEmpty())
    <div class="al al-w">
      <i class="fas fa-info-circle"></i>
      No hay grupos aún. Con {{ $totalInscritos }} postulantes inscritos se necesitan
      <strong>{{ $gruposNecesarios }}</strong> grupo(s). Usa el botón de arriba para generarlos.
    </div>
  @else
  <div class="card">
    <div class="card-hd"><i class="fas fa-layer-group"></i>Grupos — {{ $gestion->descripcion }}</div>
    <div class="card-bd">
      <table class="ct">
        <thead>
          <tr>
            <th>Código</th><th>Turno</th><th>Modalidad</th>
            <th>Capacidad</th><th>Inscritos</th>
            <th>Asignaciones</th><th>Estado</th><th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($grupos as $g)
          <tr>
            <td><strong>{{ $g->codigo }}</strong></td>
            <td>{{ ucfirst($g->turno) }}</td>
            <td>{{ ucfirst($g->modalidad) }}</td>
            <td>{{ $g->capacidad_maxima }}</td>
            <td>
              <span class="bg {{ $g->postulantes_count >= $g->capacidad_maxima ? 'bd':($g->postulantes_count > 0 ?'bna':'bg2') }}">
                {{ $g->postulantes_count }} / {{ $g->capacidad_maxima }}
              </span>
            </td>
            <td>
              <span class="bg {{ $g->asignaciones->count() >= 4 ?'bv':'bna' }}">
                {{ $g->asignaciones->count() }}/4 materias
              </span>
            </td>
            <td><span class="bg {{ $g->estado?'bv':'bg2' }}">{{ $g->estado?'Activo':'Inactivo' }}</span></td>
            <td>
              <a href="{{ route('grupos.show',$g) }}" class="btn bsm bw" title="Ver detalle / asignar docentes">
                <i class="fas fa-eye"></i> Gestionar
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif
@endif
@endsection
