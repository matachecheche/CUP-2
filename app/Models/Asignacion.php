<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/** CU-12 */
class Asignacion extends Model {
    protected $table    = 'asignaciones';
    protected $fillable = ['grupo_id','docente_id','materia_id','dia','hora_inicio','hora_fin','aula'];

    public function grupo()   { return $this->belongsTo(Grupo::class); }
    public function docente() { return $this->belongsTo(Docente::class); }
    public function materia() { return $this->belongsTo(Materia::class); }
}
