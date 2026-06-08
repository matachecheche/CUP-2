<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** CU-11 · Validación de creación de grupo. */
class StoreGrupoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // el middleware permission:crear grupos ya autoriza
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:20', 'unique:grupos,codigo'],
            'turno' => ['required', 'in:mañana,tarde,noche'],
            'modalidad' => ['required', 'in:presencial,virtual'],
            'capacidad_maxima' => ['required', 'integer', 'min:1', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.unique' => 'Ya existe un grupo con ese código.',
            'capacidad_maxima.min' => 'La capacidad debe ser al menos 1.',
        ];
    }
}
