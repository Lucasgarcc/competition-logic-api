@extends('app')

@section('titulo', 'Estatísticas Gerais')

@section('content')
<div class="animate-in fade-in duration-700" ng-controller="MainController" ng-init="initEstatisticas()">
    
    <!-- HEADER SIMPLIFICADO -->
    <div class="mb-10 border-b border-slate-100 pb-8">
        <h1 class="text-4xl text-white font-black text-slate-800 italic uppercase tracking-tighter">
            Classificação & Partidas
        </h1>
        <p class="text-slate-400 font-medium mt-2">Dados consolidados de desempenho e histórico de confrontos.</p>
    </div>

    <div ng-if="!loading" class="grid grid-cols-1 gap-12">
        
        <!-- SEÇÃO: TABELA DE CLASSIFICAÇÃO GERAL -->
        <div  class="bg-slate-50 rounded-[2.5rem] p-8 border border-slate-100 shadow-inner">
            <h4 class="text-[11px] font-black text-blue-500 uppercase mb-8 tracking-[0.2em] flex items-center">
                <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span> Ranking Acumulado
            </h4>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] text-slate-400 font-black uppercase border-b-2 border-slate-200">
                            <th class="pb-5 px-2">Pos</th>
                            <th class="pb-5">Clube</th>
                            <th class="pb-5 text-center">J</th>
                            <th class="pb-5 text-center">V</th>
                            <th class="pb-5 text-center">E</th>
                            <th class="pb-5 text-center">D</th>
                            <th class="pb-5 text-right px-2">Pontos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="time in classificacaoGeral" class="group border-b border-slate-100 last:border-0 hover:bg-white/50 transition-colors">
                            <td class="py-5 px-2 font-black text-slate-300 group-hover:text-blue-500">#@{{ $index + 1 }}</td>
                            <td class="py-5 font-extrabold text-slate-700 uppercase tracking-tight">@{{ time.nome }}</td>
                            <td class="py-5 text-center font-bold text-slate-500">@{{ time.jogos }}</td>
                            <td class="py-5 text-center font-bold text-green-600">@{{ time.vitorias }}</td>
                            <td class="py-5 text-center font-bold text-slate-400">@{{ time.empates }}</td>
                            <td class="py-5 text-center font-bold text-red-400">@{{ time.derrotas }}</td>
                            <td class="py-5 text-right px-2">
                                <span class="bg-blue-600 text-white px-4 py-1.5 rounded-xl font-black text-xs">@{{ time.pontos }} PTS</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SEÇÃO: ÚLTIMAS PARTIDAS REALIZADAS -->
        <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
            <h4 class="text-[11px] font-black text-slate-400 uppercase mb-8 tracking-[0.2em] flex items-center">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span> Histórico de Confrontos
            </h4>
            <div class="space-y-4">
                <div ng-repeat="partida in historicoPartidas | limitTo:20" class="flex items-center justify-between p-6 bg-slate-50 rounded-2xl border border-slate-100 hover:border-blue-200 transition-all group">
                    <div class="flex-1 text-right font-black text-slate-700 uppercase italic">@{{ partida.time_mandante }}</div>
                    
                    <div class="flex items-center space-x-4 mx-8">
                        <span class="text-2xl font-black text-slate-900">@{{ partida.gols_mandante }}</span>
                        <span class="text-[10px] font-black text-slate-300 uppercase">vs</span>
                        <span class="text-2xl font-black text-slate-900">@{{ partida.gols_visitante }}</span>
                    </div>

                    <div class="flex-1 text-left font-black text-slate-700 uppercase italic">@{{ partida.time_visitante }}</div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection