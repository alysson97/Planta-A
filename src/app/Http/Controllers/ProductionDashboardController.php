<?php

namespace App\Http\Controllers;

use App\Production;
use App\Services\ProductionDashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductionDashboardController extends Controller
{
    private ProductionDashboardService $dashboard;

    public function __construct(ProductionDashboardService $dashboard)
    {
        $this->dashboard = $dashboard;
    }

    public function index(Request $request): View
    {
        $line = $request->query('line');

        // Filtro invalido cai para "todas as linhas" em vez de quebrar a tela.
        if (! in_array($line, Production::LINES, true)) {
            $line = null;
        }

        return view('dashboard', [
            'lines' => Production::LINES,
            'selectedLine' => $line,
            'summary' => $this->dashboard->summaryByLine($line),
        ]);
    }
}
