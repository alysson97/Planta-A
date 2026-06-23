<?php

namespace App\Services;

use App\Production;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ProductionDashboardService
{
    /**
     * Janeiro/2026 e o unico periodo coberto pelo dashboard.
     * Usamos limites de data em vez de whereYear/whereMonth de proposito: o
     * range e sargavel e aproveita o indice (production_date, line); funcoes
     * sobre a coluna invalidariam esse indice.
     */
    private const PERIOD_START = '2026-01-01';
    private const PERIOD_END = '2026-01-31';

    /**
     * Eficiencia (first pass yield) sobre os totais somados, calculada no
     * banco e protegida contra divisao por zero / conjunto vazio. Compartilhada
     * pelo resumo por linha e pelos totais para manter uma unica fonte da
     * formula.
     */
    private const EFFICIENCY_SQL =
        'ROUND(CASE WHEN COALESCE(SUM(produced), 0) = 0 THEN 0'
        . ' ELSE (SUM(produced) - SUM(defects)) / SUM(produced) * 100 END, 2)';

    /**
     * Agrega producao por linha no periodo (ranking por eficiencia),
     * opcionalmente filtrando uma linha.
     */
    public function summaryByLine(?string $line = null): Collection
    {
        return $this->baseQuery($line)
            ->select('line')
            ->selectRaw('SUM(produced) AS produced')
            ->selectRaw('SUM(defects) AS defects')
            ->selectRaw(self::EFFICIENCY_SQL . ' AS efficiency')
            ->groupBy('line')
            ->orderByDesc('efficiency')
            ->get();
    }

    /**
     * Totais consolidados do periodo (para os indicadores do topo), tambem
     * calculados no banco e respeitando o filtro de linha.
     */
    public function totals(?string $line = null): object
    {
        return $this->baseQuery($line)
            ->selectRaw('COALESCE(SUM(produced), 0) AS produced')
            ->selectRaw('COALESCE(SUM(defects), 0) AS defects')
            ->selectRaw(self::EFFICIENCY_SQL . ' AS efficiency')
            ->first();
    }

    /**
     * Recorte base (periodo + filtro de linha) reutilizado pelas consultas.
     */
    private function baseQuery(?string $line): Builder
    {
        return Production::query()
            ->whereBetween('production_date', [self::PERIOD_START, self::PERIOD_END])
            ->when($line, fn (Builder $query) => $query->where('line', $line));
    }
}
