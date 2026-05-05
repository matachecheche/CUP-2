<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'cuota_id',
        'monto_pagado',
        'fecha_pago',
        'metodo',
        'estado',
        'comprobante',
        'observacion',
        'user_id',
        'multa_id',
    ];

public function cuota()
{
    return $this->belongsTo(Cuota::class, 'cuota_id');
}
public function multa()
{
    return $this->belongsTo(Multa::class, 'multa_id');
}

public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}


    public function residente()
    {
        return $this->hasOneThrough(
            Residente::class,
            Cuota::class,
            'id',
            'id',
            'cuota_id',
            'residente_id'
        );
    }
}
