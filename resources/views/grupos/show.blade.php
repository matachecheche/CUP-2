@extends('layouts.ap')
@section('title','Gestionar Grupo')
@section('content')
<div class="ph">
  <h1>Grupo {{ $grupo->codigo }}</h1>
  <p class="sub">
    CU-11 Editar horario/modalidad e inscribir postulantes ·
    CU-12 Asignar docente y validar cruces de horario
  </p>
  <ol class="bc">
    <li><a href="{{ route('panel') }}">Inicio</a></li>
    <li><a href="{{ route('grupos.index') }}">Grupos</a></li>
    <li>{{ $grupo->codigo }}</li>
  </ol>
</div>

@if(session('success'))<div class="al al-v" style="margin-bottom:1rem"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
@if(session('error'))<div class="al al-d" style="margin-bottom:1rem"><i class="fas fa-times-circle"></i> {{ session('error') }}</div>@endif
@if($errors->any())<div class="al al-d" style="margin-bottom:1rem"><ul style="margin:0;padding-left:1.2rem">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

{{-- Fila superior: Info + Editar horario/modalidad --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;max-width:900px;margin-bottom:1.25rem">

  {{-- Info básica --}}
  <div class="card">
    <div class="card-hd"><i class="fas fa-info-circle"></i>Datos del grupo</div>
    <div class="card-bd" style="font-size:.88rem">
      @foreach(['Código'=>$grupo->codigo,'Turno'=>ucfirst($grupo->turno),'Modalidad'=>ucfirst($grupo->modalidad),'Capacidad'=>$grupo->capacidad_maxima,'Postulantes'=>$grupo->postulantes->count(),'Gestión'=>$grupo->gestion?->descripcion] as $l=>$v)
      <div style="display:flex;justify-content:space-between;padding:.35rem 0;border-bottom:1px solid var(--cr2)">
        <span style="color:var(--t3)">{{ $l }}</span>
        <span style="font-weight:500">{{ $v??'—' }}</span>
      </div>
      @endforeach
    </div>
  </div>

  {{-- CU-11: Editar horario y modalidad --}}
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

{{-- CU-12: Asignar docente con validación de cruce --}}
@can('editar grupos')
<div class="card" style="max-width:900px;margin-bottom:1.25rem">
  <div class="card-hd"><i class="fas fa-user-tie"></i>CU-12 — Asignar docente a materia (con validación de cruces)</div>
  <div class="card-bd">
    <form action="{{ route('grupos.asignarDocente',$grupo) }}" method="POST" novalidate>
      @csrf
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:.75rem">
        <div>
          <label class="fl">Materia <span class="rq">*</span></label>
          <select name="materia_id" class="fs @error('materia_id') is-invalid @enderror" required>
            <option value="">— Seleccionar —</option>
            @foreach($materias as $m)
            <option value="{{ $m->id }}" {{ old('materia_id')==$m->id?'selected':'' }}>
              {{ $m->nombre }}
              @php $ya = $grupo->asignaciones->firstWhere('materia_id',$m->id); @endphp
              @if($ya) (ya asignado: {{ $ya->docente?->apellidos }}) @endif
            </option>
            @endforeach
          </select>
          @error('materia_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="fl">Docente <span class="rq">*</span></label>
          <select name="docente_id" class="fs @error('docente_id') is-invalid @enderror" required>
            <option value="">— Seleccionar —</option>
            @foreach($docentes as $d)
            <option value="{{ $d->id }}" {{ old('docente_id')==$d->id?'selected':'' }}>
              {{ $d->apellidos }}, {{ $d->nombres }}
              — {{ $d->area_formacion ?? '' }}
            </option>
            @endforeach
          </select>
          @error('docente_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:.75rem;margin-bottom:.75rem">
        <div>
          <label class="fl">Día <span class="rq">*</span></label>
          <select name="dia" class="fs @error('dia') is-invalid @enderror" required>
            <option value="">— Día —</option>
            @foreach(['lunes','martes','miercoles','jueves','viernes','sabado'] as $dia)
            <option value="{{ $dia }}" {{ old('dia')===$dia?'selected':'' }}>{{ ucfirst($dia) }}</option>
            @endforeach
          </select>
          @error('dia')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="fl">Hora inicio <span class="rq">*</span></label>
          <input type="time" name="hora_inicio"
                 class="fc @error('hora_inicio') is-invalid @enderror"
                 value="{{ old('hora_inicio','07:00') }}" required>
          @error('hora_inicio')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="fl">Hora fin <span class="rq">*</span></label>
          <input type="time" name="hora_fin"
                 class="fc @error('hora_fin') is-invalid @enderror"
                 value="{{ old('hora_fin','09:00') }}" required>
          @error('hora_fin')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="fl">Aula</label>
          <input type="text" name="aula"
                 class="fc @error('aula') is-invalid @enderror"
                 value="{{ old('aula') }}" maxlength="30" pattern="[A-Za-z0-9\-]+"
                 placeholder="Ej: A-101">
          @error('aula')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
      </div>
      <div class="al al-w" style="margin-bottom:.75rem;font-size:.82rem">
        <i class="fas fa-shield-alt"></i>
        El sistema <strong>rechazará</strong> la asignación si: (a) hay cruce de horario,
        (b) el docente ya tiene 4 grupos, o (c) el docente no pertenece al área de la materia.
      </div>
      <button type="submit" class="btn bp bsm"><i class="fas fa-user-plus"></i> Asignar docente</button>
    </form>
  </div>
</div>
@endcan

{{-- Tabla asignaciones actuales (CU-12) --}}
<div class="card" style="max-width:900px;margin-bottom:1.25rem">
  <div class="card-hd">
    <i class="fas fa-table"></i>Asignaciones actuales
    <span style="font-weight:normal;font-size:.78rem;margin-left:.5rem">
      ({{ $grupo->asignaciones->count() }}/4 materias asignadas)
    </span>
  </div>
  <div class="card-bd">
    @if($grupo->asignaciones->isEmpty())
      <p style="color:var(--t3);text-align:center;padding:.75rem">Sin asignaciones. Usa el formulario de arriba.</p>
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
          <td>{{ $a->hora_inicio }} — {{ $a->hora_fin }}</td>
          <td>{{ $a->aula ?? '—' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif
  </div>
</div>

{{-- CU-11: Inscribir postulantes --}}
@can('editar grupos')
<div class="card" style="max-width:900px;margin-bottom:1.25rem">
  <div class="card-hd"><i class="fas fa-users"></i>CU-11 — Inscribir postulantes al grupo</div>
  <div class="card-bd">
    @if($sinGrupo->isEmpty())
      <div class="al al-v" style="margin-bottom:.5rem">
        <i class="fas fa-check-circle"></i> Todos los postulantes ya están asignados a un grupo.
      </div>
    @else
    <form action="{{ route('grupos.inscribirPostulantes',$grupo) }}" method="POST">
      @csrf
      <p style="font-size:.84rem;color:var(--t3);margin-bottom:.75rem">
        Capacidad disponible: <strong>{{ $grupo->capacidad_maxima - $grupo->postulantes->count() }}</strong> lugares.
        Selecciona los postulantes a inscribir:
      </p>
      <div style="max-height:260px;overflow-y:auto;border:1px solid var(--cr2);border-radius:.4rem;padding:.5rem;margin-bottom:.75rem">
        @foreach($sinGrupo as $p)
        <label class="fck" style="padding:.3rem .25rem;border-bottom:1px solid var(--cr2)">
          <input type="checkbox" name="postulante_ids[]" value="{{ $p->id }}">
          <span style="font-size:.84rem">
            <strong>{{ $p->ci }}</strong> — {{ $p->nombre_completo }}
            <span style="color:var(--t3)">· {{ $p->primeraOpcion?->sigla }}</span>
          </span>
        </label>
        @endforeach
      </div>
      <div style="display:flex;gap:.5rem;align-items:center">
        <button type="submit" class="btn bp bsm"><i class="fas fa-user-check"></i> Inscribir seleccionados</button>
        <button type="button" class="btn bo2 bsm"
                onclick="document.querySelectorAll('[name=\'postulante_ids[]\']').forEach(c=>c.checked=true)">
          Seleccionar todos
        </button>
      </div>
    </form>
    @endif
  </div>
</div>
@endcan

{{-- Postulantes ya inscritos en este grupo --}}
<div class="card" style="max-width:900px">
  <div class="card-hd">
    <i class="fas fa-list"></i>Postulantes en {{ $grupo->codigo }}
    ({{ $grupo->postulantes->count() }}/{{ $grupo->capacidad_maxima }})
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
          <td><span class="bg {{ in_array($p->estado,['aprobado','admitido','admitido_segunda_opcion'])?'bv':($p->estado==='en_curso'?'bna':'bg2') }}">
            {{ ucfirst(str_replace('_',' ',$p->estado)) }}
          </span></td>
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
    <button class="btn bdr bsm" onclick="return confirm('¿Eliminar el grupo {{ $grupo->codigo }}?')">
      <i class="fas fa-trash"></i> Eliminar
    </button>
  </form>
  @endcan
  <a href="{{ route('grupos.index') }}" class="btn bo2"><i class="fas fa-arrow-left"></i> Volver a Grupos</a>
</div>
@endsection
