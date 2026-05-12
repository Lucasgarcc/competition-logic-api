<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Campeonato;
use App\Models\Time;
use App\Models\Classificacao;
use App\Models\Partida;
use App\Http\Controllers\CampeonatoController;
use Illuminate\Support\Facades\Process;

class CampeonatoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function impede_simulacao_com_menos_de_oito_times(): void 
    {
        // Arrange: Criamos apenas 7 times (limite inferior)
        Time::factory()->count(7)->create();

        // Action
        //$response = $this->get('/simular-partida-teste');
        $response = $this->post('/simular-partida', [
            'nome_campeonato' => 'Campeonato de Teste',
            'ano' => date('Y')
        ]);

        // Assert
        $response->assertStatus(400);
        $response->assertJsonFragment(['erro' => 'Não há times disponíveis para a fase: quartas']);

        $this->assertDatabaseCount('partidas', 0);
    }

    /** @test */
    public function ignora_simulacao_com_mais_de_oito_times_passa_apenas_oito(): void 
    {
        // Arrange: Criamos 10 times (excedente)
        Time::factory()->count(10)->create();

        // Action
        //$response = $this->get('/simular-partida-teste');
        $response = $this->post('/simular-partida', [
            'nome_campeonato' => 'Campeonato de Teste',
            'ano' => date('Y')
        ]);
        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseCount('partidas', 1);
    }

    /** @test */
    public function gerar_partida_com_sucesso_com_oito_times(): void 
    {
    
        Time::factory()->count(8)->create();

        //$response = $this->get('/simular-partida-teste');
        $response = $this->post('/simular-partida', [
            'nome_campeonato' => 'Campeonato de Teste',
            'ano' => date('Y')
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('partidas', 1);
        $this->assertDatabaseCount('classificacoes', 2);
        $response->assertJsonStructure([
            'mensagem',
            'confronto',
            'placar'
        ]);
    }

    /** @test */
    public function gerar_uma_partida_valida(): void 
    {
        Time::factory()->count(8)->create();

        //$response = $this->get('/simular-partida-teste');
        $response = $this->post('/simular-partida', [
            'nome_campeonato' => 'Campeonato de Teste',
            'ano' => date('Y')
        ]);

        $response->assertStatus(200);
    }
    
    /** @test */
    public function valida_estatisticas_sao_atualizadas_corretamente(): void {

        $times = Time::factory()->count(8)->create();
        $mandante = $times[0];
        $visitante = $times[1];

        //$response = $this->get('/simular-partida-teste');
        $response = $this->post('/simular-partida', [
            'nome_campeonato' => 'Campeonato de Teste',
            'ano' => date('Y')
        ]);

        // Pegamos a última partida no banco para saber o placar real que saiu
        $partida = Partida::latest()->first();

        // Valida classificações do mandante
        $this->assertDatabaseHas('classificacoes', [
            'time_id' => $partida->time_mandante_id,
            'gols_pro' => $partida->gols_mandante,
            'gols_contra' => $partida->gols_visitante,
            'pontos' => $partida->gols_mandante > $partida->gols_visitante ? 3 : ($partida->gols_mandante == $partida->gols_visitante ? 1 : 0)
        ]);
    }

    /** @test */
    public function valida_desempate_time_com_maior_pontuacao_ganhador(): void {

        $campeonato = Campeonato::factory()->create();
        $timeA = Time::factory()->create(['nome' => 'Time A']);
        $timeB = Time::factory()->create(['nome' => 'Time B']);

        // Cria uma "vantagem" manual para o Time A no banco de classificações
        Classificacao::create([
            'time_id' => $timeA->id,
            'campeonato_id' => $campeonato->id,
            'pontos' => 6,
            'saldo_gols' => 5
        ]);

        Classificacao::create([
            'time_id' => $timeB->id,
            'campeonato_id' => $campeonato->id,
            'pontos' => 6,
            'saldo_gols' => 2
        ]);

        // Usamos reflexão ou tornamos o método público para testar o retorno direto
        $controller = new CampeonatoController();
        $method = new \ReflectionMethod($controller, 'desempatePorCampanha');
        $method->setAccessible(true);

        $vencedorId = $method->invoke($controller, $timeA->id, $timeB->id);

        $this->assertEquals($timeA->id, $vencedorId, "O Time A deveria vencer pelo saldo de gols.");

    }

    /** @test */
    public function valida_integracao_formato_do_script_python(): void {

        // Arrange: nomes de times para o script
        $mandante = "Time A";
        $visitante = "Time B";

        // Action: executa o teste que existe no Campeonatocontroller
        $result = Process::run("python3 " . base_path('teste.py') . " '{$mandante}' '{$visitante}'");

        // Assert 
        $this->assertTrue($result->successful(), "O script Python falhou ao ser executado.");

        // Saida
        $output = trim($result->output());

        // Valida se o output segue o padrão 'numero-numero' (ex: 2-1)
        $this->assertMatchesRegularExpression('/^\d+-\d+$/', $output, "O formato do placar Python está inválido: {$output}");

    }

    /** @test */
    public function valida_o_comportamento_quando_script_python_falha(): void {
       
        Process::fake([
            'python3*' => Process::result('', 'Erro simulado no Python', 1),

        ]);

        Time::factory()->count(8)->create();

        // Action
        //$response = $this->get('/simular-partid-teste');
        $response = $this->post('/simular-partida', [
            'nome_campeonato' => 'Campeonato de Teste',
            'ano' => date('Y')
        ]);

        // Assert: O controller deve retornar 500

        $response->assertStatus(500);
        $response->assertJsonFragment(['erro' => 'Falha ao rodar o script de teste Python']);

    }
}



