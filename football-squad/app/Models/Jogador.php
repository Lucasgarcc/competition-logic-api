<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jogador extends Model
{
    use HasFactory;

    protected $table = 'jogadores';

    protected $fillable = [
        'time_id',
        'nome',
        'data_nascimento',
        'posicao',
        'numero_camisa'
    ];

    /**
     * Relacionamento: O jogador pertence a um time.
     */
    public function time() {
        return $this->belongsTo(Time::class);
    }

    /**
     * Relacionamento: Um jogador pode ter vários cartões no histórico.
     */
    public function cartoes() {
        return $this->belongsTo(Cartao::class);
    }

}
