<?php

namespace App\Services;

use App\Models\Auditoria;

/**
 * Registro inmutable de acciones de usuarios (reglas.md §19).
 * La tabla 'auditorias' solo se inserta y se consulta: nunca se edita ni se elimina vía la app.
 */
class AuditoriaService
{
    /**
     * Registrar una acción en el módulo de auditoría.
     *
     * @param string $accion      crear | actualizar | eliminar | login | logout | backup | publicar...
     * @param string $entidad     snippet | categoria | lenguaje | usuario | perfil ...
     * @param int|null $id        ID de la entidad afectada.
     * @param string|null $descripcion  Texto legible de lo ocurrido.
     * @param mixed $datosAntes   Estado previo de la entidad (si aplica).
     * @param mixed $datosDespues Estado posterior de la entidad (si aplica).
     */
    public function registrar(
        string $accion,
        string $entidad,
        ?int $id = null,
        ?string $descripcion = null,
        mixed $datosAntes = null,
        mixed $datosDespues = null
    ): Auditoria {
        return Auditoria::create([
            'user_id' => optional(auth()->user())->id,
            'accion' => $accion,
            'entidad' => $entidad,
            'entidad_id' => $id,
            'descripcion' => $descripcion,
            'datos_antes' => $datosAntes,
            'datos_despues' => $datosDespues,
            'ip' => request()->ip(),
        ]);
    }
}