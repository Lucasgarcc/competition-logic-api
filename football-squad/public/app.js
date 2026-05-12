var app = angular.module('campeonatoApp', [])
.controller('MainController', ['$scope', '$http', '$timeout', function($scope, $http, $timeout) {
    
    // Configurações
    const nomesFases = {
        'quartas': 'Quartas de Final',
        'semifinal': 'Semifinais',
        'final': 'GRANDE FINAL 🏆',
        'terceiro_lugar': 'Disputa de 3º Lugar'
    };

    // 1. Inicialização direta no $scope
    $scope.estado = 'inicio'; // 'inicio', 'simulacao', 'finalizado'
    $scope.simulando = false;
    $scope.novoTorneio = true;
    $scope.vencedor = '';
    $scope.loading = false;
    $scope.exibirResultado = false;
    $scope.campeonatoId = null;
    $scope.faseAtual = 'quartas';
    $scope.textoBotao = 'Simular Primeira Partida';
    $scope.form = {
        nomeCampeonato: ''
    };


    // 2. Função de Carga Inicial
    $scope.carregarDadosIniciais = function() {
        $scope.loading = true;
        $scope.exibirResultado = false;
        $scope.simulando = false;
        $http.get('/campeonato/status-atual').then(function(response) {

            const dados = response.data;
            
            $scope.loading = false;
            $scope.exibirResultado = true;
            $scope.exibirResultado = true;
            $scope.campeonatoId = dados.id;
            $scope.form.nomeCampeonato = dados.nome;
            $scope.faseAtual = nomesFases[dados.ultima_partida.fase] || dados.ultima_partida.fase;
            $scope.textoBotao = 'Simular Próxima Partida';

            $scope.partidaFinalizada = {
                detalhes: dados.ultima_partida
            };

            $scope.mandante = dados.detalhes?.time_mandante; 
            $scope.golsMandante = dados.detalhes?.gols_mandante;
            $scope.visitante = dados.detalhes?.time_visitante;
            $scope.golsVisitante = dados.detalhes?.gols_visitante;

            $scope.placar = dados.placar;

            $scope.classificacao = dados.ranking;

    
            dados.ultima_partida?.vencedor_id === dados.ultima_partida?.time_mandante_id  ? $scope.vencedor = dados.ultima_partida?.time_mandante.nome : $scope.vencedor =  dados.ultima_partida?.time_visitante.nome

            $pontuacao = 


            console.log($scope.vencedor)
            //  AS ESTATÍSTICAS RÁPIDAS -
            if (dados.ranking && dados.ranking.length > 0) {
               
                $scope.estatisticas = {

                    // Ordena por gols_pro (Ataque) decrescente
                    ataque: angular.copy(dados.ranking)
                        .sort((a, b) => b.gols_pro - a.gols_pro)
                        .map(item => ({ nome: item.nome, gols: item.gols_pro })),

                    // Ordena por gols_contra (Defesa) crescente
                    defesa: angular.copy(dados.ranking)
                        .sort((a, b) => a.gols_contra - b.gols_contra)
                        .map(item => ({ nome: item.nome, gols_sofridos: item.gols_contra }))
                };
            }

            if(dados.ultima_partida?.encerrada_em) {
                $scope.estado = 'simulacao';
                $scope.textoBotao = (dados.ultima_partida?.fase === 'final') ? "DISPUTAR A TAÇA 🏆" : "Próxima Partida";
            }
        }).catch(function(err) {
            console.error('Erro na carga inicial', err);
            $scope.textoBotao = 'Iniciar Partidas';
        });
    };

    // 3. Função para Iniciar
    $scope.simular = function() {
        $scope.estado = 'simulacao';
    };
 
    $scope.carregarDadosIniciais();   

    // 4. Função Próxima Partida
    $scope.proximaPartida = function() {

        if (!$scope.form.nomeCampeonato || $scope.form.nomeCampeonato.length < 3) {
            alert("Por favor, dê um nome ao seu campeonato antes de começar!");
            return;
        }
        
        $scope.loading = true;
        $scope.exibirResultado = false; // Esconde o antigo enquanto carrega
        
        const payload = {
            nome_campeonato: $scope.form.nomeCampeonato, 
            campeonato_id: $scope.campeonatoId 
        }
    
        $http.post('/simular-partida', payload)
            .then(function(response) {
                const dados = response.data; // Aqui estão os dados do Aliança/Jardim
    
                $timeout(function() {
                    $scope.partidaFinalizada = { detalhes: dados.detalhes };
                    $scope.faseAtual = nomesFases[dados.detalhes.fase] || dados.detalhes.fase;
                    $scope.campeonatoId = dados.detalhes.campeonato_id;
                    $scope.loading = false;
                    $scope.exibirResultado = true;

                    $scope.simulando = response.data.em_andamento;

                    // Lógica do Confete: verifica se a fase que acabou de ocorrer é a final
                    if ($scope.faseAtual === 'final' && $scope.partidaFinalizada.vencedor_id) {
                        // Pequeno delay para o card aparecer na tela antes do confete
                        $timeout(function() {
                            confetti({
                                particleCount: 150,
                                spread: 70,
                                origin: { y: 0.6, x: 0.6 },
                                zIndex: 999
                            });
                        }, 300);
                    }
                }, 1500);
            })
            .catch(function(err) {
                $scope.loading = false;
                $scope.exibirResultado = true;
                alert("Erro ao processar partida.");
            });
    };

    // Dentro do seu controller Angular
    $scope.initEstatisticas = function() {
        $http.get('/estatisticas-globais').then(function(response) {
            $scope.classificacaoGeral = response.data.classificacaoGeral;
            $scope.historicoPartidas = response.data.historicoPartidas;
        });
    };

    $scope.initEstatisticas();

    $scope.initGaleria = function() {
        $scope.loading = true; // Boa prática para feedback visual
        
        $http.get('/campeonatos')
            .then(function(response) {
                
                $scope.todosOsCampeonatos = response.data;
                console.log(response.data);
            })
            .catch(function(error) {
                console.error('Erro ao carregar galeria:', error);
            })
            .finally(function() {
                $scope.loading = false;
            });
    };

    $scope.initGaleria();

    // 5. Função para Reiniciar o fluxo (Adicione esta função)
    $scope.voltarAoInicio = function() {
        $scope.simulando = false;
        $scope.novoTorneio = true;
        $scope.estado = 'inicio';
        $scope.campeonatoId = null;
        $scope.form.nomeCampeonato = '';
        $scope.exibirResultado = false;
        $scope.partidaFinalizada = null;
        $scope.faseAtual = 'quartas';
    };

    $scope.voltarAoInicio();
}]);

app.run(['$http', function($http) {
    $http.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}]);