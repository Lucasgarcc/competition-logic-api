# Tournament Rules Engine

Este é um motor de backend desenvolvido em **Laravel 11** focado na gestão e automação de torneios com sistema eliminatório (*knockout stage*).

## Sobre o Projeto

O objetivo deste projeto é fornecer uma API robusta para processar todas as etapas de um campeonato de futebol, desde o sorteio inicial das quartas de final até a grande final, garantindo a aplicação rigorosa das regras de competição e critérios de desempate.

## 🛠️ Tecnologias e Arquitetura

- **Framework:** Laravel 11 (PHP 8.2+)
- **Banco de Dados:** PostgreSQL (via Neon Serverless)
- **Padrões de Projeto:** Service Pattern para isolamento da lógica de negócio.
- **Versionamento:** GitFlow e Conventional Commits.

## 🧠 Regras de Negócio Implementadas

O motor de regras processa o fluxo completo do torneio seguindo estas etapas:

1. **Sorteio Automatizado:** Divisão aleatória de 8 times em 4 jogos de Quartas de Final.
2. **Progressão de Fases:** Chaveamento automático para Semifinais, Disputa de 3º Lugar e Final.
3. **Lógica de Desempate:** Em caso de igualdade no placar, o sistema aplica sequencialmente:
    - **Saldo de Gols Acumulado:** (Gols Marcados - Gols Sofridos) durante o torneio.
    - **Prioridade de Inscrição:** Critério de desempate por ordem cronológica de registro no sistema.

## ⚙️ Configuração Local

1. Clone o repositório.
2. Configure o seu arquivo `.env` com as credenciais do Neon PostgreSQL.
3. Execute as migrations para preparar a estrutura:
   ```bash
   php artisan migrate