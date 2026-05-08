# 🏆 Competition Logic API

Uma engine de backend robusta desenvolvida para gerenciar o ciclo de vida completo de competições esportivas eliminatórias. Este projeto foca na automação de chaves de jogos (brackets) e na aplicação rigorosa de critérios de desempate complexos.

## Diferenciais Técnicos

- **Arquitetura:** Implementação do *Service Pattern* para isolar a lógica de competição dos controladores, garantindo um código testável e modular.
- **Motor de Desempate:** Algoritmo customizado que processa sequencialmente:
  1. Saldo de Gols Acumulado.
  2. Prioridade de Registro (Timestamp).
- **Persistência:** Otimizado para **PostgreSQL (Neon Serverless)** com tratamento de concorrência.
- **Padronização:** Histórico de desenvolvimento baseado em *Conventional Commits* e metodologia *GitFlow*.

## 🛠️ Tecnologias

- **Framework:** Laravel 11
- **Linguagem:** PHP 8.3
- **Banco de Dados:** PostgreSQL (Neon)
- **Ferramentas:** Eloquent ORM, Migrations, Seeders.

## 📂 Estrutura do Repositório

O código principal da aplicação está localizado no diretório:
- [`/football-squad`](./football-squad): Contém toda a lógica Laravel, Models, Services e Configurações.

## ⚙️ Instalação Rápida

1. Clone o repositório:
   ```bash
   git clone [https://github.com/Lucasgarcc/competition-logic-api.git](https://github.com/Lucasgarcc/competition-logic-api.git)