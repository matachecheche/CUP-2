<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comunicado extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'contenido',
        'tipo',
        'fecha_publicacion',
        'usuario_id',
        'notificado'
    ];

    protected $casts = [
        'fecha_publicacion' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
