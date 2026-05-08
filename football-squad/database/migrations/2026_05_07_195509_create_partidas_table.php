<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     *  roda a migrations
     */
    public function up(): void
    {
        Schema::create('partidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campeonato_id')->constrained('campeonatos')->onDelete('cascade');
    
            // Chaves estrangeiras para os dois times
            $table->foreignId('time_mandante_id')->constrained('times');
            $table->foreignId('time_visitante_id')->constrained('times');
    
            // Campo para identificar a fase: 'quartas', 'semi', 'terceiro_lugar', 'final'
            $table->string('fase'); 
    
            // Placares da partida
            $table->integer('gols_mandante')->default(0);
            $table->integer('gols_visitante')->default(0);
            $table->foreignId('vencedor_id')->nullable()->constrained('times');
            // Status para saber se a partida já foi realizada
            $table->timestamp('encerrada_em')->nullable();
            $table->timestamps();
        });
    }

    /**
     * reverte a migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidas');
    }
};
