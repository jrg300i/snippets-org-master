<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 50)->unique();
            $table->string('color', 7)->default('#6c757d');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

         if (!Schema::hasColumn('languages', 'color')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->string('color')->default('#6c757d')->after('name');
            });
        }
    }

    public function down(): void
    {
         if (Schema::hasColumn('languages', 'color')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->dropColumn('color');
            });
        }
        Schema::dropIfExists('languages');
    }
};
