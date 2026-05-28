<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/** CU-11 / CU-12 */
class Grupo extends Model {
    protected $table    = 'grupos';
    protected $fillable = ['gestion_id','codigo','turno','modalidad','capacidad_maxima','estado'];
    protected $casts    = ['estado' => 'boolean'];

    public function gestion()      { return $this->belongsTo(Gestion::class); }
    public function asignaciones() { return $this->hasMany(Asignacion::class); }
    public function postulantes()  { return $this->belongsToMany(Postulante::class, 'grupo_postulante'); }
    public function notas()        { return $this->hasMany(Nota::class); }
}
