<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    use HasFactory;

    protected $fillable = [
        'residente_id',
        'nombre_visitante',
        'ci_visitante',
        'placa_vehiculo',
        'motivo',
        'fecha_inicio',
        'fecha_fin',
        'codigo',
        'estado',
        'hora_entrada',
        'hora_salida',
        'user_entrada_id',
        'user_salida_id',
        'observaciones'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'hora_entrada' => 'datetime',
        'hora_salida' => 'datetime',
    ];

    // Relaciones
    public function residente()
    {
        return $this->belongsTo(Residente::class);
    }

    public function userEntrada()
    {
        return $this->belongsTo(User::class, 'user_entrada_id');
    }

    public function userSalida()
    {
        return $this->belongsTo(User::class, 'user_salida_id');
    }

    // Scopes útiles
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeEnCurso($query)
    {
        return $query->where('estado', 'en_curso');
    }

    public function scopeFinalizadas($query)
    {
        return $query->where('estado', 'finalizada');
    }
}