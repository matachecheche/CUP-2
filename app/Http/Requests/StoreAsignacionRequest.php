<?php

namespace App\Http\Requests;

use App\Models\Docente;
use App\Models\Materia;
use App\Services\AsignacionService;
use Illuminate\Foundation\Http\FormRequest;

/**
 * CU-12 · Validación de la asignación docente↔grupo↔materia.
 * Reglas de entrada + reglas de negocio (delegadas en AsignacionService).
 */
class StoreAsignacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // el middleware permission ya autoriza
    }

    public function rules(): array
    {
        return [
            'docente_id' => ['required', 'exists:docentes,id'],
            'grupo_id' => ['required', 'exists:grupos,id'],
            'materia_id' => ['required', 'exists:materias,id'],
            'dia' => ['required', 'in:lunes,martes,miercoles,jueves,viernes,sabado'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'aula' => ['nullable', 'string', 'max:30', 'regex:/^[A-Za-z0-9\-]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'hora_fin.after' => 'La hora de fin debe ser posterior a la de inicio.',
            'aula.regex' => 'El aula solo admite letras, números y guiones.',
        ];
    }

    /** Id de la asignación en edición (null al crear), para excluirla de las validaciones. */
    protected function excluirId(): ?int
    {
        return $this->route('asignacion')?->id;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            if ($v->errors()->isNotEmpty()) {
                return; // no correr reglas de negocio si la entrada básica ya falló
            }
            $svc = app(AsignacionService::class);
            $excluir = $this->excluirId();
            $docente = Docente::find($this->docente_id);
            $materia = Materia::find($this->materia_id);

            if ($docente && $materia && ! $svc->docenteCumpleRequisitos($docente, $materia)) {
                $v->errors()->add('docente_id', 'El docente no cumple los requisitos (afinidad de área, maestría y diplomado en educación superior).');
            }
            if ($svc->materiaYaCubierta((int) $this->grupo_id, (int) $this->materia_id, $excluir)) {
                $v->errors()->add('materia_id', 'Esa materia ya tiene un docente asignado en este grupo.');
            }
            if ($svc->excedeLimiteGrupos((int) $this->docente_id, (int) $this->grupo_id, $excluir)) {
                $v->errors()->add('grupo_id', 'El docente ya alcanzó el máximo de '.AsignacionService::MAX_GRUPOS_POR_DOCENTE.' grupos.');
            }
            if ($svc->hayCruceHorario((int) $this->docente_id, $this->dia, $this->hora_inicio, $this->hora_fin, $excluir)) {
                $v->errors()->add('hora_inicio', 'Cruce de horario con otra asignación del mismo docente.');
            }
        });
    }
}
