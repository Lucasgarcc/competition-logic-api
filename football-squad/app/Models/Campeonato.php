<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return $this->hasMany(Partida::class);
    }

    /**
     * Relacionamento: Um campeonato tem um vencedor (time vencedor).
     */
    public function vencedor() {
        return $this->belongsTo(Time::class, 'vencedor_id', 'id');
    }

    /**
     * Relacionamento: Um campeonato se tem calssificações.
     */
    public function estatisticas(): HasMany
    {
        return $this->hasMany(Classificacao::class, 'campeonato_id'); // Ou o nome correto do seu model
    }
}
