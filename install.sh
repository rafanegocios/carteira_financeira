#!/bin/bash



echo "=== Iniciando instalação do sistema de Carteira Financeira ==="

# Verificar se o Docker e Docker Compose estão instalados
if ! command -v docker &> /dev/null || ! command -v docker-compose &> /dev/null; then
    echo "ERRO: Docker e/ou Docker Compose não estão instalados."
    echo "Por favor, instale o Docker e o Docker Compose antes de continuar."
    exit 1
fi


if [ ! -f .env ]; then
    echo "Criando arquivo .env..."
    cp .env.example .env

    RANDOM_PASSWORD=$(openssl rand -base64 12)
    sed -i "s/DB_PASSWORD=password/DB_PASSWORD=$RANDOM_PASSWORD/" .env

    echo "Arquivo .env criado com sucesso."
else
    echo "Arquivo .env já existe. Mantendo configurações atuais."
fi

# Iniciar os containers Docker
echo "Iniciando os containers Docker..."
docker-compose up -d

# Aguardar alguns segundos para que os containers estejam completamente inicializados
echo "Aguardando containers inicializarem..."
sleep 10

# Instalar dependências do Composer
echo "Instalando dependências do Composer..."
docker-compose exec app composer install

# Gerar chave da aplicação
echo "Gerando chave da aplicação..."
docker-compose exec app php artisan key:generate

# Executar migrações do banco de dados
echo "Executando migrações do banco de dados..."
docker-compose exec app php artisan migrate

# Limpar cache
echo "Limpando cache..."
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear

# Configurar permissões
echo "Configurando permissões..."
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache

echo "=== Instalação concluída! ==="
echo "O sistema está disponível em: http://localhost:8000"
echo "Para parar os containers: docker-compose down"
echo "Para iniciar os containers: docker-compose up -d"
