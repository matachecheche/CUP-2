@extends('layouts.ap')
@section('title','Registro de Notas')
@section('content')
<div class="ph">
  <h1>Registro de Notas</h1>
  <p class="sub">CU-13 Registrar notas · CU-14 Nota final · Promedio · Estado · CU-15 Consultar</p>
  <ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li>Notas</li></ol>
</div>

{{-- Selector grupo / materia --}}
<form method="GET" action="{{ route('notas.index') }}" style="display:flex;gap:.75rem;margin-bottom:1.25rem;flex-wrap:wrap">
  <select name="grupo_id" class="fs" style="width:200px" onchange="this.form.submit()">
    <option value="">— Grupo —</option>
    @foreach($grupos as $g)<option value="{{ $g->id }}" {{ ($grupoSel?->id==$g->id)?'selected':'' }}>{{ $g->codigo }} · {{ ucfirst($g->turno) }}</option>@endforeach
  </select>
  <select name="materia_id" class="fs" style="width:200px" onchange="this.form.submit()">
    <option value="">— Materia —</option>
    @foreach($materias as $m)<option value="{{ $m->id }}" {{ ($matSel?->id==$m->id)?'selected':'' }}>{{ $m->nombre }}</option>@endforeach
  </select>
</form>

@if(session('success'))<div class="al al-v"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
@if($errors->any())<div class="al al-d"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

@if($grupoSel && $matSel)
<div class="card">
  <div class="card-hd"><i class="fas fa-table"></i>Notas — {{ $grupoSel->codigo }} / {{ $matSel->nombre }}
    <span style="font-size:.75rem;font-weight:normal;margin-left:.5rem">({{ $matSel->pond_examen1 }}% + {{ $matSel->pond_examen2 }}% + {{ $matSel->pond_examen3 }}%)</span>
  </div>
  <div class="card-bd">
  @if($notas->isEmpty() && $sinNota->isEmpty())
    <p style="color:var(--t3);text-align:center">No hay postulantes inscritos en este grupo.</p>
  @else
  <table class="ct"><thead><tr><th>Postulante</th><th>CI</th><th>Ex. 1</th><th>Ex. 2</th><th>Ex. 3</th><th>Nota Final</th><th>Estado</th><th></th></tr></thead>
  <tbody>
  @foreach($notas as $n)
  <tr>
    <td>{{ $n->postulante?->nombre_completo }}</td>
    <td>{{ $n->postulante?->ci }}</td>
    <td>{{ $n->examen1 }}</td>
    <td>{{ $n->examen2 }}</td>
    <td>{{ $n->examen3 }}</td>
    <td><strong>{{ $n->nota_final }}</strong></td>
    <td><span class="bg {{ $n->aprobado?'bv':'bd' }}">{{ $n->aprobado?'Aprobado':'Reprobado' }}</span></td>
    <td><a href="{{ route('notas.edit',$n) }}" class="btn bw bsm"><i class="fas fa-edit"></i></a></td>
  </tr>
  @endforeach
  @foreach($sinNota as $p)
  <tr style="background:var(--cr2)">
    <td>{{ $p->nombre_completo }}</td>
    <td>{{ $p->ci }}</td>
    <td colspan="4" style="color:var(--t3);font-size:.82rem">Sin notas registradas</td>
    <td>
      @can('crear notas')
      <a href="{{ route('notas.create',['postulante_id'=>$p->id,'grupo_id'=>$grupoSel->id,'materia_id'=>$matSel->id]) }}" class="btn bp bsm"><i class="fas fa-plus"></i> Registrar</a>
      @endcan
    </td>
  </tr>
  @endforeach
  </tbody></table>
  @endif
  </div>
</div>
@endif
@endsection
