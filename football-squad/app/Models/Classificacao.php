<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classificacao extends Model
{
    use HasFactory;

    protected $table = 'classificacoes';
    protected $fillable = [
        'time_id',
        'campeonato_id',
        'vitorias',
        'empates',
        'derrotas',
        'gols_pro',
        'gols_contra',
        'saldo_gols',
        'pontos'
    ];

    /**
     *  Relacionamento: A classificação pertencente a um time.
     */
    public function time() {
        return $this->belongsTo(Time::class);
    }

    /**
     * Relacionamento: A classificação pertencente a um compeonato.
     */
    public function campeonato() {
        return $this->belongsTo(Time::class);
    }
}