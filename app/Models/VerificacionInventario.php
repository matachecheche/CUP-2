<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificacionInventario extends Model
{
    use HasFactory;

    protected $table = 'verificacion_inventarios';

    protected $fillable = [
        'reserva_id',
        'inventario_id',
        'estado',
        'observacion',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }
}