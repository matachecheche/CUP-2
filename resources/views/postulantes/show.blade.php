@extends('layouts.ap')
@section('title',$postulante->nombre_completo)
@section('content')
<div class="ph"><h1>{{ $postulante->nombre_completo }}</h1><p class="sub">CU-05 — Estado del postulante</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li><a href="{{ route('postulantes.index') }}">Postulantes</a></li><li>Detalle</li></ol></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;max-width:840px">
<div class="card"><div class="card-hd"><i class="fas fa-user"></i>Datos personales</div><div class="card-bd" style="font-size:.87rem">
@foreach(['CI'=>$postulante->ci,'Nombres'=>$postulante->nombres,'Apellidos'=>$postulante->apellidos,'Fecha de nac.'=>$postulante->fecha_nacimiento?->format('d/m/Y'),'Sexo'=>$postulante->sexo,'Teléfono'=>$postulante->telefono,'Correo'=>$postulante->email,'Colegio'=>$postulante->colegio_procedencia,'Ciudad'=>$postulante->ciudad,'Dirección'=>$postulante->direccion] as $l=>$v)
<div style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--cr2)">
<span style="color:var(--t3)">{{ $l }}</span><span style="font-weight:500">{{ $v??'—' }}</span></div>
@endforeach
</div></div>
<div>
<div class="card" style="margin-bottom:1rem"><div class="card-hd"><i class="fas fa-graduation-cap"></i>Opciones de carrera</div><div class="card-bd" style="font-size:.88rem">
<div style="margin-bottom:.6rem"><span style="font-size:.72rem;text-transform:uppercase;color:var(--t3)">1ª OPCIÓN</span>
<div style="font-weight:600;color:var(--v)">{{ $postulante->primeraOpcion?->nombre??'—' }}</div></div>
<div><span style="font-size:.72rem;text-transform:uppercase;color:var(--t3)">2ª OPCIÓN</span>
<div style="font-weight:600">{{ $postulante->segundaOpcion?->nombre??'—' }}</div></div>
</div></div>
<div class="card" style="margin-bottom:1rem"><div class="card-hd"><i class="fas fa-file-check"></i>Documentos (CU-05)</div><div class="card-bd" style="font-size:.88rem;display:flex;flex-direction:column;gap:.4rem">
@foreach(['doc_ci'=>'CI','doc_libreta_colegio'=>'Libreta de colegio','doc_titulo_bachiller'=>'Título de Bachiller'] as $col=>$lbl)
<div><i class="fas fa-{{ $postulante->$col ? 'check-circle':'times-circle' }}" style="color:{{ $postulante->$col ? 'var(--v3)':'var(--d)' }}"></i> {{ $lbl }}</div>
@endforeach
</div></div>
<div class="card"><div class="card-hd"><i class="fas fa-info-circle"></i>Estado (CU-05)</div><div class="card-bd" style="text-align:center;padding:1.5rem">
<span class="bg {{ $postulante->estado_badge }}" style="font-size:.9rem;padding:.4rem 1rem">
{{ ucfirst(str_replace('_',' ',$postulante->estado)) }}</span>
@if($postulante->promedio_general)
<div style="margin-top:.75rem;font-size:.88rem;color:var(--t3)">Promedio: <strong>{{ number_format($postulante->promedio_general,2) }}</strong></div>
@endif
</div></div>
</div></div>

{{-- CU-05: Validar requisitos --}}
@can("editar postulantes")
<div class="card" style="max-width:840px;margin-top:1rem">
  <div class="card-hd"><i class="fas fa-clipboard-check"></i>Validar requisitos — CU-05</div>
  <div class="card-bd">
    @if(!$postulante->tieneDocumentos())
    <div class="al al-w" style="margin-bottom:.75rem">
      <i class="fas fa-exclamation-triangle"></i>
      Faltan documentos. El postulante no puede acceder al CUP hasta completarlos.
    </div>
    @else
    <div class="al al-v" style="margin-bottom:.75rem">
      <i class="fas fa-check-circle"></i>
      Todos los documentos presentados. Postulante habilitado.
    </div>
    @endif
    <form action="{{ route("postulantes.validar",$postulante) }}" method="POST">
      @csrf @method("PATCH")
      <div style="display:flex;flex-direction:column;gap:.5rem;margin-bottom:1rem">
        <label class="fck">
          <input type="checkbox" name="doc_ci" value="1" {{ $postulante->doc_ci?"checked":"" }}>
          <span>Fotocopia de Cédula de Identidad (CI)</span>
        </label>
        <label class="fck">
          <input type="checkbox" name="doc_libreta_colegio" value="1" {{ $postulante->doc_libreta_colegio?"checked":"" }}>
          <span>Libreta de colegio</span>
        </label>
        <label class="fck">
          <input type="checkbox" name="doc_titulo_bachiller" value="1" {{ $postulante->doc_titulo_bachiller?"checked":"" }}>
          <span>Título de Bachiller</span>
        </label>
      </div>
      <button type="submit" class="btn bp bsm"><i class="fas fa-save"></i> Guardar validación</button>
    </form>
  </div>
</div>
@endcan

<div style="margin-top:1rem;display:flex;gap:.75rem">
@can('editar postulantes')<a href="{{ route('postulantes.edit',$postulante) }}" class="btn bw"><i class="fas fa-edit"></i> Editar</a>@endcan
<a href="{{ route('postulantes.index') }}" class="btn bo2"><i class="fas fa-arrow-left"></i> Volver</a></div>
@endsection
