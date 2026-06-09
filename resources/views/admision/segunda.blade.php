@extends('layouts.ap')
@section('title','CU-17 · Segunda opción — Asignación Manual')
@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
<style>
  .cu17-badge-lleno  { background:#fdecea; color:#92271d; border:1px solid #f5c6c2; border-radius:4px; padding:2px 8px; font-size:.78rem; font-weight:600; }
  .cu17-badge-libre  { background:#e8f6ee; color:#14532d; border:1px solid #bbe5c8; border-radius:4px; padding:2px 8px; font-size:.78rem; }
  .cu17-card-alerta  { border-left:4px solid #c0392b; background:#fff9f9; margin-bottom:1rem; }
  .cu17-card-bloq    { border-left:4px solid #b0b0b0; background:#f8f8f8; margin-bottom:1rem; }
  .cu17-btn-asignar  { background:#2563eb; color:#fff; border:none; border-radius:5px; padding:.35rem .9rem; font-size:.84rem; cursor:pointer; transition:background .15s; }
  .cu17-btn-asignar:hover { background:#1749b8; }
  .cu17-btn-asignar:disabled { background:#9bb4e8; cursor:not-allowed; }
  .cu17-tr-puede     { background:#f0fdf4 !important; }
  .cu17-tr-nopuede   { background:#fff9f9 !important; }
  .cu17-contador     { font-weight:700; color:#2563eb; }
</style>
@endpush
@section('content')

<div class="ph">
  <h1>CU-17 · Reasignación a Segunda Opción <small style="font-size:.6em;color:#888">(Manual)</small></h1>
  <p class="sub">Asigna individualmente a cada postulante sin cupo en su 1ª opción — {{ $gestion->descripcion }}</p>
  <ol class="bc">
    <li><a href="{{ route('panel') }}">Inicio</a></li>
    <li><a href="{{ route('admision.index') }}">Admisión</a></li>
    <li>Paso 2 · CU-17</li>
  </ol>
</div>

@if(session('success'))
  <div style="background:#e8f6ee;color:#14532d;border:1px solid #bbe5c8;border-radius:6px;padding:.7rem 1rem;margin-bottom:1rem">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
  </div>
@endif
@if(session('error'))
  <div style="background:#fdecea;color:#92271d;border:1px solid #f5c6c2;border-radius:6px;padding:.7rem 1rem;margin-bottom:1rem">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
  </div>
@endif

@include('admision._cupos')

@if($hayCarreraLlena)

  {{-- Banner: carreras con cupos llenos --}}
  <div class="card cu17-card-alerta" style="margin-bottom:1rem">
    <div class="card-bd">
      <strong><i class="fas fa-lock"></i> Carrera(s) con cupos 100% ocupados:</strong>
      <span style="margin-left:.5rem">
        @foreach($carrerasLlenas as $cl)
          <span class="cu17-badge-lleno" style="margin-right:.3rem">
            {{ $cl['carrera'] }} ({{ $cl['ocupados'] }}/{{ $cl['cupo'] }})
          </span>
        @endforeach
      </span>
      <p style="font-size:.83rem;color:#7d2c2c;margin:.4rem 0 0">
        Los postulantes con 1ª opción en estas carreras no pudieron ser admitidos.
        Puedes asignarles su 2ª opción si tiene cupos disponibles.
      </p>
    </div>
  </div>

  @if($candidatos->isEmpty())
    <div class="card" style="margin-bottom:1rem">
      <div class="card-bd">
        <p style="font-size:.9rem">
          <i class="fas fa-check-circle" style="color:#16a34a"></i>
          No hay postulantes pendientes de 2ª opción.
          Si el paso 1 (CU-16) aún no se ejecutó, ve a <a href="{{ route('admision.primera') }}">CU-16</a>.
        </p>
      </div>
    </div>
  @else
    @php
      $conCupo    = $candidatos->where('puede_asignar', true)->count();
      $sinCupo    = $candidatos->where('puede_asignar', false)->count();
    @endphp

    <div class="card" style="margin-bottom:1rem">
      <div class="card-hd">
        <i class="fas fa-user-check"></i>
        Candidatos para asignación de 2ª opción
        (<span class="cu17-contador">{{ $candidatos->count() }}</span> pendientes)
      </div>
      <div class="card-bd">

        <p style="font-size:.83rem;color:var(--t3,#8a8678);margin-bottom:.8rem">
          <i class="fas fa-info-circle"></i>
          Haz clic en <strong>Asignar 2ª opción</strong> para asignar a cada postulante individualmente.
          Filas en <span style="background:#f0fdf4;padding:1px 5px;border-radius:3px">verde</span>
          tienen cupo disponible; en
          <span style="background:#fff9f9;padding:1px 5px;border-radius:3px">rojo</span> no tienen cupo.
        </p>

        <div class="tw">
          <table id="tbl-candidatos" class="ct" style="width:100%">
            <thead>
              <tr>
                <th>#</th>
                <th>CI</th>
                <th>Postulante</th>
                <th>Promedio</th>
                <th>2ª Opción</th>
                <th>Cupos libres</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody>
              @foreach($candidatos as $p)
              <tr class="{{ $p->puede_asignar ? 'cu17-tr-puede' : 'cu17-tr-nopuede' }}" id="row-post-{{ $p->id }}">
                <td style="color:var(--t3);font-size:.8rem">{{ $loop->iteration }}</td>
                <td style="font-family:'Courier New',monospace;font-size:.84rem">{{ $p->ci }}</td>
                <td><strong>{{ $p->apellidos }}</strong>, {{ $p->nombres }}</td>
                <td><strong>{{ $p->promedio_general }}</strong></td>
                <td style="font-size:.84rem">
                  {{ $p->segundaOpcion?->nombre ?? '—' }}
                  <span style="font-size:.75rem;color:#888">({{ $p->segundaOpcion?->sigla }})</span>
                </td>
                <td>
                  @if($p->puede_asignar)
                    <span class="cu17-badge-libre">{{ $p->cupos_segunda }} libres</span>
                  @else
                    <span class="cu17-badge-lleno">Sin cupo</span>
                  @endif
                </td>
                <td>
                  @if($p->puede_asignar)
                    <form method="POST"
                          action="{{ route('admision.segunda.asignar', $p->id) }}"
                          id="form-asignar-{{ $p->id }}">
                      @csrf
                      <button type="button"
                              class="cu17-btn-asignar"
                              id="btn-{{ $p->id }}"
                              data-id="{{ $p->id }}"
                              data-nombre="{{ $p->nombre_completo }}"
                              data-carrera="{{ $p->segundaOpcion?->nombre ?? '' }}"
                              onclick="confirmarAsignacion(this)">
                        <i class="fas fa-check"></i> Asignar 2ª opción
                      </button>
                    </form>
                  @else
                    <button class="cu17-btn-asignar" disabled title="La 2ª opción no tiene cupos">
                      <i class="fas fa-ban"></i> Sin cupo
                    </button>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- Botón asignación masiva --}}
        @if($conCupo > 0)
          <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid #e5e2d9;display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
            <div>
              <strong>Asignación masiva:</strong>
              <span style="font-size:.84rem;color:var(--t3)">
                Asigna de una vez a los <strong>{{ $conCupo }}</strong> postulante(s) con cupo en 2ª opción.
                Los restantes <strong>{{ $sinCupo }}</strong> quedarán como <em>no admitidos</em>.
              </span>
            </div>
            <form method="POST" action="{{ route('admision.segunda.procesar') }}" id="form-masiva">
              @csrf
              <button type="button"
                      class="btn bp"
                      data-con="{{ $conCupo }}"
                      data-sin="{{ $sinCupo }}"
                      onclick="confirmarMasiva(this)">
                <i class="fas fa-exchange-alt"></i> Asignar todos con cupo ({{ $conCupo }})
              </button>
            </form>
          </div>
        @endif

      </div>
    </div>
  @endif

@else
  <div class="card cu17-card-bloq" style="margin-bottom:1rem">
    <div class="card-bd">
      <p style="margin:0">
        <i class="fas fa-lock" style="color:#999"></i>
        <strong>CU-17 no disponible aún.</strong>
        La asignación de segunda opción solo se habilita cuando
        <strong>al menos una carrera tiene todos sus cupos ocupados</strong>.<br>
        <span style="font-size:.84rem;color:var(--t3)">
          Ejecuta primero el <a href="{{ route('admision.primera') }}">Paso 1 (CU-16)</a>
          para completar los cupos de la primera opción.
        </span>
      </p>
    </div>
  </div>
@endif

@if($reasignados->isNotEmpty())
<div class="card" style="margin-bottom:1rem">
  <div class="card-hd">
    <i class="fas fa-clipboard-check"></i>
    Resultados de 2ª opción ya procesados ({{ $reasignados->count() }})
  </div>
  <div class="card-bd">
    <div class="tw">
      <table class="ct" style="width:100%">
        <thead>
          <tr><th>#</th><th>CI</th><th>Postulante</th><th>Promedio</th><th>Resultado</th></tr>
        </thead>
        <tbody>
          @foreach($reasignados as $a)
          <tr>
            <td style="color:var(--t3);font-size:.8rem">{{ $loop->iteration }}</td>
            <td style="font-family:'Courier New',monospace;font-size:.84rem">{{ $a->postulante?->ci }}</td>
            <td><strong>{{ $a->postulante?->apellidos }}</strong>, {{ $a->postulante?->nombres }}</td>
            <td>{{ $a->promedio_general }}</td>
            <td>
              @if($a->resultado === 'admitido_segunda')
                <span class="bg bv"><i class="fas fa-check"></i> Admitido 2ª — {{ $a->carreraAsignada?->nombre }}</span>
              @else
                <span class="bg bd"><i class="fas fa-times"></i> No admitido</span>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <a class="btn bsm bo2" style="margin-top:.8rem" href="{{ route('admision.publicacion') }}">
      Continuar al paso 3 (CU-18) <i class="fas fa-arrow-right"></i>
    </a>
  </div>
</div>
@endif

@push('js')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function () {
  if (document.getElementById('tbl-candidatos')) {
    $('#tbl-candidatos').DataTable({
      language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
      order: [[3, 'desc']],
      pageLength: 15,
      columnDefs: [{ orderable: false, targets: 6 }]
    });
  }
});

function confirmarAsignacion(btn) {
  var id      = btn.getAttribute('data-id');
  var nombre  = btn.getAttribute('data-nombre');
  var carrera = btn.getAttribute('data-carrera');
  if (!confirm('Asignar a ' + nombre + '\na la carrera: ' + carrera + '?')) return;
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Asignando...';
  document.getElementById('form-asignar-' + id).submit();
}

function confirmarMasiva(btn) {
  var con = btn.getAttribute('data-con');
  var sin = btn.getAttribute('data-sin');
  if (!confirm('Asignar masivamente a ' + con + ' postulante(s) con cupo en 2a opcion.\n\nLos ' + sin + ' sin cupo quedaran como NO ADMITIDOS.\n\n Continuar?')) return;
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
  document.getElementById('form-masiva').submit();
}
</script>
@endpush
@endsection
