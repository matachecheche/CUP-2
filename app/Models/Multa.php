<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Multa extends Model
{
    protected $table = 'multas';

    protected $fillable = [
        'motivo',
        'monto',
        'fechaEmision',
        'fechaLimite',
        'estado',
        'residente_id',
        'empleado_id',
        'cuota_id',
        'id',
    ];

    public function residente()
    {
        return $this->belongsTo(Residente::class);
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function cuota()
    {
        return $this->belongsTo(Cuota::class);
    }
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'multa_id');
    }
}
