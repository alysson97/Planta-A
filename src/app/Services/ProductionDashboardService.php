<?php

namespace App\Services;

use App\Production;
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
     * Agrega producao por linha no periodo, opcionalmente filtrando uma linha.
     *
     * Eficiencia e agregacao sao calculadas no banco sobre os totais somados
     * — (SUM(produced) - SUM(defects)) / SUM(produced) * 100 — nunca como
     * media de percentuais. O CASE WHEN protege contra divisao por zero.
     */
    public function summaryByLine(?string $line = null): Collection
    {
        return Production::query()
            ->select('line')
            ->selectRaw('SUM(produced) AS produced')
            ->selectRaw('SUM(defects) AS defects')
            ->selectRaw(
                'ROUND(CASE WHEN SUM(produced) = 0 THEN 0'
                . ' ELSE (SUM(produced) - SUM(defects)) / SUM(produced) * 100 END, 2)'
                . ' AS efficiency'
            )
            ->whereBetween('production_date', [self::PERIOD_START, self::PERIOD_END])
            ->when($line, fn ($query) => $query->where('line', $line))
            ->groupBy('line')
            ->orderBy('line')
            ->get();
    }
}
