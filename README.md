# Projeto: projeto-php-backend

Documentação do backend do projeto — API e serviços desenvolvidos em Laravel.

**Stack principal**: PHP ^8.2, Laravel ^12, Sanctum (autenticação), Stripe (pagamentos).

## Visão geral

Este repositório contém o backend da aplicação — APIs REST, modelos Eloquent, serviços (ex.: integração com LastFM e Stripe) e comandos Artisan usados pelo projeto.

## Requisitos

- PHP 8.2+
- Composer
- Node.js + npm (para assets quando aplicável)
- Banco de dados (MySQL, MariaDB ou SQLite para desenvolvimento)

As dependências principais estão em `composer.json` (Laravel ^12, Sanctum, Stripe).

## Instalação rápida

1. Clone o repositório:

```bash
git clone <repo-url> projeto-php-backend
cd projeto-php-backend
```

2. Instale dependências PHP:

```bash
composer install
```

3. Copie o arquivo de ambiente e gere a chave:

```bash
cp .env.example .env
php artisan key:generate
```

4. Configure variáveis de ambiente no `.env` (exemplos importantes):

- `APP_ENV`, `APP_DEBUG`, `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `STRIPE_KEY`, `STRIPE_SECRET` (para pagamentos)

5. Execute migrações e seeders (se existirem):

```bash
php artisan migrate
php artisan db:seed
```

6. Instale dependências JS e construa assets (quando necessário):

```bash
npm install
npm run dev   # ou npm run build para produção
```

7. Inicie o servidor local:

```bash
php artisan serve
```

## Comandos úteis

- `composer install` — instala dependências PHP
- `php artisan migrate` — aplica migrações
- `php artisan migrate:fresh --seed` — recria banco e popula
- `php artisan tinker` — shell interativo
- `php artisan test` — executa testes (PHPUnit)

## Estrutura principal do projeto

- `app/` — controladores, modelos, serviços e traits
- `routes/` — rotas da aplicação (`api.php`, `web.php`)
- `database/` — migrations, seeders e factories
- `tests/` — testes automatizados
- `config/` — configurações do Laravel e dos pacotes

## Rotas e API

As rotas API ficam em `routes/api.php`. Documente endpoints principais aqui quando for necessário (ex.: autenticação, CRUD de playlists, assinaturas, pagamentos).

Exemplo de uso (Autenticação via Sanctum):

- Registrar: `POST /api/register`
- Login: `POST /api/login` (retorna token Sanctum)
- Rotas autênticadas: enviar header `Authorization: Bearer <token>`

Verifique `app/Http/Controllers` para exemplos de controladores já implementados.

## Serviços integrados

- `app/Services/LastFmService.php` — integração com LastFM para buscas/metadata
- `app/Services/ResponseService.php` — helpers para formatação de respostas
- Integração com Stripe via `stripe/stripe-php` em `composer.json` para processar pagamentos e assinaturas

## Testes

Execute a suíte de testes com:

```bash
php artisan test
```

Os testes estão em `tests/`. Use factories em `database/factories` para criar dados de teste.

## Boas práticas e convenções

- Use migrations para alterar esquema do banco
- Valide requests com `FormRequest` em `app/Http/Requests`
- Centralize lógica externa em `app/Services`

## Deploy

Etapas gerais para produção:

1. Ajustar variáveis em `.env` (BD, Stripe, APP_ENV=production)
2. `composer install --optimize-autoloader --no-dev`
3. `php artisan migrate --force`
4. `php artisan config:cache` e `php artisan route:cache`
5. Construir assets (`npm run build`)

## Contribuição

1. Abra uma issue descrevendo a proposta ou bug
2. Crie uma branch com nome descritivo
3. Envie um pull request com descrição clara das mudanças

## Contato

Para dúvidas relacionadas ao projeto, contacte o mantenedor ou crie uma issue no repositório.

## Licença

Projeto licenciado sob MIT (ver `LICENSE` quando aplicável).

