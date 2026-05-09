<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'fecha_adquisicion',
        'tipo_adquisicion',
        'valor_estimado',
        'vida_util',
        'valor_residual',
        'fecha_baja',
        'motivo_baja',
        'ubicacion',
        'categoria_id',
        'user_id',
        'area_comun_id',
    ];

    protected $casts = [
        'fecha_adquisicion' => 'datetime',    
        'fecha_baja'        => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaInventario::class, 'categoria_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function areaComun()
    {
        return $this->belongsTo(AreaComun::class, 'area_comun_id');
    }
    public function verificaciones()
    {
        return $this->hasMany(VerificacionInventario::class);
    }
}
