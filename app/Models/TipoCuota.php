<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCuota extends Model
{
    use HasFactory;

    // ✅ Asegura el nombre correcto de la tabla si Laravel no lo infiere bien
    protected $table = 'tipos_cuotas';

    protected $fillable = ['nombre', 'frecuencia', 'editable'];

    public function cuotas()
    {
        return $this->hasMany(Cuota::class);
    }
}
