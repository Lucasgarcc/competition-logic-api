import sys
import random

def simulador_partida(time_mandante, time_visitante):
    #lógica do gols: Aleatórios de 0 a 5
    gols_m = random.randint(0, 5)
    gols_v = random.randint(0, 5)

    # Em caso de empate, simule uma decisão rápida (opcional para o teste)
    # Aqui apenas retormanos o placar no formatir padrão.
    print(f'{gols_m}-{gols_v}')

if __name__ == '__main__':
    # Verifica se existe times, se os nomes foram passados

    if len(sys.argv) > 2:

        madante = sys.argv[1]
        visitante = sys.argv[2]
        simulador_partida(madante, visitante)

    else: 
        # Se rodar sem argumentos, apenas gera um placar genérico
        simulador_partida('Madante', 'Visitante')