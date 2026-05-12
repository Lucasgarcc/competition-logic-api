<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partida extends Model
{
    use HasFactory;

    protected $fillable = [
        'campeonato_id',
        'time_mandante_id',
        'time_visitante_id',
        'gols_mandante',
        'gols_visitante',
        'vencedor_id',
        'fase',//ex:'quartas', 'semi', 'final'
        //'status',
        'encerrada_em'
       
    ];

    const LIMITES_FASE = [
        'quartas'   => 4,
        'semifinal' => 2,
        'final'     => 1,
        'terceiro_lugar' => 1,
    ];

    /**
     * Relacionamento: A partida pertence a um campeonato.
     */
    public function campeonato() {
        return $this->belongsTo(Campeonato::class);
    }

    /**
     * Relacionamento: O time que joga em casa (Mandante).
     */
    public function timeMandante() {
        return $this->belongsTo(Time::class, 'time_mandante_id');
    }

    /**
     * Relacionamente: O time que joga fora (Visitante). 
     */
    public function timeVisitante() {
        return $this->belongsTo(Time::class, 'time_visitante_id');
    }

    /**
     * Valida se ainda é possível criar partidas para uma determinada fase.
     */
    public static function gerarPartida($fase, $campeonatoId){
        // Usando self:: para acessar a constante
        $partidasExistentes = self::where('fase', $fase)
            ->where('campeonato_id', $campeonatoId)
            ->count();

        // Verificamos se a fase existe no array para evitar erro de índice
        if (!isset(self::LIMITES_FASE[$fase])) {
            throw new \Exception("Fase '{$fase} inválida!'");
            
        }

        if ($partidasExistentes >= self::LIMITES_FASE[$fase]) {
            throw new \Exception("Limite de partidas para a fase {$fase} atingido.");
        }
        
        return true;
    }
}
