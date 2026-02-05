<x-app-layout>
    <div class="min-h-[calc(100vh-4rem)] bg-gradient-to-b from-slate-950 via-slate-950 to-slate-900">
        {{-- background glow --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -top-32 left-1/2 h-[520px] w-[520px] -translate-x-1/2 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div class="absolute top-40 right-[-120px] h-[420px] w-[420px] rounded-full bg-cyan-400/10 blur-3xl"></div>
            <div class="absolute bottom-[-160px] left-[-140px] h-[520px] w-[520px] rounded-full bg-fuchsia-500/10 blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-white">
                        Dashboard
                    </h1>
                    <p class="mt-1 text-sm text-slate-300">
                        Progresul tău, statistici și acces rapid la teste.
                    </p>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('tests.index') }}"
                       class="inline-flex items-center justify-center rounded-xl bg-white/10 px-4 py-2 text-sm font-medium text-white ring-1 ring-white/10 backdrop-blur hover:bg-white/15 transition">
                        Test nou
                    </a>
                    <a href="{{ route('history.index') }}"
                       class="inline-flex items-center justify-center rounded-xl bg-indigo-500/90 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 transition shadow-lg shadow-indigo-500/20">
                        Istoric
                    </a>
                </div>
            </div>

            {{-- KPI cards --}}
            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @php
                    $cards = [
                        ['label' => 'Teste totale', 'value' => $totalAttempts ?? 0],
                        ['label' => 'Finalizate', 'value' => $finishedAttempts ?? 0],
                        ['label' => 'Best %', 'value' => number_format($bestPercent ?? 0, 1) . '%'],
                        ['label' => 'Medie %', 'value' => number_format($avgPercent ?? 0, 1) . '%'],
                    ];
                @endphp

                @foreach($cards as $c)
                    <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur p-5 hover:bg-white/7 transition">
                        <div class="text-xs uppercase tracking-wider text-slate-300">{{ $c['label'] }}</div>
                        <div class="mt-2 text-2xl font-semibold text-white">{{ $c['value'] }}</div>
                        <div class="mt-3 h-1.5 w-full rounded-full bg-white/10 overflow-hidden">
                            <div class="h-full w-2/3 rounded-full bg-gradient-to-r from-indigo-400 to-cyan-300"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Main grid --}}
            <div class="mt-8 grid gap-6 lg:grid-cols-3">
                {{-- Left: Quick actions / categories --}}
                <div class="lg:col-span-2 rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-white">Începe rapid</h2>
                        <span class="text-xs text-slate-300">Alege o categorie</span>
                    </div>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        @forelse(($categories ?? []) as $cat)
                            <a href="{{ route('tests.start', $cat) }}"
                               class="group rounded-2xl bg-white/5 ring-1 ring-white/10 p-5 hover:bg-white/10 transition">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-sm font-semibold text-white group-hover:text-white">
                                            {{ $cat->name }}
                                        </div>
                                        <div class="mt-1 text-xs text-slate-300">
                                            Începe un test din această categorie
                                        </div>
                                    </div>
                                    <div class="mt-0.5 shrink-0 rounded-xl bg-indigo-500/15 ring-1 ring-indigo-400/30 px-2 py-1 text-xs text-indigo-200">
                                        Start
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-6 text-sm text-slate-300">
                                Nu există categorii încă. Importă întrebări în Admin.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Right: Recent attempts --}}
                <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-white">Ultimele teste</h2>
                        <a href="{{ route('history.index') }}" class="text-xs text-slate-300 hover:text-white transition">
                            Vezi tot
                        </a>
                    </div>

                    <div class="mt-5 space-y-3">
                        @forelse(($recentAttempts ?? []) as $a)
                            <a href="{{ route('history.show', $a) }}"
                               class="block rounded-2xl bg-white/5 ring-1 ring-white/10 p-4 hover:bg-white/10 transition">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-medium text-white">
                                        {{ optional($a->category)->name ?? 'Test' }}
                                    </div>
                                    <div class="text-xs text-slate-300">
                                        {{ $a->finished_at ? 'Finalizat' : 'În desfășurare' }}
                                    </div>
                                </div>
                                <div class="mt-2 text-xs text-slate-300">
                                    Întrebări: {{ $a->total_questions ?? 0 }} · Corecte: {{ $a->correct_count ?? 0 }}
                                </div>
                            </a>
                        @empty
                            <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-4 text-sm text-slate-300">
                                N-ai încă teste. Apasă „Test nou”.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Footer hint --}}
            <div class="mt-10 text-xs text-slate-400">
                Tip: fă 3 teste pe zi ca să vezi progres clar în medie și best score.
            </div>
        </div>
    </div>
</x-app-layout>
