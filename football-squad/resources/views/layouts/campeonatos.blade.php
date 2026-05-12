@extends('app')

@section('titulo', 'Galeria de Campeonatos')

@section('content')
<div class="animate-in fade-in duration-500" ng-controller="MainController" ng-init="initGaleria()">
    
    <div class="mb-10 border-b border-slate-100 pb-8">
        <h1 class="text-4xl font-black text-white italic uppercase tracking-tighter">
            🏆 Galeria de Campeonatos
        </h1>
        <p class="text-slate-400 font-medium mt-2">Consulte todos os torneios finalizados e estatísticas globais</p>
    </div>

    <div class="space-y-8">
        <!-- O array que vem do seu JSON é todosOsCampeonatos -->
        <div ng-if="!loading"  ng-repeat="camp in todosOsCampeonatos" 
             class="bg-slate-50 rounded-[2rem] p-8 border border-slate-100 shadow-sm hover:shadow-md transition-all">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <span class="text-[10px] font-black text-blue-500 uppercase tracking-[0.3em]">Torneio Finalizado</span>
                    <h2 class="text-2xl font-black text-slate-800 mt-1">@{{ camp.nome }}</h2>
                </div>
                <div class="bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm">
                    <!-- Formatando a data que vem no JSON -->
                    <span class="text-slate-400 text-xs font-bold">📅 @{{ camp.ano }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Coluna de Ataque: Ordenando por gols_pro (descendente) -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100">
                    <h4 class="text-[11px] font-black text-orange-500 uppercase mb-4 tracking-widest flex items-center">
                        <span class="w-2 h-2 bg-orange-500 rounded-full mr-2"></span> Melhores Ataques
                    </h4>
                    <div class="space-y-3">
                        <!-- 'gols_pro' é a chave que vimos no seu JSON -->
                        <div ng-repeat="estat in camp.estatisticas | orderBy:'-gols_pro' | limitTo:3" class="flex justify-between text-sm">
                            <span class="font-bold text-slate-600">Time @{{ estat.time_id }}</span>
                            <span class="font-black text-slate-900">@{{ estat.gols_pro }} Gols</span>
                        </div>
                    </div>
                </div>

                <!-- Coluna de Defesa: Ordenando por gols_contra (ascendente) -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100">
                    <h4 class="text-[11px] font-black text-indigo-500 uppercase mb-4 tracking-widest flex items-center">
                        <span class="w-2 h-2 bg-indigo-500 rounded-full mr-2"></span> Defesas Sólidas
                    </h4>
                    <div class="space-y-3">
                        <!-- 'gols_contra' é a chave do JSON. Menos gols contra = melhor defesa -->
                        <div ng-repeat="estat in camp.estatisticas | orderBy:'gols_contra' | limitTo:3" class="flex justify-between text-sm">
                            <span class="font-bold text-slate-600">Time @{{ estat.time_id }}</span>
                            <span class="font-black text-slate-900">@{{ estat.gols_contra }} GS</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div ng-if="todosOsCampeonatos.length === 0" class="text-center py-20">
            <div class="text-6xl mb-4 opacity-20">📁</div>
            <p class="text-slate-400 font-bold italic">Nenhum campeonato registrado no histórico.</p>
        </div>
    </div>
</div>

@endsection