FROM php:8.4-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip

# Limpar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Obter Composer mais recente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar diretório de trabalho
WORKDIR /var/www

# Copiar arquivos de configuração personalizados do PHP
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

# Copiar arquivos do projeto
COPY . /var/www

# Atribuir permissões para o diretório de armazenamento
RUN chown -R www-data:www-data /var/www

# Expor porta 9000 para conectar ao serviço web
EXPOSE 9000

# Iniciar servidor PHP-FPM
CMD ["php-fpm"]
