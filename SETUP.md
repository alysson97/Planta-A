---
 - Para quem roda nativo: cp .env.example .env (com DB_HOST=127.0.0.1), depois php artisan key:generate e php artisan migrate --seed.
---

---
 - Para quem roda Docker: cp .env.docker .env (com DB_HOST=db), depois docker compose up -d e docker compose exec app php artisan key:generate && php artisan migrate --seed.
---