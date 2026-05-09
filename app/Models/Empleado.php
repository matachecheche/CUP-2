<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'apellido', 'ci', 'telefono', 'direccion', 'cargo_empleado_id'
    ];

    public function cargo()
    {
        return $this->belongsTo(CargoEmpleado::class, 'cargo_empleado_id');
    }
    public function multas()
    {
        return $this->hasMany(Multa::class);
    }
    public function getNombreCompletoAttribute()
    {
        return $this->nombre . ' ' . $this->apellido;
    }
    public function reclamos()
    {
        return $this->hasMany(Reclamo::class);
    }
}
