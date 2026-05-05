<?php
// app/Models/Unidad.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Residente;

class Unidad extends Model
{
    use HasFactory;

    /**
     * Los atributos que pueden asignarse masivamente.
     *
     * @var array<int,string>
     */
    protected $table = 'unidades';
    protected $fillable = [
        'codigo',
        'placa',
        'marca',
        'capacidad',
        'estado',
        'personas_por_unidad',
        'tiene_mascotas',
        'vehiculos',
        'residente_id',
    ];

    /**
     * Conversiones de tipo para atributos.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'capacidad'            => 'integer',
        'personas_por_unidad'  => 'integer',
        'tiene_mascotas'       => 'boolean',
        'vehiculos'            => 'integer',
    ];

    /**
     * Cada Unidad pertenece a un único Residente.
     */
    public function residente()
    {
        return $this->belongsTo(Residente::class);
    }

    /**
     * Scope: sólo unidades con estado 'activa'.
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }
}
