<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campeonato extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nome', 
        'ano',
        'data_inicio',
        'data_fim', 
        'vencedor_id'
    ];

    /**
     * Relacionamento: Um campeonato tem muitas partidas.
     */
    public function partidas() {
        return $this->hasMany(Partidas::class);
    }

    /**
     * Relacionamento: Um campeonato tem um vencedor (time vencedor) 
     */
    public function vencedor() {
        return $this->hasMany(Time::class, 'vencedor_id');
    }
}
