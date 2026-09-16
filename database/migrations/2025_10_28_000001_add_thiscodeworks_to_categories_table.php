<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('thiscodeworks_id', 100)->nullable()->after('proposito');
            $table->text('thiscodeworks_url')->nullable()->after('thiscodeworks_id');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['thiscodeworks_id', 'thiscodeworks_url']);
        });
    }
};