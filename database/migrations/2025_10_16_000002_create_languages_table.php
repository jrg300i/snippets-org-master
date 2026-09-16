<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            // I - ID (siempre primero)
            $table->id();

            // P - Personal / Datos de negocio
            $table->string('name', 100)->unique();
            $table->string('slug', 50)->unique();
            $table->string('color', 7)->default('#6c757d');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);

            // R - Relaciones (No aplica en esta tabla)

            // A - Auth (No aplica)

            // T - Timestamps / Fechas
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};