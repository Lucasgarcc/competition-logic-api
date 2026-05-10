# ⚽ Football Squad - Simulator API

Uma API de simulação de campeonatos de futebol desenvolvida com Laravel 10.50.2, Docker e Python.

O projeto foi estruturado para ser totalmente agnóstico ao sistema operacional, utilizando containers Docker para garantir consistência no ambiente de desenvolvimento, execução e testes.

---

# 🛠️ Tecnologias Principais

| Tecnologia | Descrição |
|---|---|
| Laravel 10.50.2 | Framework PHP principal da aplicação |
| PHP 8.5.6 | Versão utilizada pelo projeto |
| PostgreSQL| Banco de dados relacional |
| [Neon Serverless Postgres](https://neon.tech/) | Banco PostgreSQL hospedado na nuvem |

| Docker | Containerização da aplicação |
| Laravel Sail | Ambiente Docker oficial do Laravel |
| Python 3 | Engine responsável pela simulação das partidas |

---

# 💻 Configuração para Usuários Windows (WSL2)

Para evitar problemas de permissões, performance e sincronização de arquivos, o projeto deve ser executado dentro do WSL2.

## 1. Abra sua distribuição Linux

```bash
wsl -d Ubuntu
```

---

## 2. Clone o projeto dentro do sistema Linux

Exemplo recomendado:

```bash
/home/seu-usuario/projects
```

⚠️ Evite executar o projeto em diretórios montados do Windows:

```bash
/mnt/c/
```

---

# 🚀 Guia de Instalação

## 1. Clonagem do Projeto

```bash
git clone https://github.com/seu-usuario/football-squad.git
cd football-squad
```

---

## 2. Instalação das Dependências

O Composer será executado via Docker, portanto não é necessário ter PHP instalado localmente.

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

---

# ⚙️ Configuração do Ambiente

## 1. Crie o arquivo `.env`

```bash
cp .env.example .env
```

---

## 2. Configure as credenciais do Neon.tech

Exemplo:

```env
DB_CONNECTION=pgsql
DB_HOST=ep-your-project-id.aws.neon.tech
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

---

# 🐳 Inicialização do Ambiente Docker

Suba os containers utilizando o Laravel Sail:

```bash
./vendor/bin/sail up -d
```

---

# 🗄️ Preparação do Banco de Dados

## 1. Gere a chave da aplicação

```bash
./vendor/bin/sail artisan key:generate
```

---

## 2. Execute as migrations

```bash
./vendor/bin/sail artisan migrate:fresh
```

---

## 3. Popule os dados iniciais

O Seeder é obrigatório para registrar os times no banco de dados.

```bash
./vendor/bin/sail artisan db:seed --class=TimeSeeder
```

---

# 🎮 Como Testar a API

## Executando uma Simulação

Com o ambiente em execução, acesse:

```txt
http://localhost/teste-simulacao
```

---

## Fluxo da Simulação

O sistema:

1. Sorteia dois times aleatórios do banco
2. Envia os nomes para o script Python `teste.py`
3. Recebe o placar gerado
4. Calcula o vencedor da partida
5. Persiste o `vencedor_id` na tabela `partidas`

---

# ✅ Validando a Persistência no Banco

Para verificar se os dados foram salvos corretamente no Neon:

```bash
./vendor/bin/sail artisan tinker --execute="print_r(App\Models\Partida::latest()->first()->toArray())"
```

---

# 📂 Estrutura e Padrões Utilizados

## Conventional Commits

O projeto segue o padrão de commits semânticos:

```txt
feat:
fix:
refactor:
docs:
chore:
```

---

## GitFlow

Fluxo de versionamento baseado em:

- `main`
- `develop`
- `feature/*`
- `hotfix/*`

---

## Segurança no Model

O model `Partida` utiliza:

```php
$fillable
```

para garantir segurança na persistência de dados como `vencedor_id`.

---

# 🧠 Arquitetura do Projeto

A aplicação integra:

- Laravel como camada principal da API
- PostgreSQL/Neon como persistência de dados
- Python para processamento da lógica de simulação
- Docker como ambiente isolado e padronizado

Essa arquitetura facilita:

- Escalabilidade
- Portabilidade
- Padronização do ambiente
- Facilidade de onboarding
- Independência de sistema operacional

---

# 📌 Requisitos

## Necessário ter instalado

- Docker Desktop
- WSL2 (Windows)
- Git

---

# 👨‍💻 Autor

Desenvolvido por Lucas Garcia com foco em engenharia de software, integração entre tecnologias e arquitetura escalável.