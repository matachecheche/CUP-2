@extends('layouts.ap')
@section('title','Gestionar Grupo')
@section('content')
<div class="ph">
  <h1>Grupo {{ $grupo->codigo }}</h1>
  <p class="sub">CU-11 · Editar el grupo, inscribir y ver estudiantes (la asignación de docentes es CU-12)</p>
  <ol class="bc">
    <li><a href="{{ route('panel') }}">Inicio</a></li>
    <li><a href="{{ route('grupos.index') }}">Grupos</a></li>
    <li>{{ $grupo->codigo }}</li>
  </ol>
</div>

@if(session('success'))<div class="al al-v" style="margin-bottom:1rem"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
@if(session('error'))<div class="al al-d" style="margin-bottom:1rem"><i class="fas fa-times-circle"></i> {{ session('error') }}</div>@endif
@if($errors->any())<div class="al al-d" style="margin-bottom:1rem"><ul style="margin:0;padding-left:1.2rem">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

{{-- Fila superior: Info + Editar (CU-11) --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;max-width:900px;margin-bottom:1.25rem">
  <div class="card">
    <div class="card-hd"><i class="fas fa-info-circle"></i>Datos del grupo</div>
    <div class="card-bd" style="font-size:.88rem">
      @foreach(['Código'=>$grupo->codigo,'Turno'=>ucfirst($grupo->turno),'Modalidad'=>ucfirst($grupo->modalidad),'Capacidad'=>$grupo->capacidad_maxima,'Inscritos'=>$grupo->postulantes->count(),'Cupos libres'=>$grupo->cupos_disponibles,'Gestión'=>$grupo->gestion?->descripcion] as $l=>$v)
      <div style="display:flex;justify-content:space-between;padding:.35rem 0;border-bottom:1px solid var(--cr2)">
        <span style="color:var(--t3)">{{ $l }}</span>
        <span style="font-weight:500">{{ $v??'—' }}</span>
      </div>
      @endforeach
    </div>
  </div>

  @can('editar grupos')
  <div class="card">
    <div class="card-hd"><i class="fas fa-clock"></i>CU-11 — Editar horario y modalidad</div>
    <div class="card-bd">
      <form action="{{ route('grupos.update',$grupo) }}" method="POST">
        @csrf @method('PUT')
        <div style="margin-bottom:.6rem">
          <label class="fl">Turno</label>
          <select name="turno" class="fs">
            @foreach(['mañana','tarde','noche'] as $t)
            <option value="{{ $t }}" {{ $grupo->turno===$t?'selected':'' }}>{{ ucfirst($t) }}</option>
            @endforeach
          </select>
        </div>
        <div style="margin-bottom:.6rem">
          <label class="fl">Modalidad</label>
          <select name="modalidad" class="fs">
            @foreach(['presencial','virtual'] as $m)
            <option value="{{ $m }}" {{ $grupo->modalidad===$m?'selected':'' }}>{{ ucfirst($m) }}</option>
            @endforeach
          </select>
        </div>
        <div style="margin-bottom:.75rem">
          <label class="fl">Capacidad máxima</label>
          <input type="number" name="capacidad_maxima" class="fc" value="{{ $grupo->capacidad_maxima }}" min="1" max="200">
        </div>
        <button type="submit" class="btn bp bsm"><i class="fas fa-save"></i> Guardar</button>
      </form>
    </div>
  </div>
  @endcan
</div>

{{-- Asignaciones (solo lectura): se gestionan en CU-12 --}}
<div class="card" style="max-width:900px;margin-bottom:1.25rem">
  <div class="card-hd">
    <i class="fas fa-user-tie"></i>Docentes asignados
    <span style="font-weight:normal;font-size:.78rem;margin-left:.5rem">({{ $grupo->asignaciones->count() }} materia(s))</span>
  </div>
  <div class="card-bd">
    @if($grupo->asignaciones->isEmpty())
      <p style="color:var(--t3);text-align:center;padding:.75rem">Sin asignaciones todavía.</p>
    @else
    <table class="ct">
      <thead><tr><th>Materia</th><th>Docente</th><th>Área</th><th>Día</th><th>Horario</th><th>Aula</th></tr></thead>
      <tbody>
        @foreach($grupo->asignaciones as $a)
        <tr>
          <td><strong>{{ $a->materia?->nombre }}</strong></td>
          <td>{{ $a->docente?->apellidos }}, {{ $a->docente?->nombres }}</td>
          <td style="font-size:.82rem;color:var(--t3)">{{ $a->docente?->area_formacion }}</td>
          <td>{{ ucfirst($a->dia) }}</td>
          <td>{{ substr($a->hora_inicio,0,5) }} — {{ substr($a->hora_fin,0,5) }}</td>
          <td>{{ $a->aula ?? '—' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif
    @can('editar grupos')
    <a class="btn bsm bo2" style="margin-top:.8rem" href="{{ route('asignaciones.create',['grupo_id'=>$grupo->id]) }}">
      <i class="fas fa-user-plus"></i> Asignar docente (CU-12)
    </a>
    @endcan
  </div>
</div>

{{-- CU-11: Inscribir postulantes --}}
@can('editar grupos')
<div class="card" style="max-width:900px;margin-bottom:1.25rem">
  <div class="card-hd"><i class="fas fa-users"></i>CU-11 — Inscribir postulantes al grupo</div>
  <div class="card-bd">
    @if($sinGrupo->isEmpty())
      <div class="al al-v" style="margin-bottom:.5rem"><i class="fas fa-check-circle"></i> Todos los postulantes ya están asignados a un grupo.</div>
    @else
    <form action="{{ route('grupos.inscribirPostulantes',$grupo) }}" method="POST">
      @csrf
      <p style="font-size:.84rem;color:var(--t3);margin-bottom:.75rem">
        Capacidad disponible: <strong>{{ $grupo->cupos_disponibles }}</strong> lugares. Selecciona los postulantes a inscribir:
      </p>
      <div style="max-height:260px;overflow-y:auto;border:1px solid var(--cr2);border-radius:.4rem;padding:.5rem;margin-bottom:.75rem">
        @foreach($sinGrupo as $p)
        <label class="fck" style="padding:.3rem .25rem;border-bottom:1px solid var(--cr2)">
          <input type="checkbox" name="postulante_ids[]" value="{{ $p->id }}">
          <span style="font-size:.84rem"><strong>{{ $p->ci }}</strong> — {{ $p->nombre_completo }}
            <span style="color:var(--t3)">· {{ $p->primeraOpcion?->sigla }}</span></span>
        </label>
        @endforeach
      </div>
      <div style="display:flex;gap:.5rem;align-items:center">
        <button type="submit" class="btn bp bsm"><i class="fas fa-user-check"></i> Inscribir seleccionados</button>
        <button type="button" class="btn bo2 bsm" onclick="document.querySelectorAll('[name=\'postulante_ids[]\']').forEach(c=>c.checked=true)">Seleccionar todos</button>
      </div>
    </form>
    @endif
  </div>
</div>
@endcan

{{-- Postulantes ya inscritos --}}
<div class="card" style="max-width:900px">
  <div class="card-hd">
    <i class="fas fa-list"></i>Postulantes en {{ $grupo->codigo }} ({{ $grupo->postulantes->count() }}/{{ $grupo->capacidad_maxima }})
    <a href="{{ route('grupos.estudiantes',$grupo) }}" style="float:right;font-size:.8rem"><i class="fas fa-external-link-alt"></i> Ver lista completa</a>
  </div>
  <div class="card-bd">
    @if($grupo->postulantes->isEmpty())
      <p style="color:var(--t3);text-align:center;padding:.75rem">Sin postulantes inscritos.</p>
    @else
    <table class="ct">
      <thead><tr><th>CI</th><th>Nombre</th><th>1ª Opción</th><th>Estado</th></tr></thead>
      <tbody>
        @foreach($grupo->postulantes as $p)
        <tr>
          <td style="font-family:'Courier New',monospace;font-size:.83rem">{{ $p->ci }}</td>
          <td>{{ $p->nombre_completo }}</td>
          <td>{{ $p->primeraOpcion?->sigla ?? '—' }}</td>
          <td><span class="bg {{ in_array($p->estado,['aprobado','admitido','admitido_segunda_opcion'])?'bv':($p->estado==='en_curso'?'bna':'bg2') }}">{{ ucfirst(str_replace('_',' ',$p->estado)) }}</span></td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif
  </div>
</div>

<div style="margin-top:1rem;display:flex;gap:.75rem">
  @can('eliminar grupos')
  <form action="{{ route('grupos.destroy',$grupo) }}" method="POST" style="display:inline">
    @csrf @method('DELETE')
    <button class="btn bdr bsm" onclick="return confirm('¿Eliminar el grupo {{ $grupo->codigo }}?')"><i class="fas fa-trash"></i> Eliminar</button>
  </form>
  @endcan
  <a href="{{ route('grupos.index') }}" class="btn bo2"><i class="fas fa-arrow-left"></i> Volver a Grupos</a>
</div>
@endsection
