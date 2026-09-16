<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('snippets', function (Blueprint $table) {
            // I - ID (siempre primero)
            $table->id();

            // P - Personal / Datos de negocio
            $table->string('title', 255);
            $table->text('code');
            $table->text('description')->nullable();
            // Etiquetas estilo thiscodeworks (jsonb para búsquedas futuras)
            $table->jsonb('tags')->nullable();
            // Referencia al snippet publicado en thiscodeworks.com (integración API)
            $table->string('thiscodeworks_id', 64)->nullable();
            $table->string('thiscodeworks_url', 255)->nullable();

            // R - Relaciones (desacopladas de modelos, usando strings)
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('language_id')->nullable()->constrained('languages')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // A - Auth (No aplica en esta tabla)

            // T - Timestamps / Fechas
            $table->timestamps();
            $table->softDeletes();
        });

        // ÍNDICES para búsquedas (clave del rendimiento)
        $table = 'snippets';
        // Búsqueda por usuario (panel/dashboard)
        DB::statement("CREATE INDEX idx_{$table}_user_id ON {$table} (user_id)");
        // Filtro combinado categoría + lenguaje
        DB::statement("CREATE INDEX idx_{$table}_category_language ON {$table} (category_id, language_id)");
        // Búsqueda de snippets vinculados a thiscodeworks
        DB::statement("CREATE INDEX idx_{$table}_thiscodeworks ON {$table} (thiscodeworks_id) WHERE thiscodeworks_id IS NOT NULL");
        // Búsqueda por título sin importar mayúsculas (índice de expresión)
        DB::statement("CREATE INDEX idx_{$table}_lower_title ON {$table} (LOWER(title))");
        // Búsqueda de texto completo en código (GIN)
        DB::statement("CREATE INDEX idx_{$table}_search ON {$table} USING GIN (to_tsvector('spanish', code))");
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS idx_snippets_user_id');
        DB::statement('DROP INDEX IF EXISTS idx_snippets_category_language');
        DB::statement('DROP INDEX IF EXISTS idx_snippets_thiscodeworks');
        DB::statement('DROP INDEX IF EXISTS idx_snippets_lower_title');
        DB::statement('DROP INDEX IF EXISTS idx_snippets_search');

        Schema::dropIfExists('snippets');
    }
};