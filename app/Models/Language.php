<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Language extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'color', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Mutator para asegurar formato correcto del color
     */
    public function setColorAttribute($value)
    {
        // Limpiar y formatear el color
        $color = Str::lower(trim($value));
        
        // Asegurar que empiece con #
        if (!Str::startsWith($color, '#')) {
            $color = '#' . $color;
        }

        // Validar formato hexadecimal
        if (!preg_match('/^#([a-f0-9]{6}|[a-f0-9]{3})$/', $color)) {
            // Color por defecto si no es válido
            $color = '#6c757d';
        }

        $this->attributes['color'] = $color;
    }

    /**
     * Accessor para obtener color con formato consistente
     */
    public function getColorAttribute($value)
    {
        return Str::lower($value);
    }

    /**
     * Get the snippets for the language.
     */
    public function snippets(): HasMany
    {
        return $this->hasMany(Snippet::class);
    }

    /**
     * Scope active languages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Obtener el color en formato RGB
     */
    public function getColorRgbAttribute()
    {
        $color = ltrim($this->color, '#');
        
        if (strlen($color) === 3) {
            $color = $color[0].$color[0].$color[1].$color[1].$color[2].$color[2];
        }

        $rgb = [
            'r' => hexdec(substr($color, 0, 2)),
            'g' => hexdec(substr($color, 2, 2)),
            'b' => hexdec(substr($color, 4, 2))
        ];

        return $rgb;
    }

    /**
     * Obtener color con transparencia
     */
    public function getColorWithAlpha($alpha = 0.1)
    {
        $rgb = $this->color_rgb;
        return "rgba({$rgb['r']}, {$rgb['g']}, {$rgb['b']}, {$alpha})";
    }
}