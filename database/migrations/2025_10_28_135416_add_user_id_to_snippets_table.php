<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Solo ejecutar si la tabla snippets existe y no tiene la columna user_id
        if (Schema::hasTable('snippets') && !Schema::hasColumn('snippets', 'user_id')) {
            Schema::table('snippets', function (Blueprint $table) {
                // Agregar la columna como nullable primero
                $table->foreignId('user_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('users')
                      ->onDelete('cascade');
            });

            // Opcional: Asignar snippets existentes al primer usuario
            try {
                if (class_exists('App\Models\User')) {
                    $user = \App\Models\User::first();
                    if ($user) {
                        \Illuminate\Support\Facades\DB::table('snippets')
                            ->whereNull('user_id')
                            ->update(['user_id' => $user->id]);
                    }
                }
            } catch (\Exception $e) {
                // Si hay error al asignar, continuar de todos modos
                \Log::warning('Error al asignar snippets existentes: ' . $e->getMessage());
            }

            // Hacer la columna NOT NULL después de asignar valores
            Schema::table('snippets', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('snippets', 'user_id')) {
            Schema::table('snippets', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
};