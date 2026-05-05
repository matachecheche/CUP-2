<?php
// app/Models/EmpresaExterna.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaExterna extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'servicio',
        'telefono',
        'correo',
        'direccion',
        'observacion',
    ];
}
