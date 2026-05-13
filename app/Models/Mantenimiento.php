<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'descripcion', 
        'estado', 
        'prioridad',
        'fecha_hora', 
        'monto',
        'usuario_id', 
        'empresaExterna_id', 
        'gasto_id', 
        'pago_id'
    ];

    protected $casts = [
    'fecha_hora' => 'datetime',
    ];
    
    public const PRIORIDAD_BAJA = 'baja';
    public const PRIORIDAD_MEDIA = 'media';
    public const PRIORIDAD_ALTA = 'alta';

    public static function prioridades()
    {
        return [
            self::PRIORIDAD_BAJA  => 'Baja',
            self::PRIORIDAD_MEDIA => 'Media',
            self::PRIORIDAD_ALTA  => 'Alta',
        ];
    }

    public function getPrioridadEtiquetaAttribute()
    {
        $etiquetas = [
            'baja'  => 'Baja Prioridad',
            'media' => 'Prioridad Media',
            'alta'  => 'ALTA PRIORIDAD ⚠️',
        ];
        return $etiquetas[$this->prioridad] ?? 'No definida';
    }

    public function scopeAltaPrioridad($query)
    {
        return $query->where('prioridad', 'alta');
    }

    public function scopeMediana($query)
    {
        return $query->where('prioridad', 'media');
    }

    public function scopeBaja($query)
    {
        return $query->where('prioridad', 'baja');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function empresa()
    {
        return $this->belongsTo(EmpresaExterna::class, 'empresaExterna_id');
    }

    public function gasto()
    {
        return $this->belongsTo(Gasto::class);
    }

    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }
}