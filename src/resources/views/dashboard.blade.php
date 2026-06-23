<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Planta A — Eficiência de Produção</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
@php
    // Banda de cor por faixa de eficiencia (first pass yield), reutilizada nos
    // indicadores, badges e barras. Tailwind Play CDN gera as classes a partir
    // do HTML ja renderizado, entao compor o nome da cor aqui funciona.
    $band = fn (float $efficiency) => $efficiency >= 95 ? 'emerald' : ($efficiency >= 90 ? 'amber' : 'rose');
    $br = fn ($value, $decimals = 0) => number_format((float) $value, $decimals, ',', '.');

    $generalEfficiency = (float) $totals->efficiency;
    $defectRate = $totals->produced > 0 ? $totals->defects / $totals->produced * 100 : 0.0;
    $attention = $summary->last(); // resumo vem ordenado por eficiencia desc
@endphp

    <div class="mx-auto max-w-6xl px-4 py-10 space-y-8">

        <header>
            <h1 class="text-2xl font-bold text-slate-900">Dashboard de Eficiência</h1>
            <p class="text-sm text-slate-500">
                Produção da Planta A · Janeiro/2026{{ $selectedLine ? ' · ' . $selectedLine : '' }}
            </p>
        </header>

        {{-- Indicadores --}}
        <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-slate-500">Produção total</span>
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                </div>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $br($totals->produced) }}</p>
                <p class="mt-1 text-xs text-slate-400">unidades produzidas no período</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-slate-500">Defeitos totais</span>
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                </div>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $br($totals->defects) }}</p>
                <p class="mt-1 text-xs text-slate-400">{{ $br($defectRate, 2) }}% da produção</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-slate-500">Eficiência geral</span>
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                </div>
                <p class="mt-2 text-3xl font-bold text-{{ $band($generalEfficiency) }}-600">{{ $br($generalEfficiency, 2) }}%</p>
                <p class="mt-1 text-xs text-slate-400">first pass yield</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-slate-500">Linha em atenção</span>
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                </div>
                @if ($attention)
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $attention->line }}</p>
                    <p class="mt-1">
                        <span class="inline-flex rounded-full bg-{{ $band((float) $attention->efficiency) }}-100 px-2.5 py-0.5 text-xs font-semibold text-{{ $band((float) $attention->efficiency) }}-700">
                            {{ $br($attention->efficiency, 2) }}% de eficiência
                        </span>
                    </p>
                @else
                    <p class="mt-2 text-2xl font-bold text-slate-400">—</p>
                    <p class="mt-1 text-xs text-slate-400">sem dados no período</p>
                @endif
            </div>
        </section>

        {{-- Filtro de busca (combobox) --}}
        <div class="flex justify-end">
            <form method="GET" id="lineFilter" class="relative w-full sm:w-80">
                <input type="hidden" name="line" id="lineValue" value="{{ $selectedLine }}">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input
                    type="text"
                    id="lineSearch"
                    autocomplete="off"
                    placeholder="Pesquisar linha de produto..."
                    value="{{ $selectedLine }}"
                    class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-4 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                >
                <ul id="lineOptions" class="absolute z-10 mt-1 hidden max-h-60 w-full overflow-auto rounded-lg border border-slate-200 bg-white py-1 text-sm shadow-lg">
                    <li data-value="" class="cursor-pointer px-4 py-2 text-slate-500 hover:bg-slate-50">Todas as linhas</li>
                    @foreach ($lines as $line)
                        <li data-value="{{ $line }}" class="cursor-pointer px-4 py-2 text-slate-700 hover:bg-slate-50">{{ $line }}</li>
                    @endforeach
                </ul>
            </form>
        </div>

        {{-- Painéis --}}
        <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            {{-- Eficiência por linha (barras) --}}
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-slate-900">Eficiência por linha</h2>
                <p class="mb-5 text-sm text-slate-500">Percentual de itens sem defeito (first pass yield)</p>
                <div class="space-y-4">
                    @forelse ($summary as $row)
                        @php $efficiency = (float) $row->efficiency; @endphp
                        <div>
                            <div class="mb-1 flex items-baseline justify-between text-sm">
                                <span class="font-medium text-slate-700">{{ $row->line }}</span>
                                <span class="font-semibold text-{{ $band($efficiency) }}-600">{{ $br($efficiency, 2) }}%</span>
                            </div>
                            <div class="h-2.5 w-full rounded-full bg-slate-100">
                                <div class="h-2.5 rounded-full bg-{{ $band($efficiency) }}-500" style="width: {{ $efficiency }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Nenhum dado de produção para o período.</p>
                    @endforelse
                </div>
            </div>

            {{-- Detalhamento (tabela) --}}
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-6 py-5">
                    <h2 class="text-base font-semibold text-slate-900">Detalhamento</h2>
                    <p class="text-sm text-slate-500">Produção e defeitos por linha</p>
                </div>
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Linha</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Produzida</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Defeitos</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Eficiência</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($summary as $row)
                            @php $efficiency = (float) $row->efficiency; @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $row->line }}</td>
                                <td class="px-6 py-4 text-right text-sm text-slate-700">{{ $br($row->produced) }}</td>
                                <td class="px-6 py-4 text-right text-sm text-slate-700">{{ $br($row->defects) }}</td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <span class="inline-flex rounded-full bg-{{ $band($efficiency) }}-100 px-2.5 py-0.5 text-xs font-semibold text-{{ $band($efficiency) }}-700">
                                        {{ $br($efficiency, 2) }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">
                                    Nenhum dado de produção encontrado para o período.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <script>
        // Combobox de busca: filtra as linhas conforme o usuario digita e, ao
        // escolher uma opcao, submete o filtro (server-side continua sendo a
        // fonte de verdade da agregacao).
        (function () {
            const form = document.getElementById('lineFilter');
            const search = document.getElementById('lineSearch');
            const value = document.getElementById('lineValue');
            const list = document.getElementById('lineOptions');
            const options = Array.from(list.querySelectorAll('li'));

            const open = () => list.classList.remove('hidden');
            const close = () => list.classList.add('hidden');

            const select = (option) => {
                value.value = option.dataset.value;
                form.submit();
            };

            search.addEventListener('focus', open);
            search.addEventListener('input', () => {
                const query = search.value.toLowerCase();
                options.forEach((option) => {
                    const match = option.textContent.toLowerCase().includes(query);
                    option.classList.toggle('hidden', !match);
                });
                open();
            });
            search.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    const first = options.find((option) => !option.classList.contains('hidden'));
                    if (first) select(first);
                }
            });

            options.forEach((option) => option.addEventListener('click', () => select(option)));

            document.addEventListener('click', (event) => {
                if (!event.target.closest('#lineFilter')) close();
            });
        })();
    </script>
</body>
</html>
