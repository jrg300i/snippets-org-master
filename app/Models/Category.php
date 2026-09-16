<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'proposito',
        'thiscodeworks_id',
        'thiscodeworks_url',
    ];

protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    /**
     * Get the snippets for the category.
     */
    public function snippets(): HasMany
    {
        return $this->hasMany(Snippet::class);
    }

    /**
     * Snippets de la categoría ya publicados en thiscodeworks.com.
     */
    public function publishedSnippets(): HasMany
    {
        return $this->hasMany(Snippet::class)->whereNotNull('thiscodeworks_id');
    }
}
