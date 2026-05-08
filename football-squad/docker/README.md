## 🐳 Guia de Operação Docker (Laravel Sail)

O ambiente foi configurado utilizando Docker Desktop + WSL2, garantindo isolamento total do PHP e do PostgreSQL.

### 🔌 Portas e Serviços
Ao subir o ambiente com `sail up -d`, os seguintes serviços ficam disponíveis:

| Serviço | Porta Local | Acesso |
| :--- | :--- | :--- |
| **Aplicação (Laravel)** | `80` | [http://localhost](http://localhost) |
| **Vite (Frontend Assets)** | `5173` | Automático via Blade |
| **PostgreSQL (Local)** | `5432` | Via Client SQL (ex: DBeaver) |

### 🚀 Comandos de Verificação

Para garantir que tudo está ok, execute no terminal do WSL:

Observação: Entre na pasta correta do projeto.

1. **Verificar se os containers estão vivos:**
   ```bash
   ./vendor/bin/sail ps

2. **Verificar containers ativos:**

```Bash
./vendor/bin/sail ps
```
3. **Validar conexão com o banco:**

```Bash
./vendor/bin/sail artisan migrate:status
```