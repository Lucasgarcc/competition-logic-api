<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    use HasFactory;

    protected $fillable = ['nome']; 

    public function jogadores() {
        return $this->hasMany(Jogador::class);
    }

    // Útil para calcular o desempate: pontos acumulados (gols feitos - sofridos)
    public function partidasComoMandante() {
        return $this->hasMany(Partida::class, 'time_mandante_id');
    }

    public function partidasComoVisitante() {
        return $this->hasMany(Partida::class, 'time_visitante_id');
    }
}
