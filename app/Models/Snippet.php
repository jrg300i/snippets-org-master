<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Snippet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'code',
        'description',
        'tags',
        'thiscodeworks_id',
        'thiscodeworks_url',
        'category_id',
        'language_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'json',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Normalizar las etiquetas antes de guardar: minúsculas, sin vacíos y en JSON.
     * Almacenamos el JSON directamente porque el atributo ya queda seteado por el mutator
     * y PostgreSQL jsonb no acepta un array PHP crudo.
     */
    public function setTagsAttribute($value): void
    {
        $raw = is_array($value) ? $value : preg_split('/\s*,\s*/', (string) $value);

        $tags = collect($raw)
            ->map(fn ($tag) => trim((string) $tag))
            ->filter()
            ->map(fn ($tag) => mb_strtolower($tag))
            ->values()
            ->all();

        $this->attributes['tags'] = json_encode($tags, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Obtener las primeras N líneas del código.
     */
    public function getFirstLines(int $lines = 15): string
    {
        $codeLines = explode("\n", $this->code);
        $firstLines = array_slice($codeLines, 0, $lines);

        return implode("\n", $firstLines);
    }

    /**
     * Scope para snippets del usuario autenticado.
     */
    public function scopeForCurrentUser($query)
    {
        return $query->where('user_id', auth()->id());
    }
}