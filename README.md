- Esse projeto foi feito com o auxílio de Docker para rodar a versão 7 do Laravel, então existe os arquivos de Docker .env.docker e um .env.example dentro de src exclusivos para rodar com Docker.

- Abaixo está o README do projeto gerado com IA e editado por mim:

---

# Planta A — Dashboard de Eficiência de Produção

Dashboard de eficiência de produção da **Planta A** referente a
**janeiro/2026**, com quatro linhas de produto (Geladeira, Máquina de Lavar,
TV e Ar-Condicionado). Para cada linha exibe a quantidade produzida, os
defeitos e a eficiência (%), permitindo visualizar todas as linhas ou
filtrar por uma específica.

## Stack

- Laravel 7 (PHP 7.4)
- MySQL 8
- Blade + Tailwind (via Play CDN, sem etapa de build)
- Docker (containers `app` e `db`)

## Como rodar

O passo a passo completo está em **[SETUP.md](SETUP.md)**, com dois caminhos
independentes e auto-suficientes — escolha o que se aplica a você:

- **Docker** (recomendado) — não exige PHP nem MySQL instalados na máquina.
- **Local (nativo)** — para quem já tem PHP 7.4 e MySQL 8.

Em ambos, a aplicação fica disponível em http://localhost:8000.

## Estrutura da tabela e dados de exemplo

> O enunciado do desafio pediu **explicitamente** a estrutura da tabela e um
> `INSERT` de dados de exemplo neste README. Em uso normal, o banco é populado
> pelo seeder (`php artisan migrate --seed`, que gera dados para todo o mês de
> janeiro/2026); os `INSERT`s abaixo são um recorte equivalente, apenas para
> simular o banco manualmente.

Estrutura da tabela `productions`:

```sql
CREATE TABLE `productions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `line` enum('Geladeira','Máquina de Lavar','TV','Ar-Condicionado') NOT NULL,
  `production_date` date NOT NULL,
  `produced` int unsigned NOT NULL,
  `defects` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `productions_production_date_line_index` (`production_date`, `line`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Dados de exemplo (três dias por linha, com volumes e taxas de defeito
próximos aos do seeder):

```sql
INSERT INTO `productions` (`line`, `production_date`, `produced`, `defects`) VALUES
('Geladeira',        '2026-01-02', 120, 4),
('Geladeira',        '2026-01-03', 135, 5),
('Geladeira',        '2026-01-04',  98, 3),
('Máquina de Lavar', '2026-01-02', 110, 6),
('Máquina de Lavar', '2026-01-03',  95, 4),
('Máquina de Lavar', '2026-01-04', 125, 8),
('TV',               '2026-01-02', 180, 14),
('TV',               '2026-01-03', 160, 12),
('TV',               '2026-01-04', 195, 18),
('Ar-Condicionado',  '2026-01-02',  85, 8),
('Ar-Condicionado',  '2026-01-03', 100, 10),
('Ar-Condicionado',  '2026-01-04',  72, 7);
```

## Decisões de arquitetura

- **Thin Controller / Heavy Service:** toda a lógica de agregação, filtro e
  cálculo de eficiência fica em `app/Services/ProductionDashboardService.php`,
  injetado no controller via type hinting no construtor.
- **Cálculo no banco:** a eficiência agregada é calculada em SQL sobre os
  totais somados — `(SUM(produzida) - SUM(defeitos)) / SUM(produzida) * 100` —
  nunca como média de percentuais. Um `CASE WHEN SUM(produzida) = 0` evita
  divisão por zero diretamente na query.
- **Índice composto** `(production_date, line)`: cobre o recorte por data e o
  `GROUP BY line` numa única estrutura. O filtro de data usa comparação por
  intervalo (sargável) para aproveitar esse índice.
- **ENUM na coluna `line`:** as quatro linhas são fixas pelo escopo, sem
  cadastro dinâmico. O ENUM funciona como constraint de integridade no próprio
  banco e evita um JOIN apenas para exibir um rótulo. Trade-off assumido: uma
  eventual 5ª linha exigiria uma migration.

## Decisões de interpretação do enunciado

Dois pontos do enunciado foram interpretados de forma explícita. Registro
ambos aqui para transparência na avaliação:

1. **Fórmula da eficiência.** O enunciado menciona "produzida/defeitos".
   Adotei a definição industrial de *first pass yield* —
   `((produzida - defeitos) / produzida) * 100` — por ser a métrica que
   representa eficiência de produção de fato (percentual de itens sem defeito).
   A razão literal "produzida/defeitos" foi entendida como imprecisão do texto.

2. **Período fixo (janeiro/2026).** O escopo define um dashboard de um único
   mês. Por isso o recorte temporal de **janeiro/2026** é uma constante do
   serviço (`PERIOD_START` / `PERIOD_END`), e não um filtro de data exposto na
   interface — não há seleção de período na tela porque não há outro período
   no escopo. Caso o dashboard precise cobrir múltiplos meses no futuro, basta
   promover essas constantes a um parâmetro do serviço.
