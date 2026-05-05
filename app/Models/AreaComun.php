<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaComun extends Model
{
    use HasFactory;
    protected $table = 'area_comuns';

    protected $fillable = [
        'nombre',
        'monto',
        'estado'
    ];

    public function reserva()
    {
        return $this->hasMany(Reserva::class, 'area_comun_id');
    }
    public function inventarios()
{
    return $this->hasMany(Inventario::class);
}

}
