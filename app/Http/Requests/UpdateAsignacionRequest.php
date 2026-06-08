<?php

namespace App\Http\Requests;

/**
 * CU-12 · Validación de edición de asignación.
 * Mismas reglas que la creación; StoreAsignacionRequest ya excluye la propia
 * asignación (vía el parámetro de ruta) de las validaciones de negocio.
 */
class UpdateAsignacionRequest extends StoreAsignacionRequest
{
}
