<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Istoric</h2>
                <div class="text-sm text-gray-500">Vezi toate testele și filtrează rapid.</div>
            </div>

            <a href="{{ route('tests.index') }}"
               class="px-4 py-2 rounded-lg bg-black text-white text-sm font-medium hover:bg-gray-900">
                Test nou
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Filtre --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <form method="GET" action="{{ route('tests.history') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="text-xs text-gray-500">Categorie</label>
                        <select name="category_id" class="mt-1 w-full rounded-lg border-gray-200 text-sm">
                            <option value="">Toate</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs text-gray-500">Status</label>
                        <select name="status" class="mt-1 w-full rounded-lg border-gray-200 text-sm">
                            <option value="all" @selected(($status ?? 'all') === 'all')>Toate</option>
                            <option value="finished" @selected(($status ?? 'all') === 'finished')>Finalizate</option>
                            <option value="active" @selected(($status ?? 'all') === 'active')>În lucru</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs text-gray-500">De la</label>
                        <input type="date" name="from" value="{{ request('from') }}"
                               class="mt-1 w-full rounded-lg border-gray-200 text-sm">
                    </div>

                    <div>
                        <label class="text-xs text-gray-500">Până la</label>
                        <input type="date" name="to" value="{{ request('to') }}"
                               class="mt-1 w-full rounded-lg border-gray-200 text-sm">
                    </div>

                    <div class="md:col-span-4 flex items-center gap-2 pt-2">
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-black">
                            Aplică filtre
                        </button>

                        <a href="{{ route('tests.history') }}"
                           class="px-4 py-2 rounded-lg bg-gray-100 text-gray-900 text-sm font-medium hover:bg-gray-200">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Listă --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="font-semibold text-gray-900">Teste</div>
                    <div class="text-sm text-gray-500">
                        {{ $attempts->total() }} rezultate
                    </div>
                </div>

                @if($attempts->count() === 0)
                    <div class="p-6 text-sm text-gray-500">
                        Nu există teste pentru filtrele selectate.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="text-left font-medium px-5 py-3">Dată</th>
                                    <th class="text-left font-medium px-5 py-3">Categorie</th>
                                    <th class="text-left font-medium px-5 py-3">Status</th>
                                    <th class="text-left font-medium px-5 py-3">Scor</th>
                                    <th class="text-right font-medium px-5 py-3">Acțiune</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($attempts as $a)
                                    @php
                                        $isFinished = !is_null($a->finished_at);
                                        $pct = null;

                                        if ($isFinished && (int)$a->total_questions > 0) {
                                            $pct = (int) round(($a->correct_count * 100) / (int)$a->total_questions);
                                        }
                                    @endphp

                                    <tr class="hover:bg-gray-50">
                                        <td class="px-5 py-4 text-gray-700 whitespace-nowrap">
                                            <div class="font-medium">
                                                {{ \Illuminate\Support\Carbon::parse($a->created_at)->format('d.m.Y') }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ \Illuminate\Support\Carbon::parse($a->created_at)->format('H:i') }}
                                            </div>
                                        </td>

                                        <td class="px-5 py-4 text-gray-900">
                                            {{ $a->category->name ?? '—' }}
                                        </td>

                                        <td class="px-5 py-4">
                                            @if($isFinished)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-100">
                                                    Finalizat
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                                    În lucru
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-5 py-4 text-gray-800 whitespace-nowrap">
                                            @if($pct === null)
                                                <span class="text-gray-400">—</span>
                                            @else
                                                <div class="font-semibold">{{ $pct }}%</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $a->correct_count }}/{{ $a->total_questions }}
                                                </div>
                                            @endif
                                        </td>

                                        <td class="px-5 py-4 text-right whitespace-nowrap">
                                            @if($isFinished)
                                                <a href="{{ route('tests.result', $a) }}"
                                                   class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-black">
                                                    Vezi rezultat
                                                </a>
                                            @else
                                                <a href="{{ route('tests.show', $a) }}"
                                                   class="inline-flex items-center px-4 py-2 rounded-lg bg-black text-white text-sm font-medium hover:bg-gray-900">
                                                    Continuă
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-5 py-4 border-t border-gray-100">
                        {{ $attempts->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
