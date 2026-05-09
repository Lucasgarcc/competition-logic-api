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
        'fase'//ex:'quartas', 'semi', 'final'
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
    public function mandante() {
        return $this->belongsTo(Time::class, 'time_mandante_id');
    }

    /**
     * Relacionamente: O time que joga fora (Visitante). 
     */
    public function visitante() {
        return $this->belongsTo(Time::class, 'time_visitante_id');
    }
}
