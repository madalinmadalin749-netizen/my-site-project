<x-app-layout>
<style>
  .score-bar {
    display: block !important;
    height: 8px !important;
    width: 100% !important;
    background: #f3f4f6 !important;
    border-radius: 9999px !important;
    overflow: hidden !important;
    margin-top: 8px !important;
  }
  .score-bar > span {
    display: block !important;
    height: 8px !important;
    background: #f59e0b !important;
    width: 0%;
    border-radius: 9999px !important;
  }
</style>

    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="text-xl font-semibold text-gray-800">Dashboard</div>

            <div class="flex items-center gap-2">
                @if($activeAttempt)
                    <a href="{{ route('tests.show', $activeAttempt) }}"
                       class="px-4 py-2 rounded-lg bg-orange-500 text-white text-sm font-medium hover:bg-orange-600">
                        Continuă testul
                    </a>
                @endif

                <a href="{{ route('tests.index') }}"
                   class="px-4 py-2 rounded-lg bg-black text-white text-sm font-medium hover:bg-gray-900">
                    Test nou
                </a>

                @if(\Illuminate\Support\Facades\Route::has('tests.history'))
                    <a href="{{ route('tests.history') }}"
                       class="px-4 py-2 rounded-lg bg-gray-100 text-gray-900 text-sm font-medium hover:bg-gray-200">
                        Istoric
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Stats row (ca în screenshot) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <div class="text-sm text-gray-500">Teste totale</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">{{ $totalAttempts ?? 0 }}</div>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <div class="text-sm text-gray-500">Teste finalizate</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">{{ $finishedAttempts ?? 0 }}</div>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <div class="text-sm text-gray-500">Cel mai bun scor</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">{{ (int) round($bestPercent ?? 0) }}%</div>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <div class="text-sm text-gray-500">Media scorurilor</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">{{ (int) round($avgPercent ?? 0) }}%</div>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <div class="text-sm text-gray-500">Streak (≥ 70%)</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">{{ $streak ?? 0 }}</div>
                </div>
            </div>

            {{-- Banner test în desfășurare (cu buton) --}}
            @if($activeAttempt)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 flex items-center justify-between">
                    <div>
                        <div class="font-semibold text-amber-900">Ai un test în desfășurare</div>
                        <div class="text-sm text-amber-800">
                            Categoria: {{ $activeAttempt->category->name ?? '—' }}
                        </div>
                    </div>

                    <a href="{{ route('tests.show', $activeAttempt) }}"
                       class="px-5 py-2 rounded-lg bg-orange-500 text-white font-medium hover:bg-orange-600">
                        Continuă
                    </a>
                </div>
            @endif

            {{-- LAYOUTUL CORECT: stânga (2 carduri stacked), dreapta (1 card) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- STÂNGA: ocupă 2 coloane pe desktop --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Evoluție scor (cu bara de progres pe fiecare rând) --}}
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-lg font-semibold text-gray-900">Evoluție scor</div>
                            <div class="text-sm text-gray-500">Ultimele 10</div>
                        </div>

                        @forelse($recentFinished as $a)
                            @php
                                $total = max(1, (int) $a->total_questions);
                                $pct = (int) round(($a->correct_count * 100) / $total);
                            @endphp

                            <div class="py-3 border-t border-gray-100 first:border-t-0">
                                <div class="flex items-center justify-between text-sm">
                                    <div class="font-medium text-gray-900">
                                        {{ $a->category->name ?? 'Categorie' }}
                                        <span class="text-gray-400 font-normal">• {{ \Illuminate\Support\Carbon::parse($a->finished_at)->format('d.m.Y H:i') }}</span>
                                    </div>
                                    <div class="font-semibold text-orange-600">{{ $pct }}%</div>
                                </div>

                                {{-- BARA de progres (asta ziceai că nu o mai ai) --}}
                                <div class="score-bar">
    <span style="width: {{ (int)$pct }}%"></span>
</div>


                                <div class="mt-2 text-xs text-gray-500">
                                    {{ $a->correct_count }}/{{ $a->total_questions }}
                                    <a class="float-right text-gray-500 hover:text-gray-900"
                                       href="{{ route('tests.result', $a) }}">vezi →</a>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500">Nu ai teste finalizate încă.</div>
                        @endforelse
                    </div>

                    {{-- Ultimele teste (sub Evoluție, tot în stânga) --}}
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-lg font-semibold text-gray-900">Ultimele teste</div>

                            @if(\Illuminate\Support\Facades\Route::has('tests.history'))
                                <a class="text-sm text-gray-500 hover:text-gray-900" href="{{ route('tests.history') }}">
                                    toate →
                                </a>
                            @endif
                        </div>

                        <div class="space-y-3">
                            @forelse($recentAttempts as $a)
                                @php
                                    $pct = null;
                                    if ($a->finished_at && (int)$a->total_questions > 0) {
                                        $pct = (int) round(($a->correct_count * 100) / (int)$a->total_questions);
                                    }
                                @endphp

                                <div class="rounded-xl border border-gray-100 p-4 flex items-center justify-between">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $a->category->name ?? 'Categorie' }}</div>
                                        <div class="text-xs text-gray-500">{{ \Illuminate\Support\Carbon::parse($a->created_at)->format('d.m.Y H:i') }}</div>
                                        <div class="mt-1 text-sm text-gray-700">
                                            @if($pct === null)
                                                {{ $a->finished_at ? '—' : 'În lucru' }}
                                            @else
                                                {{ $a->correct_count }}/{{ $a->total_questions }} • {{ $pct }}%
                                            @endif
                                        </div>
                                    </div>

                                    <a href="{{ $a->finished_at ? route('tests.result', $a) : route('tests.show', $a) }}"
                                       class="px-4 py-2 rounded-lg bg-black text-white text-sm font-medium hover:bg-gray-900">
                                        {{ $a->finished_at ? 'Vezi rezultat' : 'Continuă' }}
                                    </a>
                                </div>
                            @empty
                                <div class="text-sm text-gray-500">Nu ai încercări încă.</div>
                            @endforelse
                        </div>
                    </div>

                </div>

                {{-- DREAPTA: Progres pe categorii --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-lg font-semibold text-gray-900">Progres pe categorii</div>
                            <div class="text-sm text-gray-500">Ultimul scor</div>
                        </div>

                        <div class="space-y-4">
                            @foreach($categoryProgress as $row)
                                @php
                                    $c = $row->category;
                                    $pct = $row->last_percent;   // null sau 0..100
                                    $last = $row->last_attempt;
                                @endphp

                                <div class="rounded-xl border border-gray-100 p-4">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $c->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $c->questions_count }} întrebări</div>
                                        </div>
                                        <div class="font-semibold text-gray-700">
                                            {{ is_null($pct) ? '—' : ($pct.'%') }}
                                        </div>
                                    </div>

                                    <div style="margin-top:8px;height:8px;background:#f3f4f6;border-radius:9999px;overflow:hidden;">
    <div style="height:8px;background:#f59e0b;width: {{ (int)$pct }}%;border-radius:9999px;"></div>
</div>

                                    <div class="mt-3 flex items-center gap-2">
                                        <form method="POST" action="{{ route('tests.start') }}">
                                            @csrf
                                            <input type="hidden" name="category_id" value="{{ $c->id }}">
                                            <input type="hidden" name="count" value="20">
                                            <button type="submit"
                                                    class="px-4 py-2 rounded-lg bg-black text-white text-sm font-medium hover:bg-gray-900">
                                                Start (20)
                                            </button>
                                        </form>

                                        @if($last)
                                            <a href="{{ route('tests.result', $last) }}"
                                               class="px-4 py-2 rounded-lg bg-gray-100 text-gray-900 text-sm font-medium hover:bg-gray-200">
                                                Ultimul rezultat
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
