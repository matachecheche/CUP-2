@extends('layouts.ap')
@section('title',$docente->nombre_completo)
@section('content')
<div class="ph"><h1>{{ $docente->nombre_completo }}</h1><p class="sub">CU-10 — Perfil profesional del docente</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li><a href="{{ route('docentes.index') }}">Docentes</a></li><li>Perfil</li></ol></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;max-width:780px">
<div class="card"><div class="card-hd"><i class="fas fa-user"></i>Datos personales</div><div class="card-bd" style="font-size:.88rem">
@foreach(['CI'=>$docente->ci,'Nombres'=>$docente->nombres,'Apellidos'=>$docente->apellidos,'Teléfono'=>$docente->telefono,'Email'=>$docente->email,'Área de formación'=>$docente->area_formacion,'Estado'=>null] as $lbl=>$v)
@if($lbl==='Estado')
<div style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--cr2)">
<span style="color:var(--t3)">Estado</span>
<span class="bg {{ $docente->estado?'bv':'bg2' }}">{{ $docente->estado?'Activo':'Inactivo' }}</span>
</div>
@else
<div style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--cr2)">
<span style="color:var(--t3)">{{ $lbl }}</span><span style="font-weight:500">{{ $v??'—' }}</span>
</div>
@endif
@endforeach
</div></div>
<div class="card"><div class="card-hd"><i class="fas fa-certificate"></i>Perfil profesional</div><div class="card-bd" style="font-size:.88rem">
@foreach(['Título profesional'=>$docente->titulo_profesional,'Maestría'=>$docente->maestria,'Diplomado en Edu. Superior'=>$docente->diplomado_educacion_superior,'Certif. de Inglés'=>$docente->certificacion_ingles,'Otras certificaciones'=>$docente->otras_certificaciones] as $l=>$v)
@if($v)
<div style="margin-bottom:.75rem">
<div style="font-size:.72rem;text-transform:uppercase;color:var(--t3);margin-bottom:.2rem">{{ $l }}</div>
<div style="font-weight:600">{{ $v }}</div>
</div>
@endif
@endforeach
</div></div>
</div>
<div style="margin-top:1rem;display:flex;gap:.75rem">
@can('editar docentes')<a href="{{ route('docentes.edit',$docente) }}" class="btn bw"><i class="fas fa-edit"></i> Editar</a>@endcan
<a href="{{ route('docentes.index') }}" class="btn bo2"><i class="fas fa-arrow-left"></i> Volver</a>
</div>
@endsection
