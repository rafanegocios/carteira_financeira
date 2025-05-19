#!/bin/bash

# Script para executar testes no sistema de Carteira Financeira

echo "=== Executando testes do sistema de Carteira Financeira ==="

# Verificar se os containers estão rodando
if ! docker-compose ps | grep -q "carteira_app"; then
    echo "ERRO: Os containers do Docker não estão rodando."
    echo "Por favor, execute 'docker-compose up -d' antes de executar os testes."
    exit 1
fi

# Limpar cache de testes anteriores
echo "Limpando cache..."
docker-compose exec app php artisan config:clear

# Executar migrações no banco de dados de teste
echo "Preparando banco de dados de teste..."
docker-compose exec app php artisan migrate:fresh --env=testing

# Executar testes unitários
echo "Executando testes unitários..."
docker-compose exec app php artisan test --testsuite=Unit

# Executar testes de integração
echo "Executando testes de integração..."
docker-compose exec app php artisan test --testsuite=Feature

echo "=== Testes concluídos! ==="
