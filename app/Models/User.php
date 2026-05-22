<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'docente_id',
        'postulante_id',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Relaciones ────────────────────────────────────────────────────────────

    /** Usuario vinculado a un docente del CUP */
    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }

    /** Usuario vinculado a un postulante */
    public function postulante()
    {
        return $this->belongsTo(Postulante::class);
    }

    /** Bitácora de acciones */
    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class);
    }
}
