<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('snippets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->foreignId('language_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->string('title', 255);
            $table->text('code');
            $table->timestamps();
            
            $table->index(['category_id', 'language_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('snippets');
    }
};
