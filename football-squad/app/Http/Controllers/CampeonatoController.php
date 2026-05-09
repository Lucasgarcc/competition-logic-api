<?php

namespace App\Http\Controllers;

use App\Models\Time;
use App\Models\Partida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

class CampeonatoController extends Controller
{
    public function simularQuartas() {

        // Busca 2 times aleatórios para o teste
        $times = Time::inRandomOrder()->take(2)->get();
        $inicioPartida = now();
    
        $campeonato = \App\Models\Campeonato::first() ?? \App\Models\Campeonato::create([
            'nome' => 'Copa Melhor Bairro teste',
            'ano' => 2026,
            'data_inicio' => now(),
            'data_fim' => $inicioPartida->copy()->addMinutes(50) // ex: Define 50 min o tempo do jogo;
        ]);

        if ($times->count() < 2) {
            return response()->json(['erro' => 'Não há times suficientes no banco. Rode o Seeder!'], 400);
        }

        $mandante = $times[0];
        $visitante = $times[1];

        // Chama o simulador Python
        $result = Process::run("python3 " . base_path('teste.py') . " '{$mandante->nome}' '{$visitante->nome}'");
    
        if ($result->successful()) {
            $placar = trim($result->output()); 
            [$golsM, $golsV] = explode('-', $placar);

            // Cria o registro na tabela partidas
            $partida = Partida::create([
                'campeonato_id' => $campeonato->id,
                'time_mandante_id' => $mandante->id,
                'time_visitante_id' => $visitante->id,
                'gols_mandante' => (int) $golsM,
                'gols_visitante' => (int) $golsV,
                'fase' => 'quartas',
                'status' => 'encerrado',
                'data_partida' => now(),
            ]);

            return response()->json([
                'mensagem' => 'Partida simulada com sucesso!',
                'confronto' => "{$mandante->nome} {$golsM} x {$golsV} {$visitante->nome}",
                'detalhes' => $partida,
                'palcar' => $placar
            ]);
    
            return response()->json([
                'mensagem' => 'Partida simulada com sucesso!',
                'confronto' => "{$mandante->nome} {$golsM} x {$golsV} {$visitante->nome}",
                'detalhes' => $partida
            ]);
        }
        
        return response()->json([
            'erro' => 'Falha ao rodar o script de teste Python',
            'output' => $result->errorOutput()
        ], 500);
    }
}
