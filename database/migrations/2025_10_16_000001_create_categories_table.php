<?php
// database/migrations/2025_10_16_000001_create_categories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            // I - ID (siempre primero)
            $table->id();

            // P - Personal / Datos de negocio
            $table->string('name', 100)->unique();
            $table->string('description', 255)->nullable();
            $table->string('proposito', 255)->nullable();

            // R - Relaciones (No aplica en esta tabla)

            // A - Auth (No aplica)

            // T - Timestamps / Fechas
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};