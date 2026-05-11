<?php

namespace App\Http\Controllers;

use App\Models\Time;
use App\Models\Partida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\DB;

class CampeonatoController extends Controller {

    /**
     * Simula uma partida do campeonato com base na fase atual.
     * * Gerencia o fluxo automático de fases (quartas, semi, 3º lugar e final),
     * integra com script Python para placares e atualiza estatísticas.
     *
     * @param Request $request Contém nome_campeonato e ano (opcionais).
     * @return \Illuminate\Http\JsonResponse
     */
    public function simularPartida(Request $request) {

        $nomeCampeonato = $request->input('nome_campeonato', 'Campeonato Padrão');
        $anoCampeonato = $request->input('ano', date('Y'));

        //$fase = $request->input('fase', 'quartas');
     
        $campeonato = \App\Models\Campeonato::firstOrCreate(
            ['nome' => $nomeCampeonato, 'ano' => $anoCampeonato],
            [
                'data_inicio' => now(),
                'data_fim' => now()->addDays(30) // ex: dura 1 mês
            ]
        );

        $contagemQuartas = Partida::where('campeonato_id', $campeonato->id)->where('fase', 'quartas')->count();
        $contagemSemi = Partida::where('campeonato_id', $campeonato->id)->where('fase', 'semifinal')->count();
        $contagemTerceiro = Partida::where('campeonato_id', $campeonato->id)->where('fase', 'terceiro_lugar')->count();

        if ($contagemQuartas < 4) {
            $fase = 'quartas';
        } 
        elseif ($contagemSemi < 2) {
            $fase = 'semifinal';
        }
        elseif ($contagemTerceiro < 1 ) {
            $fase = 'terceiro_lugar';
        }
        else {
            $fase = 'final';
        }

        try {
            // Validação de fase
            Partida::gerarPartida($fase);
        
            // Identifica quem tem direito de jogar (Memória de Classificados)
            $faseAnterior = match($fase) {
                'semifinal' => 'quartas',
                'final'     => 'semifinal',
                'terceiro_lugar' => 'semifinal',
                default     => null
            };

            // Filtra quem já jogou na fase ATUAL (para não repetir jogo)
            $timesJaJogaramNestaFase = Partida::where('fase', $fase)
                ->where('campeonato_id', $campeonato->id)
                ->get()
                ->flatMap(fn($p) => [$p->time_mandante_id, $p->time_visitante_id]);

            // Monta a Query baseada na Fase
            if ($fase === 'quartas') {
                
                // Nas quartas, qualquer time do banco que ainda não jogou
                $queryTimes = Time::whereNotIn('id', $timesJaJogaramNestaFase);
            }
            elseif ($fase === 'terceiro_lugar') {

                $jogaramSemi = Partida::where('fase', 'semifinal')
                    ->where('campeonato_id', $campeonato->id)
                    ->get()
                    ->flatMap(fn($p) => [$p->time_mandante_id, $p->time_visitante_id]);
                
                $vencedoresSemi = Partida::where('fase', 'semifinal') 
                    ->where('campeonato_id', $campeonato->id)
                    ->pluck('vencedor_id')
                    ->toArray();

                $perdedoresSemi = $jogaramSemi->diff($vencedoresSemi);
                
                $queryTimes = Time::whereIn('id', $perdedoresSemi)
                    ->whereNotIn('id', $timesJaJogaramNestaFase);
            } 
            else {

                // Nas semis/final, só entram os VENCEDORES da fase anterior
                $vencedoresFaseAnterior = Partida::where('fase', $faseAnterior)
                    ->where('campeonato_id', $campeonato->id)
                    ->pluck('vencedor_id');

                $queryTimes = Time::whereIn('id', $vencedoresFaseAnterior)
                    ->whereNotIn('id', $timesJaJogaramNestaFase);
            }

            $times = $queryTimes->inRandomOrder()->take(2)->get();

            if ($times->count() < 2) {
                return response()->json(['erro' => "Não há times disponíveis para a fase: {$fase}"], 400);
            }
    
            $mandante = $times[0];
            $visitante = $times[1];

            //  Simula teste Python
            $result = Process::run("python3 " . base_path('teste.py') . " '{$mandante->nome}' '{$visitante->nome}'");
            
            if ($result->successful()) {

                $placar = trim($result->output()); 
                [$golsM, $golsV] = explode('-', $placar);

                $golsMInt = (int)$golsM;
                $golsVInt = (int)$golsV;
                
                // Vencedor e Desempate
                if ($golsMInt > $golsVInt) {
                    $idVencedor = $mandante->id;
                } elseif ($golsVInt > $golsMInt) {
                    $idVencedor = $visitante->id;
                } else {
                    // Aqui entra o desempate por campanha se houver empate no placar
                    $idVencedor = $this->desempatePorCampanha($mandante->id, $visitante->id);
                }

                // Salva a Partida (com seus 50 min de duração)
                $dataInicio = now();
                
                $partida = Partida::create([
                    'campeonato_id' => $campeonato->id,
                    'time_mandante_id' => $mandante->id,
                    'time_visitante_id' => $visitante->id,
                    'gols_mandante' => $golsMInt,
                    'gols_visitante' => $golsVInt,      
                    'vencedor_id' => $idVencedor,
                    'fase' => $fase,
                    'status' => 'encerrado',
                    'created_at' => $dataInicio,
                    'encerrada_em' => $dataInicio->copy()->addMinutes(50),
                ]);

                $this->atualizaEstatisticas($partida);

                return response()->json([
                    'mensagem' => 'Partida simulada com sucesso!',
                    'confronto' => "{$mandante->nome} {$golsM} x {$golsV} {$visitante->nome}",
                    'detalhes' => $partida,
                    'placar' => $placar
                ]);
            }
            
            return response()->json([
                'erro' => 'Falha ao rodar o script de teste Python',
                'output' => $result->errorOutput()
            ], 500);
        } 
        catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 422);
        }
    }

    /**
     * Define o vencedor em caso de empate no tempo regulamentar.
     * * Segue os critérios de aceite:
     * 1. Maior saldo de gols acumulado.
     * 2. Ordem de inscrição (ID menor).
     *
     * @param int $id1 ID do time mandante.
     * @param int $id2 ID do time visitante.
     * @return int ID do time vencedor.
     */
    private function desempatePorCampanha($id1, $id2){
        $stats1 = DB::table('classificacoes')->where('time_id', $id1)->first();
        $stats2 = DB::table('classificacoes')->where('time_id', $id2)->first();

        if (!$stats1 || !$stats2) {
            return ($id1 < $id2) ? $id1 : $id2;
        }

        // O desafio foca na pontuação de gols (saldo)
        if ($stats1->saldo_gols != $stats2->saldo_gols) {
            return ($stats1->saldo_gols > $stats2->saldo_gols) ? $id1 : $id2;
        }

        // Segundo critério: Inscrito primeiro (ID menor)
        return ($id1 < $id2) ? $id1 : $id2;
    }

    /**
     * Orquestra a atualização de estatísticas para ambos os times da partida.
     *
     * @param Partida $partida Objeto da partida recém-criada.
     * @return void
     */
    private function atualizaEstatisticas(Partida $partida) {

        $this->updateOrCreateStats(
            $partida->time_mandante_id,
            $partida->campeonato_id, 
            $partida->gols_mandante, 
            $partida->gols_visitante
        );

        $this->updateOrCreateStats(
            $partida->time_visitante_id, 
            $partida->campeonato_id, 
            $partida->gols_visitante, 
            $partida->gols_mandante
        );
    }

    /**
     * Incrementa vitórias, empates, derrotas e saldo de gols no banco de dados.
     *
     * @param int $timeId
     * @param int $campeonatoId
     * @param int $golsPro
     * @param int $golsContra
     * @return void
     */
    private function updateOrCreateStats($timeId, $campeonatoId, $golsPro, $golsContra) {

        // Busca ou inicia um novo registro de classificações 
        $stats = \App\Models\Classificacao::firstOrNew([
            'time_id' => $timeId,
            'campeonato_id' => $campeonatoId
        ]);

        //Pontuação e Resultados
        if ($golsPro > $golsContra) {
            $stats->vitorias += 1;
            $stats->pontos += 3;
        }
        elseif ($golsPro == $golsContra) {
            $stats->empates += 1;
            $stats->pontos += 1;
        }
        else {
            $stats->derrotas += 1;
        }

        // Acumula Gols
        $stats->gols_pro += $golsPro;
        $stats->gols_contra += $golsContra;
        $stats->saldo_gols = $stats->gols_pro - $stats->gols_contra;

        $stats->save();
    }
}
