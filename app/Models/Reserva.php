<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;
    protected $table = 'reservas';
    protected $casts = [
        'fecha' => 'date',
    ];
    protected $fillable = [
        'fecha',
        'hora_inicio',
        'hora_fin',
        'estado',
        'observacion',
        'monto_total',
        'area_comun_id',
        'residente_id'
    ];

    public function areaComun()
    {
        return $this->belongsTo(AreaComun::class, 'area_comun_id');
    }
    public function residente()
    {
        return $this->belongsTo(Residente::class, 'residente_id');
    }
    public function verificaciones()
    {
        return $this->hasMany(VerificacionInventario::class);
    }
}
