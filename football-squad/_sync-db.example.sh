#!/bin/bash

# Configurações
NEON_URL="postgresql://USER:PASSWORD@ep-floral-fire-a5894rst.us-east-2.aws.neon.tech/NAMEDATABASE?sslmode=require&channel_binding=require"
USER= # usuário do seu .env local
PASSWORD=  # senha do seu .env local

# Função de carregamento (Spinner)
show_spinner() {
    local pid=$1
    local message=$2
    local spin='-\|/'
    local i=0
    while kill -0 $pid 2>/dev/null; do
        i=$(( (i+1) % 4 ))
        printf "\r[%c] %s..." "${spin:$i:1}" "$message"
        sleep .1
    done
    printf "\r[✅] %s concluído!     \n" "$message"
}

echo "--- Iniciando Sincronização ---"

# 1. Baixando dados do Neon
./vendor/bin/sail shell -c "pg_dump '$NEON_URL' --clean --if-exists --no-owner --no-privileges -f storage/app/neon_dump.sql" > /dev/null 2>&1 & 
# O & acima envia para o background
pid_dump=$! # Captura o PID do comando anterior
show_spinner $pid_dump "Baixando dados do Neon"

# 2. Importando para o banco local
./vendor/bin/sail shell -c "PGPASSWORD='$PASSWORD' psql -h pgsql -U '$USER' -d footballsquad -f storage/app/neon_dump.sql" > /dev/null 2>&1 &
pid_import=$!
show_spinner $pid_import "Importando para o banco local (Docker)"

echo "------------------------------------"
echo "✅ Sincronização concluída com sucesso!"