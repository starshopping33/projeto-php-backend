<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
{
    Schema::create('playlist_music', function (Blueprint $table) {
        $table->id();
        $table->string('music_id');      
        $table->string('music_name');
        $table->string('artist_name');
        $table->string('cover_url')->nullable();
        
        $table->timestamps();
    });
}
    public function down(): void
    {
        Schema::dropIfExists('playlist_music');
    }
};