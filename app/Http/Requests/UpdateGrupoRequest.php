<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** CU-11 · Validación de edición de grupo (turno, modalidad, capacidad; código opcional). */
class UpdateGrupoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // el middleware permission:editar grupos ya autoriza
    }

    public function rules(): array
    {
        return [
            'codigo' => ['sometimes', 'required', 'string', 'max:20',
                Rule::unique('grupos', 'codigo')->ignore($this->route('grupo')?->id)],
            'turno' => ['required', 'in:mañana,tarde,noche'],
            'modalidad' => ['required', 'in:presencial,virtual'],
            'capacidad_maxima' => ['required', 'integer', 'min:1', 'max:200'],
        ];
    }
}
