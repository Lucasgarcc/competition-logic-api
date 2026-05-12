<!DOCTYPE html>
<html lang="pt-br" ng-app="campeonatoApp">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sports Cup - Dashboard</title>
    
    <!-- Google Fonts: Nunito -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200..1000&display=swap" rel="stylesheet">
    
    <!-- FontAwesome para ícones do Menu -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Nunito', 'sans-serif'] },
                    colors: {
                        dark: '#0F172A',
                        sidebar: '#1E293B'
                    }
                },
            },
        }
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script src="{{ asset('app.js') }}"></script>

    <style>
        * { font-family: 'Nunito', sans-serif !important; }
        [ng\:cloak], [ng-cloak], .ng-cloak { display: none !important; }

        body {

        padding: 0;
        margin: 0;
        min-height: 100vh;
        box-sizing: border-box;
        @apply font-sans; /* Se configurar no script abaixo */
        /* Gradiente Ajustado */
        background: hsla(89, 87%, 22%, 1);
        background: linear-gradient(45deg, hsla(89, 87%, 22%, 1) 0%, hsla(171, 42%, 19%, 1) 54%, hsla(202, 100%, 11%, 1) 99%);
        background-repeat: no-repeat;
        background-attachment: fixed;
        min-height: 100%;
        background: -moz-linear-gradient(45deg, hsla(89, 87%, 22%, 1) 0%, hsla(171, 42%, 19%, 1) 54%, hsla(202, 100%, 11%, 1) 99%);
        background: -webkit-linear-gradient(45deg, hsla(89, 87%, 22%, 1) 0%, hsla(171, 42%, 19%, 1) 54%, hsla(202, 100%, 11%, 1) 99%);
        }

        /* Animações originais mantidas */
        .bola-loading { animation: bounce 0.6s infinite alternate; }
        @keyframes bounce { 
            from { transform: translateY(10px); } 
            to { transform: translateY(-40px); } 
        }
        
        .sombra-loading {
            width: 40px; height: 8px; background: rgba(0, 0, 0, 0.2);
            border-radius: 50%; margin: 10px auto 0;
            animation: shadowScale 0.6s infinite alternate;
        }
        @keyframes shadowScale { from { transform: scale(1); opacity: 1; } to { transform: scale(0.5); opacity: 0.4; } }

        .animate-bounce-in { animation: bounceIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55); }
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3); }
            50% { opacity: 1; transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .sidebar-item-active {
            @apply bg-blue-600 text-white shadow-lg shadow-blue-900/20;
        }
    </style>
</head>
<body class="ng-cloak text-slate-200" ng-controller="MainController">

    <!-- ESTRUTURA DASHBOARD -->
    <div class="flex min-h-screen">
        <!-- SIDEBAR -->
        <aside class="w-72 bg-sidebar border-r border-slate-700 hidden md:flex flex-col fixed h-full z-50">
            <div class="p-8 text-center">
                <h1 class="text-2xl font-black text-white italic tracking-tighter">
                    <a href="{{ url('/') }}" class="flex items-center space-x-3 p-4 rounded-xl hover:bg-white/10">
                        <span class="material-symbols-outlined">
                            Sports<span class="text-blue-500">CUP</span>
                        </span>
                    </a>
                </h1>
                <p class="text-[10px] text-slate-400 font-bold uppercase mt-1 tracking-widest">Campeonato de Bairro</p>
            </div>

            <!-- SIDEBAR -->
            <nav class="space-y-2">
                <!-- Link para a Home (Simulador) -->
                <a href="{{ url('/') }}" class="flex items-center space-x-3 p-4 rounded-xl hover:bg-white/10">
                
                    <i class="fa-solid fa-house"></i><span>Início</span>
                </a>
            
                <!-- Link para Campeonatos -->
                <a href="{{ url('/campeonatos') }}" class="flex items-center space-x-3 p-4 rounded-xl hover:bg-white/10">
                    <i class="fa-solid fa-trophy"></i> <span>Campeonatos</span>
                </a>
            
                <!-- Link para Estatísticas -->
                <a href="{{ url('/estatisticas') }}" class="flex items-center space-x-3 p-4 rounded-xl hover:bg-white/10">
                    <i class="fa-solid fa-chart-line"></i> <span>Estatísticas</span>
                </a>
            </nav>

            <div class="p-6 border-t border-slate-700">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center font-black text-white">JG</div>
                    <div>
                        <p class="text-base font-black text-white">José Gustavo</p>
                        <p class="text-[12px] text-slate-400">Organizador</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- CONTEÚDO PRINCIPAL -->
        <main class="flex-1 md:ml-72 min-h-screen p-4 md:p-10 flex flex-col">
            @if(Request::is('/'))
                <!-- HEADER DA PÁGINA DINÂMICO -->
                <header  class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight leading-none">
                            <span ng-if="estado === 'inicio' || estado === 'simulacao'">Painel de Controle</span>
                            <span ng-if="estado === 'historico'">Galeria de Troféus</span>
                        </h2>
                        <p class="text-slate-400 text-sm mt-2 font-medium opacity-80">Bem-vindo de volta ao simulador.</p>
                    </div>

                    <!-- Badge de Status Rápido -->
                    <div class="flex items-center space-x-2 bg-white/10 backdrop-blur-md px-4 py-2 rounded-2xl border border-white/10">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-white/70">Sistema Online</span>
                    </div>
                </header>

                <!-- CONTAINER DO CARD  -->
                <div class="flex-1 flex flex-col">
                    <div class="w-full max-w-5xl mx-auto bg-white rounded-[2.5rem] shadow-2xl p-8 md:p-12 text-slate-800 overflow-hidden relative flex-1">
                        
                        <!-- ESTADO: INICIO -->
                        <div ng-if="estado === 'inicio' " class="h-full flex flex-col items-center justify-center text-center py-10">
                            <div class="mb-8 relative">
                                <div class="absolute inset-0 bg-blue-400/20 blur-3xl rounded-full"></div>
                                <div class="relative inline-flex p-6 bg-blue-50 rounded-full text-blue-600 text-5xl shadow-inner">⚽</div>
                            </div>
                            <h2 class="text-4xl font-black mb-4 tracking-tighter">Pronto para o apito inicial?</h2>
                            <p class="text-slate-500 mb-10 font-medium max-w-md mx-auto">Configure os times e inicie a jornada rumo ao título máximo com processamento em tempo real.</p>
                            <button ng-click="simular()" class="bg-blue-600 text-white px-16 py-6 rounded-2xl text-xl font-black hover:bg-blue-700 transition-all transform hover:scale-105 shadow-2xl shadow-blue-300/50 active:scale-95">
                                Começar Torneio
                            </button>
                        </div>

                        <!-- ESTADO: SIMULACAO -->
                        <div ng-if="estado === 'simulacao'"  class="animate-in fade-in duration-500">
                            
                            <div class="flex flex-col items-center">
                                <span class="bg-blue-100 text-blue-700 text-[11px] font-black px-5 py-2 rounded-full uppercase mb-8 tracking-[0.2em] shadow-sm">
                                    🏆 Fase Atual: @{{ faseAtual }}
                                </span>

                                <!-- Form Nome Campeonato -->
                                <div ng-if="!exibirResultado && !loading" class="h-full flex flex-col items-center justify-center text-center py-10">
                                    <label class="block text-slate-400 text-[10px] font-black uppercase mb-4 tracking-[0.2em]">Título da Competição</label>
                                    <input type="text" ng-model="form.nomeCampeonato" ng-disabled="campeonatoId"
                                        class="w-full px-6 py-5 border-2 border-slate-100 rounded-2xl text-center text-2xl font-bold focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all shadow-sm placeholder:text-slate-200"
                                        placeholder="Ex: Champions League 2026">
                                    
                                    <button ng-click="proximaPartida()" ng-disabled="form.nomeCampeonato.length < 3"
                                            class="mt-8 w-full bg-slate-900 text-white py-5 rounded-2xl font-black text-lg hover:bg-black transition-all disabled:opacity-20 disabled:cursor-not-allowed shadow-xl">
                                        @{{ textoBotao }}
                                    </button>
                                </div>

                                <!-- Card de Placar -->
                                <div ng-if="exibirResultado && !loading && !simulando" class="w-full animate-bounce-in max-w-4xl">
                                    <div class="bg-gradient-to-br from-slate-900 to-green-900 rounded-[2.5rem] p-1 shadow-2xl">
                                        <div class="bg-white/5 backdrop-blur-sm rounded-[2.4rem] p-8 md:p-14 border border-white/10 relative overflow-hidden">
                                            
                                            <!-- Reflexo Decorativo Superior -->
                                            <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-white/5 to-transparent pointer-events-none"></div>

                                            <div class="flex flex-col md:flex-row items-center justify-between gap-8 text-white relative z-10">
                                                <!-- Time Mandante -->
                                                <div class="text-center flex-1 group">
                                                    <div class="w-24 h-24 bg-white/10 rounded-3xl mx-auto mb-6 flex items-center justify-center text-4xl shadow-inner border border-white/5 group-hover:scale-110 transition-transform">🛡️</div>
                                                    <h3 class="font-black text-xl uppercase tracking-tighter italic">@{{ partidaFinalizada.detalhes.time_mandante.nome }}</h3>
                                                </div>
                                                
                                                <!-- Placar Central -->
                                                <div class="flex flex-col items-center">
                                                    <div class="flex items-center space-x-6 md:space-x-10">
                                                        <span class="text-7xl md:text-8xl font-black text-yellow-400 drop-shadow-2xl">@{{ partidaFinalizada.detalhes.gols_mandante }}</span>
                                                        <div class="flex flex-col items-center">
                                                            <span class="text-2xl font-black opacity-30 text-white italic">VS</span>
                                                            <div class="h-1 w-8 bg-yellow-400/30 rounded-full mt-2"></div>
                                                        </div>
                                                        <span class="text-7xl md:text-8xl font-black text-yellow-400 drop-shadow-2xl">@{{ partidaFinalizada.detalhes.gols_visitante }}</span>
                                                    </div>
                                                </div>

                                           
                                                <!-- Time Visitante -->
                                                <div class="text-center flex-1 group">
                                                    <div class="w-24 h-24 bg-white/10 rounded-3xl mx-auto mb-6 flex items-center justify-center text-4xl shadow-inner border border-white/5 group-hover:scale-110 transition-transform">🌌</div>
                                                    <h3 class="font-black text-xl uppercase tracking-tighter italic">@{{ partidaFinalizada.detalhes.time_visitante.nome }}</h3>
                                                </div>
                                            </div>
                                            
                                            <!-- Ações de Rodapé do Card -->
                                            <div class="mt-12 pt-8 border-t border-white/10 flex flex-wrap justify-center gap-4 relative z-10">
                                                <button ng-click="proximaPartida()" ng-if="partidaFinalizada.detalhes.fase !== 'final'"
                                                        class="bg-yellow-400 text-blue-900 px-12 py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-white transition-all transform hover:scale-105 shadow-xl shadow-yellow-500/20">
                                                    Simular Próxima ⚽
                                                </button>
                                                <button ng-click="voltarAoInicio()" ng-if="partidaFinalizada.detalhes.fase === 'final'"
                                                        class="bg-green-500 text-white px-12 py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-green-600 transition-all shadow-xl shadow-green-500/30 flex items-center">
                                                        @{{ textoBotao }} 
                                                </button>
                                            </div>
                                            <div class="mt-12 pt-8 border-t border-white/10 flex flex-wrap justify-center gap-4 relative z-10">
                                                <h3 class="font-black text-green-100  text-xl uppercase tracking-tighter italic">@{{vencedor}} É Campeão 🏅</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Loading Melhorado -->
                                <div ng-show="loading" class="py-24 text-center">
                                    <div class="relative inline-block">
                                        <div class="text-8xl bola-loading">⚽</div>
                                        <div class="sombra-loading mx-auto"></div>
                                    </div>
                                    <div class="mt-12 space-y-2">
                                        <p class="font-black text-slate-800 uppercase tracking-[0.4em] animate-pulse">Analisando Táticas</p>
                                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">O VAR está revisando os lances...</p>
                                    </div>
                                </div>
                            </div>

                            <!-- SEÇÃO DE DADOS (Tabelas) -->
                            <div ng-if="exibirResultado && !loading" class="mt-16 pt-16 border-t border-slate-100 animate-in slide-in-from-bottom duration-700">
                                <div class="flex items-center justify-between mb-10">
                                    <div>
                                        <h3 class="text-2xl font-black text-slate-800 tracking-tighter italic">Painel de Performance</h3>
                                        <div class="h-1.5 w-12 bg-blue-600 rounded-full mt-2"></div>
                                    </div>
                                </div>

                                <div class="grid w-full  grid-cols-1 gap-10">
                    
                                    <!-- Estatísticas Rápidas -->
                                    <div class="space-y-6">
                                        <div class="bg-green-50 w-full rounded-[2rem] p-8 border border-green-100 group hover:shadow-lg hover:shadow-green-100 transition-all">
                                            <h4 class="text-[10px] font-black text-green-600 uppercase mb-6 tracking-[0.2em]">🔥 Ataques Impiedosos</h4>
                                            <div ng-repeat="time in estatisticas.ataque | limitTo:3" class="flex items-center justify-between py-3 border-b border-green-200/30 last:border-0">
                                                <span class="font-bold text-slate-700 flex items-center">
                                                    <span class="text-green-300 mr-2">#@{{ $index + 1 }}</span> @{{ time.nome }}
                                                </span>
                                                <span class="font-black text-green-600 bg-white px-3 py-1 rounded-lg">@{{ time.gols }} Gols</span>
                                            </div>
                                        </div>

                                        <div class="bg-indigo-50 rounded-[2rem] w-full  p-8 border border-indigo-100 group hover:shadow-lg hover:shadow-indigo-100 transition-all">
                                            <h4 class="text-[10px] font-black text-indigo-400 uppercase mb-6 tracking-[0.2em]">🛡️ Muralhas Defensivas</h4>
                                            <div ng-repeat="time in estatisticas.defesa | limitTo:3" class="flex items-center justify-between py-3 border-b border-indigo-200/30 last:border-0">
                                                <span class="font-bold text-slate-700 flex items-center">
                                                    <span class="text-indigo-300 mr-2">#@{{ $index + 1 }}</span> @{{ time.nome }}
                                                </span>
                                                <span class="font-black text-indigo-600 bg-white px-3 py-1 rounded-lg">@{{ time.gols_sofridos }} GS</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Se não for a home, o Laravel injeta a blade aqui -->
                @yield('content')
            @endif

            <!-- FOOTER DO CONTEÚDO -->
            <footer class="mt-8 text-center md:text-left">
                <p class="text-slate-100 text-[10px] font-bold uppercase tracking-[0.3em]">&copy; 2026 Flux Cup Engine - Advanced Simulation Mode</p>
            </footer>
        </main>
    </div>

</body>
</html>