<?php

use App\Production;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * Perfil de cada linha: faixa diaria de unidades produzidas e faixa de
     * taxa de defeito (%). As faixas variam por linha de proposito, para que
     * o dashboard apresente eficiencias distintas e proximas de um cenario
     * real de fabrica.
     */
    private const LINE_PROFILES = [
        'Geladeira'        => ['produced' => [90, 140],  'defect_rate' => [2, 5]],
        'Máquina de Lavar' => ['produced' => [80, 130],  'defect_rate' => [3, 7]],
        'TV'               => ['produced' => [120, 200], 'defect_rate' => [5, 10]],
        'Ar-Condicionado'  => ['produced' => [60, 110],  'defect_rate' => [6, 12]],
    ];

    /**
     * Gera um registro diario por linha ao longo de janeiro/2026.
     */
    public function run()
    {
        $timestamp = now();
        $rows = [];

        foreach (CarbonPeriod::create('2026-01-01', '2026-01-31') as $day) {
            foreach (self::LINE_PROFILES as $line => $profile) {
                $produced = random_int(...$profile['produced']);
                $defects = (int) round($produced * random_int(...$profile['defect_rate']) / 100);

                $rows[] = [
                    'line' => $line,
                    'production_date' => $day->toDateString(),
                    'produced' => $produced,
                    'defects' => $defects,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }
        }

        Production::insert($rows);
    }
}
