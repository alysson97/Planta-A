<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Planta A — Eficiência de Produção</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800">
    <div class="mx-auto max-w-4xl px-4 py-10">
        <header class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900">Planta A — Eficiência de Produção</h1>
            <p class="text-sm text-slate-500">Janeiro/2026</p>
        </header>

        <form method="GET" class="mb-6">
            <label for="line" class="block text-sm font-medium text-slate-600 mb-1">Linha de produto</label>
            <select
                id="line"
                name="line"
                onchange="this.form.submit()"
                class="w-full sm:w-72 rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">Todas as linhas</option>
                @foreach ($lines as $line)
                    <option value="{{ $line }}" {{ $selectedLine === $line ? 'selected' : '' }}>{{ $line }}</option>
                @endforeach
            </select>
        </form>

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
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
                        @php
                            $efficiency = (float) $row->efficiency;
                            $badge = $efficiency >= 95 ? 'bg-emerald-100 text-emerald-800'
                                : ($efficiency >= 90 ? 'bg-amber-100 text-amber-800'
                                : 'bg-rose-100 text-rose-800');
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $row->line }}</td>
                            <td class="px-6 py-4 text-right text-sm text-slate-700">{{ number_format($row->produced, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-sm text-slate-700">{{ number_format($row->defects, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-sm">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $badge }}">
                                    {{ number_format($efficiency, 2, ',', '.') }}%
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
    </div>
</body>
</html>
