# Social API

API built with Laravel 12 for posts, comments, user avatars, and reactions.

---

# PT-BR

## Stack

- PHP 8.4
- Laravel 12
- Sanctum
- Nginx
- MariaDB
- Docker Compose

## Subindo com Docker

### Requisitos

- Docker Desktop ou Docker Engine
- Docker Compose

### 1. Suba os containers

Na raiz do projeto:

```bash
docker compose up --build -d
```

Isso sobe:

- `app`: PHP-FPM com Laravel
- `nginx`: servidor HTTP exposto em `http://localhost:84`
- `db`: MariaDB exposto em `localhost:3316`

### 2. Rode as migrations

Depois que os containers estiverem prontos:

```bash
docker compose exec app php artisan migrate --force
```

### 3. Crie o link do storage

Necessario para servir avatars em `/storage/...`:

```bash
docker compose exec app php artisan storage:link
```

### 4. Acesse a aplicacao

- API: `http://localhost:84`
- Exemplo de health check Laravel: `http://localhost:84/up`

## Primeira subida

Se quiser garantir um ambiente limpo:

```bash
docker compose down -v
docker compose up --build -d
docker compose exec app php artisan migrate --force
docker compose exec app php artisan storage:link
```

## Comandos uteis

### Ver logs

```bash
docker compose logs -f
```

Ou apenas do app:

```bash
docker compose logs -f app
```

### Entrar no container PHP

```bash
docker compose exec app bash
```

### Rodar testes

```bash
docker compose exec app php artisan test
```

### Limpar cache de configuracao

```bash
docker compose exec app php artisan config:clear
```

## Rotas base

Algumas rotas principais:

- `POST /api/register`
- `POST /api/login`
- `GET /api/me`
- `GET /api/posts`
- `POST /api/posts`
- `GET /api/posts/{post}`
- `GET /api/posts/{post}/comments`
- `POST /api/posts/{post}/comments`

Para listar todas as rotas:

```bash
docker compose exec app php artisan route:list
```

## Banco de dados do Docker

Configuracao usada pelos containers:

- Host: `db`
- Port: `3306`
- Database: `testdb`
- User: `testroot`
- Password: `secret!132`

Para acessar pelo host:

- Host: `127.0.0.1`
- Port: `3316`
- Database: `testdb`
- User: `testroot`
- Password: `secret!132`

## Observacoes do ambiente atual

- O container `app` executa `composer install`, copia `.env.example` para `.env` e gera `APP_KEY` ao subir.
- Se voce alterar manualmente o `.env`, confirme se ele continua correto apos recriar o container.
- O projeto hoje foi validado para uso via Docker. O fluxo fora do container nao esta documentado aqui.

## Troubleshooting

### Avatar retorna 403 ou 404

Execute:

```bash
docker compose exec app php artisan storage:link
```

### Banco nao sobe corretamente

Refaca os volumes e suba novamente:

```bash
docker compose down -v
docker compose up --build -d
```

Depois rode:

```bash
docker compose exec app php artisan migrate --force
```

### Validar se a API esta respondendo

```bash
curl http://localhost:84/up
```

---

# EN

## Stack

- PHP 8.4
- Laravel 12
- Sanctum
- Nginx
- MariaDB
- Docker Compose

## Running with Docker

### Requirements

- Docker Desktop or Docker Engine
- Docker Compose

### 1. Start the containers

From the project root:

```bash
docker compose up --build -d
```

This starts:

- `app`: Laravel PHP-FPM container
- `nginx`: HTTP server exposed at `http://localhost:84`
- `db`: MariaDB exposed at `localhost:3316`

### 2. Run the migrations

After the containers are ready:

```bash
docker compose exec app php artisan migrate --force
```

### 3. Create the storage symlink

Required to serve avatars from `/storage/...`:

```bash
docker compose exec app php artisan storage:link
```

### 4. Access the application

- API: `http://localhost:84`
- Laravel health check example: `http://localhost:84/up`

## First-time setup

If you want a clean environment:

```bash
docker compose down -v
docker compose up --build -d
docker compose exec app php artisan migrate --force
docker compose exec app php artisan storage:link
```

## Useful commands

### View logs

```bash
docker compose logs -f
```

Or only the app logs:

```bash
docker compose logs -f app
```

### Open a shell in the PHP container

```bash
docker compose exec app bash
```

### Run tests

```bash
docker compose exec app php artisan test
```

### Clear config cache

```bash
docker compose exec app php artisan config:clear
```

## Main routes

Some important routes:

- `POST /api/register`
- `POST /api/login`
- `GET /api/me`
- `GET /api/posts`
- `POST /api/posts`
- `GET /api/posts/{post}`
- `GET /api/posts/{post}/comments`
- `POST /api/posts/{post}/comments`

To list all routes:

```bash
docker compose exec app php artisan route:list
```

## Docker database settings

Container connection settings:

- Host: `db`
- Port: `3306`
- Database: `testdb`
- User: `testroot`
- Password: `secret!132`

From the host machine:

- Host: `127.0.0.1`
- Port: `3316`
- Database: `testdb`
- User: `testroot`
- Password: `secret!132`

## Current environment notes

- The `app` container runs `composer install`, copies `.env.example` to `.env`, and generates `APP_KEY` on startup.
- If you manually change `.env`, make sure it still contains the correct values after recreating the container.
- The project is currently documented and validated for Docker usage. A non-Docker local setup is not documented here.

## Troubleshooting

### Avatar returns 403 or 404

Run:

```bash
docker compose exec app php artisan storage:link
```

### Database does not start correctly

Recreate the volumes and start again:

```bash
docker compose down -v
docker compose up --build -d
```

Then run:

```bash
docker compose exec app php artisan migrate --force
```

### Check if the API is responding

```bash
curl http://localhost:84/up
```
