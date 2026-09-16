<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    protected $table = 'auditorias';

    protected $fillable = [
        'user_id',
        'accion',
        'entidad',
        'entidad_id',
        'descripcion',
        'datos_antes',
        'datos_despues',
        'ip',
    ];

    protected function casts(): array
    {
        return [
            'datos_antes' => 'array',
            'datos_despues' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}