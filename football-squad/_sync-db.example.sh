# Carrega as variáveis do Neon (ou cole a URL direta aqui)
NEON_URL="postgresql://USER:PASSWORD@ep-floral-fire-a5894rst.us-east-2.aws.neon.tech/NAMEDATABASE?sslmode=require&channel_binding=require"
USER= # usuário do seu .env local
PASSWORD=  # senha do seu .env local

# Função de carregamento
show_spinner() {
    local pid=$1
    local message=$2
    local spin='-\|/'
    local i=0
    while kill -0 $pid 2>/dev/null; do
        i=$(( (i+1) % 4 ))
        printf "\r[%c] %s" "${spin:$i:1}" "$message"
        sleep .1
    done
    printf "\r[✅] %s concluído!     \n" "$message"
}


# Executa o dump dentro do container do Sail para garantir que o pg_dump esteja disponível
./vendor/bin/sail shell -c "pg_dump '$NEON_URL' --clean --if-exists --no-owner --no-privileges -f storage/app/neon_dump.sql"

show_spinner $! "Baixando dados do Neon"

# Importa o arquivo gerado para o banco que o Sail está rodando
# Usando shell -c para garantir que o psql execute o arquivo e saia
./vendor/bin/sail shell -c "PGPASSWORD='$PASSWORD' psql -h pgsql -U '$USER' neondb_owner -d footballsquad -f storage/app/neon_dump.sql"

show_spinner $! "🔄 Importando para o Docker local"

echo "✅ Sincronização concluída!"