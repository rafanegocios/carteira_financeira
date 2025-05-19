💰 Sistema de Carteira Financeira
<p align="center"> <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"> <img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"> <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL"> <img src="https://img.shields.io/badge/Docker-20.10+-2496ED?style=for-the-badge&logo=docker&logoColor=white" alt="Docker"> <img src="https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge" alt="License"> </p> <p align="center"> Sistema completo de carteira financeira desenvolvido em Laravel para o desafio técnico do Grupo Adriano Cobuccio. Permite o gerenciamento de depósitos, transferências e reversões de transações com segurança e integridade de dados. </p>
🚀 Funcionalidades
✅ Autenticação Completa: Registro e login de usuários
✅ Depósitos: Adicione fundos à sua carteira
✅ Transferências: Envie dinheiro para outros usuários
✅ Validação de Saldo: Verificação automática antes de transferências
✅ Reversão de Operações: Possibilidade de reverter qualquer transação
✅ Histórico Completo: Visualização detalhada de todas as transações
✅ Segurança: Proteção CSRF, validação de dados e transações SQL
✅ Observabilidade: Logs estruturados de transações e atividades
🛠️ Tecnologias
Backend: PHP 8.2+, Laravel 10.x
Banco de Dados: MySQL 8.0 / SQLite
Frontend: Blade Templates, Tailwind CSS, Alpine.js
Infraestrutura: Docker, Docker Compose, Nginx
Testes: PHPUnit (Unitários e Integração)
Arquitetura: MVC, Repository Pattern, Service Layer
📋 Pré-requisitos
🐳 Docker e Docker Compose
🔧 Git
OU

🐘 PHP 8.2+
🎼 Composer
🗄️ MySQL/SQLite
🚀 Instalação
Método 1: Docker (Recomendado)
bash
# 1. Clone o repositório
git clone https://github.com/seu-usuario/carteira-financeira.git
cd carteira-financeira

# 2. Configure o ambiente
cp .env.example .env

# 3. Inicie os containers
docker-compose up -d

# 4. Instale as dependências
docker-compose exec app composer install

# 5. Gere a chave da aplicação
docker-compose exec app php artisan key:generate

# 6. Execute as migrações
docker-compose exec app php artisan migrate

# 7. Crie usuários de teste (opcional)
docker-compose exec app php artisan db:seed --class=UserSeeder

# 8. Acesse a aplicação
# http://localhost:8000
Método 2: Instalação Local
bash
# 1. Clone e configure
git clone https://github.com/seu-usuario/carteira-financeira.git
cd carteira-financeira
cp .env.example .env

# 2. Instale dependências
composer install

# 3. Configure o banco no .env
# Para SQLite (mais simples):
DB_CONNECTION=sqlite

# Para MySQL:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=carteira_financeira
DB_USERNAME=root
DB_PASSWORD=

# 4. Gere a chave e execute migrações
php artisan key:generate
php artisan migrate

# 5. Inicie o servidor
php artisan serve
🐳 Comandos Docker Úteis
bash
# Ver logs dos containers
docker-compose logs -f

# Executar comandos Artisan
docker-compose exec app php artisan [comando]

# Acessar o container
docker-compose exec app bash

# Parar containers
docker-compose down

# Rebuild completo
docker-compose down
docker-compose build --no-cache
docker-compose up -d
🧪 Testes
bash
# Executar todos os testes
docker-compose exec app php artisan test

# Testes específicos
docker-compose exec app php artisan test --testsuite=Unit
docker-compose exec app php artisan test --testsuite=Feature

# Com coverage (se configurado)
docker-compose exec app php artisan test --coverage
📊 Estrutura do Projeto
carteira-financeira/
├── app/
│   ├── Http/Controllers/    # Controllers da aplicação
│   ├── Models/             # Modelos Eloquent
│   ├── Services/           # Lógica de negócios
│   └── Observers/          # Observadores de eventos
├── database/
│   ├── migrations/         # Migrações do banco
│   └── seeders/           # Seeds para dados de teste
├── resources/views/        # Templates Blade
├── routes/                 # Definição de rotas
├── tests/                  # Testes unitários e integração
└── docker/                 # Configurações Docker
📱 Como Usar
1. Registro/Login
Acesse /register para criar uma conta
Use /login para entrar no sistema
2. Dashboard
Visualize seu saldo atual
Acesse transações recentes
Navegue para depósitos e transferências
3. Depósitos
Adicione fundos à sua carteira
Valores são adicionados imediatamente ao saldo
4. Transferências
Envie dinheiro usando o email do destinatário
Sistema valida saldo e existência do usuário
Ambos os usuários têm histórico atualizado
5. Reversões
Acesse detalhes de qualquer transação
Reverta operações quando necessário
Saldos são restaurados automaticamente
👥 Usuários de Teste
Se executou o seeder, use estes usuários para teste:

Email	Senha	Saldo Inicial
admin@teste.com	123456	R$ 1.000,00
rafael@teste.com	123456	R$ 500,00
maria@teste.com	123456	R$ 300,00
joao@teste.com	123456	R$ 800,00
🏗️ Arquitetura
Padrões Utilizados
MVC: Separação clara entre Model, View e Controller
Service Layer: Lógica de negócios encapsulada em serviços
Repository Pattern: Abstração da camada de dados
Observer Pattern: Monitoramento de eventos de transações
Princípios SOLID
Single Responsibility: Cada classe tem uma responsabilidade
Open/Closed: Extensível sem modificar código existente
Liskov Substitution: Substituição de implementações
Interface Segregation: Interfaces específicas por necessidade
Dependency Inversion: Dependências via injeção
🔒 Segurança
✅ Validação de entrada de dados
✅ Proteção contra CSRF
✅ Hash de senhas com bcrypt
✅ Sanitização de saídas (XSS)
✅ Transações SQL para integridade
✅ Autorização de ações por usuário
📈 Observabilidade
Logs Disponíveis
storage/logs/transactions.log - Transações do sistema
storage/logs/user_activity.log - Atividades dos usuários
storage/logs/laravel.log - Logs gerais da aplicação
Monitoramento
Logs estruturados em JSON
Rastreamento de transações
Métricas de performance
Auditoria de operações
🤝 Contribuição
Faça um fork do projeto
Crie uma branch para sua feature (git checkout -b feature/AmazingFeature)
Commit suas mudanças (git commit -m 'Add some AmazingFeature')
Push para a branch (git push origin feature/AmazingFeature)
Abra um Pull Request
Padrões de Commit
feat: nova funcionalidade
fix: correção de bug
docs: atualizações na documentação
style: formatação, ponto e vírgula, etc
refactor: refatoração de código
test: adição ou correção de testes
chore: tarefas de manutenção
🐛 Solução de Problemas
Erro de Permissões
bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
Erro de Autoload
bash
docker-compose exec app composer dump-autoload
Limpar Cache
bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear
📄 Licença
Este projeto está licenciado sob a MIT License - veja o arquivo LICENSE para detalhes.

👨‍💻 Autor
Seu Nome

GitHub: @seu-usuario
LinkedIn: Seu Perfil
Email: seu.email@exemplo.com
🙏 Agradecimentos
Laravel - Framework PHP
Tailwind CSS - Framework CSS
Alpine.js - Framework JavaScript
Docker - Containerização
<p align="center"> Desenvolvido com ❤️ para o desafio técnico do Grupo Adriano Cobuccio </p>
🔗 Links Úteis
Documentação Laravel
Documentação Docker
PHP The Right Way
Tailwind CSS Docs
📊 Status do Projeto
Funcionalidade	Status
Autenticação	✅ Completo
Depósitos	✅ Completo
Transferências	✅ Completo
Reversões	✅ Completo
Testes	✅ Completo
Docker	✅ Completo
Documentação	✅ Completo
Logs	✅ Completo
Nota: Este projeto foi desenvolvido como parte de um desafio técnico, implementando todas as funcionalidades solicitadas com foco em qualidade, segurança e boas práticas de desenvolvimento.

