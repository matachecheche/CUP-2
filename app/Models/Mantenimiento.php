<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'descripcion', 'estado', 'fecha_hora', 'monto',
        'usuario_id', 'empresaExterna_id', 'gasto_id', 'pago_id'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function empresa()
    {
        return $this->belongsTo(EmpresaExterna::class, 'empresaExterna_id');
    }

    public function gasto()
    {
        return $this->belongsTo(Gasto::class);
    }

    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }
}
