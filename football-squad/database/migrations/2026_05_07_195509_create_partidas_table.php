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
        Schema::create('partidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campeonato_id')->constrained('campeonatos')->onDelete('cascade');
    
            // Chaves estrangeiras para os dois times
            $table->foreignId('time_casa_id')->constrained('times');
            $table->foreignId('time_visitante_id')->constrained('times');
    
            // Campo para identificar a fase: 'quartas', 'semi', 'terceiro_lugar', 'final'
            $table->string('fase'); 
    
            // Placares da partida
            $table->integer('placar_casa')->default(0);
            $table->integer('placar_visitante')->default(0);
            
            // Status para saber se a partida já foi realizada
            $table->boolean('encerrada')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidas');
    }
};
