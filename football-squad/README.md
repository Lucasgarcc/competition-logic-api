# Integração Customizada: Laravel + Neon PostgreSQL

Este documento detalha a implementação de um conector personalizado para garantir a conectividade estável entre o framework Laravel e o banco de dados serverless **Neon**.

## 🧐 O Desafio Técnico
O Neon utiliza um proxy de conexão para gerenciar o escalonamento automático. Para que a conexão funcione, o parâmetro `endpoint` deve ser enviado na string DSN (Data Source Name). 

O conector PostgreSQL padrão do Laravel não suporta nativamente a inclusão desses parâmetros extras via `.env`, o que exigiu uma intervenção na camada de infraestrutura (Injeção de Dependência).

## 🛠️ Passo a Passo da Configuração

### 1. Criação do Conector Customizado
O arquivo foi criado para interceptar a montagem da string de conexão e injetar as opções exigidas pelo Neon diretamente no PDO.

**arquivo:** `app/Database/Connectors/NeonPostgresConnector.php`

```php
<?php

namespace App\Database\Connectors;

use Illuminate\Database\Connectors\PostgresConnector;

class NeonPostgresConnector extends PostgresConnector
{
    /**
     * Adiciona SSL compatível com Neon e opções de conexão ao DSN.
     */
    protected function addSslOptions($dsn, array $config)
    {
        $dsn = parent::addSslOptions($dsn, $config);

        if (! empty($config['channel_binding'])) {
            $dsn .= ";channel_binding={$config['channel_binding']}";
        }

        if (! empty($config['connect_options'])) {
            $dsn .= ";options={$config['connect_options']}";
        }

        return $dsn;
    }
}
```

### .2 Registro no Service Provider (O "Coração" da Troca)
Para que o Laravel utilize o nosso conector customizado em vez do padrão, registramos o vínculo no Service Container. Isso instrui o framework a usar a nova peça de infraestrutura.

**Arquivo**: app/Providers/AppServiceProvider.php

```php
    <?php

    namespace App\Providers;

    use App\Database\Connectors\NeonPostgresConnector;
    use Illuminate\Support\ServiceProvider;

    class AppServiceProvider extends ServiceProvider
    {
        /**
        * Register any application services.
        */
        public function register(): void
        {
            // Substitui o conector pgsql padrão pela nossa versão customizada
            $this->app->bind('db.connector.pgsql', fn () => new NeonPostgresConnector);
        }

        public function boot(): void
        {
            //
        }
    }

```

### 3. Ajuste no Mapa de Configuração

Atualizamos o mapeamento de banco de dados para que o Laravel reconheça e aceite os novos parâmetros que definimos no arquivo de ambiente.

**Arquivo:** config/database.php

```php
    'pgsql' => [
        'driver' => 'pgsql',
        'url' => env('DATABASE_URL'),
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '5432'),
        'database' => env('DB_DATABASE', 'forge'),
        'username' => env('DB_USERNAME', 'forge'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'search_path' => 'public',
        'sslmode' => env('DB_SSLMODE', 'prefer'),
        'channel_binding' => env('DB_CHANNEL_BINDING'),
        'connect_options' => env('DB_CONNECT_OPTIONS'),
    ],
```

## 4. Configuração do Ambiente e Contrato (.env)
As credenciais devem ser preenchidas no .env. O .env.example foi atualizado para servir de guia.

**Arquivo:** .env.example

```
    DB_CONNECTION=pgsql
    DB_HOST=seu-projeto.us-east-2.aws.neon.tech
    DB_PORT=5432
    DB_DATABASE=neondb
    DB_USERNAME=neondb_owner
    DB_PASSWORD=sua_senha_secreta
    DB_SSLMODE=require
    DB_CONNECT_OPTIONS=endpoint=seu-endpoint-id-aqui
```

## ✅ Instruções de Validação e Uso

Para garantir que a configuração foi aplicada corretamente e que a integração com o Neon está operacional, execute os seguintes comandos no seu terminal:

### 1. Limpar Cache de Configuração
Remova quaisquer configurações antigas que possam estar armazenadas em cache para garantir que o Laravel leia as novas definições do seu `.env`:

```bash
php artisan config:clear
```

### 2. Verificar Dependências de Inspeção
Certifique-se de que o pacote doctrine/dbal está instalado. Ele é essencial para que comandos de gerenciamento e inspeção de banco de dados funcionem corretamente no Laravel:

```Bash
composer require doctrine/dbal
```

### 3. Testar a Conexão Real
Tente listar o status das migrações. Se o comando retornar a lista de tabelas (ou informar que não há migrações) sem erros de conexão, sua "ponte" com o Neon está 100% funcional:

```Bash
 php artisan migrate:status
```

## Sincronizar o banco remoto com local

1. Usando o modelo .sync-db.example.sh, você cria um arquivo executavél com nome sync-db.sh.

2. Entre no wsl ubuntu, abra o diretorio do arquivo dentro da pasta raiz, rode o comando executavél.

```wsl 
    chmod +x sync-db.sh
```

3. Após executá-lo de o seguinte comando para subir aplicação docker

```wsl
    ./vendor/bin/sail up -d
```

4. Rode o comando de sincronização 

```wsl
    ./sync-db.sh
```

5. Após ter exibido importando e SET..., use qualquer (postman, tablePlus, insominia), para ver dump do remoto ao banco local.