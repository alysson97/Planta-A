# SETUP — Como rodar o projeto

Existem **dois caminhos** para rodar a aplicação. Escolha **apenas um** e siga
todos os passos dele:

| Caminho | Escolha se… | Precisa ter instalado |
|---|---|---|
| **A. Docker** (recomendado) | Você quer subir tudo sem instalar PHP/MySQL na máquina | Docker + Docker Compose |
| **B. Local (nativo)** | Você já tem PHP 7.4 e MySQL 8 e prefere rodar direto na máquina | PHP 7.4, Composer 2, MySQL 8 |

> Os caminhos são **independentes**. Não misture os dois.
> Ao final de qualquer um, a aplicação responde em **http://localhost:8000**.

> A pasta `vendor/` e o arquivo `.env` **não** são versionados — por isso os
> dois caminhos incluem `composer install` e a criação do `.env`.

---

## A. Docker (recomendado)

**Pré-requisitos:** Docker e Docker Compose. *Não* é preciso ter PHP nem MySQL
na máquina.

Rode os comandos a partir da **raiz do projeto** (onde está o
`docker-compose.yml`):

```bash
# 1. Sobe o banco de dados (MySQL 8)
docker compose up -d db

# 2. Cria o .env já configurado para Docker (DB_HOST=db)
docker compose run --rm app cp .env.docker .env

# 3. Instala as dependências PHP
docker compose run --rm app composer install

# 4. Cria as tabelas e popula os dados de janeiro/2026
docker compose run --rm app php artisan migrate --seed

# 5. Sobe a aplicação
docker compose up -d app
```

Acesse **http://localhost:8000**.

**Detalhes:**
- O `.env.docker` já traz a `APP_KEY` e as credenciais do banco do
  `docker-compose.yml` (`DB_HOST=db`), então **não** é preciso rodar
  `php artisan key:generate`.
- Se o passo 4 falhar com erro de conexão, o MySQL ainda está inicializando:
  aguarde alguns segundos e repita o comando.
- Se você editar o `.env` depois de subir, reinicie o app para recarregá-lo:
  `docker compose restart app`.

**Para parar:** `docker compose down` (use `docker compose down -v` para apagar
também os dados do banco).

---

## B. Local (nativo)

**Pré-requisitos na máquina:**
- PHP 7.4 com as extensões `pdo_mysql`, `mbstring`, `bcmath` e `zip`
- Composer 2
- MySQL 8 em execução

Rode os comandos a partir da pasta **`src/`** (onde está o arquivo `artisan`):

```bash
cd src

# 1. Cria o banco de dados
mysql -u root -p -e "CREATE DATABASE planta_a;"

# 2. Cria o .env a partir do exemplo
cp .env.example .env

# 3. Instala as dependências PHP
composer install

# 4. Gera a APP_KEY (o .env.example vem sem chave)
php artisan key:generate

# 5. Cria as tabelas e popula os dados de janeiro/2026
php artisan migrate --seed

# 6. Sobe a aplicação
php artisan serve
```

Acesse **http://localhost:8000**.

**Antes do passo 5**, ajuste as credenciais do banco no `.env` para as da sua
máquina:

```env
DB_HOST=127.0.0.1
DB_DATABASE=planta_a
DB_USERNAME=root
DB_PASSWORD=sua_senha
```

**Diferenças em relação ao Docker:**
- Aqui o `DB_HOST` é **`127.0.0.1`** (e não `db`), porque o MySQL roda na
  própria máquina.
- O `.env.example` vem com a `APP_KEY` vazia, por isso o `key:generate` é
  necessário — no Docker não é.
