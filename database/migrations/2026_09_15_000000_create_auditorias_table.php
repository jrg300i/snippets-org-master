<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // La tabla auditorias es un registro inmutable de seguridad:
        // solo se inserta y se consulta, NUNCA se edita ni se elimina vía la app.
        Schema::create('auditorias', function (Blueprint $table) {
            // I - ID (siempre primero)
            $table->id();

            // P - Personal / Datos de negocio (la acción registrada)
            $table->string('accion', 50);          // crear, actualizar, eliminar, login, logout, backup...
            $table->string('entidad', 50);         // snippet, categoria, lenguaje, usuario...
            $table->unsignedBigInteger('entidad_id')->nullable();
            $table->text('descripcion')->nullable();
            $table->jsonb('datos_antes')->nullable();   // estado previo (dato anterior → dato nuevo)
            $table->jsonb('datos_despues')->nullable();
            $table->string('ip', 45)->nullable();

            // R - Relaciones
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // A - Auth (No aplica)

            // T - Timestamps / Fechas (sin soft deletes: registro inmutable)
            $table->timestamps();
        });

        // ÍNDICES para el filtrado del admin (reglas.md §19.3)
        DB::statement('CREATE INDEX idx_auditorias_user_id ON auditorias (user_id)');
        DB::statement('CREATE INDEX idx_auditorias_entidad ON auditorias (entidad)');
        DB::statement('CREATE INDEX idx_auditorias_created_at ON auditorias (created_at)');
        DB::statement('CREATE INDEX idx_auditorias_entidad_created ON auditorias (entidad, created_at)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS idx_auditorias_user_id');
        DB::statement('DROP INDEX IF EXISTS idx_auditorias_entidad');
        DB::statement('DROP INDEX IF EXISTS idx_auditorias_created_at');
        DB::statement('DROP INDEX IF EXISTS idx_auditorias_entidad_created');

        Schema::dropIfExists('auditorias');
    }
};