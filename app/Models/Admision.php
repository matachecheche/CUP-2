<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/** CU-27 a CU-29 */
class Admision extends Model {
    protected $table    = 'admisiones';
    protected $fillable = ['postulante_id','gestion_id','promedio_general','carrera_asignada_id','resultado','publicado'];
    protected $casts    = ['publicado' => 'boolean'];

    public function postulante()      { return $this->belongsTo(Postulante::class); }
    public function gestion()         { return $this->belongsTo(Gestion::class); }
    public function carreraAsignada() { return $this->belongsTo(Carrera::class, 'carrera_asignada_id'); }
}
