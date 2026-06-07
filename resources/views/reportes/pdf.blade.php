<!DOCTYPE html><html lang="es"><head><meta charset="utf-8">
<style>
  body{font-family:DejaVu Sans, sans-serif;font-size:10px;color:#222;margin:24px}
  .memb{border-bottom:3px solid #1d3b2a;padding-bottom:8px;margin-bottom:12px}
  .memb h1{margin:0;font-size:15px;color:#1d3b2a}.memb p{margin:2px 0 0;font-size:9px;color:#666}
  h2{font-size:13px;margin:8px 0 2px}.sub{font-size:9px;color:#666;margin:0 0 10px}
  table{width:100%;border-collapse:collapse}th{background:#1d3b2a;color:#fff;padding:5px 6px;font-size:9px;text-align:left}
  td{border-bottom:1px solid #ddd;padding:4px 6px;font-size:9px}tr:nth-child(even) td{background:#f6f4ee}
  .pie{margin-top:14px;font-size:8px;color:#888;text-align:right}
</style></head><body>
<div class="memb">
  <h1>FICCT — Sistema de Admisión CUP</h1>
  <p>Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones · U.A.G.R.M. · Santa Cruz, Bolivia</p>
</div>
<h2>{{ $titulo }}</h2><p class="sub">{{ $subtitulo }}</p>
<table><thead><tr>@foreach($columnas as $c)<th>{{ $c }}</th>@endforeach</tr></thead>
<tbody>@foreach($filas as $f)<tr>@foreach($f as $v)<td>{{ $v }}</td>@endforeach</tr>@endforeach</tbody></table>
<div class="pie">Total de registros: {{ count($filas) }} · Documento generado automáticamente — CU-19</div>
</body></html>
