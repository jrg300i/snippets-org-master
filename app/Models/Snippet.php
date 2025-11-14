<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Snippet extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'code', 'category_id', 'language_id', 'user_id', 'description'
    ];

    // Relaciones
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener las primeras N líneas del código
     */
    public function getFirstLines($lines = 15)
    {
        $codeLines = explode("\n", $this->code);
        $firstLines = array_slice($codeLines, 0, $lines);
        
        return implode("\n", $firstLines);
    }

    /**
     * Scope para snippets del usuario autenticado
     */
    public function scopeForCurrentUser($query)
    {
        return $query->where('user_id', auth()->id());
    }
}