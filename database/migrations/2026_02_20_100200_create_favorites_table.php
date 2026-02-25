<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();

           
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

          
            $table->string('music_id', 255);

           
            $table->string('music_name');
            $table->string('artist_name');
            $table->string('music_url')->nullable();
            $table->string('image')->nullable();

           
           

            $table->softDeletes();
            $table->timestamps();

          
            $table->index(['user_id']);
            $table->index(['music_id']);

           
            $table->unique(['user_id', 'music_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};