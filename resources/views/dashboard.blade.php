<x-app-layout>
    <div class="relative min-h-[calc(100vh-4rem)] bg-gradient-to-b from-slate-950 via-slate-950 to-slate-900 pb-16">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-32 left-1/2 h-[520px] w-[520px] -translate-x-1/2 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div class="absolute top-40 right-[-120px] h-[420px] w-[420px] rounded-full bg-cyan-400/10 blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 space-y-8">

            {{-- Header --}}
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-white">
                    Dashboard
                </h1>
                <p class="mt-1 text-sm text-slate-300">
                    Rezumatul activității tale.
                </p>
            </div>

            {{-- CONTINUĂ TESTUL --}}
            @if($activeAttempt)
                <div class="rounded-2xl bg-indigo-500/10 ring-1 ring-indigo-400/30 p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="text-sm text-indigo-200">Test în desfășurare</div>
                        <div class="mt-1 text-lg font-semibold text-white">
                            {{ $activeAttempt->category->name ?? 'Test' }}
                        </div>
                        <div class="mt-1 text-sm text-slate-300">
                            Început la {{ $activeAttempt->created_at->format('d.m.Y H:i') }}
                        </div>
                    </div>

                    <a href="{{ route('tests.show', $activeAttempt) }}"
                       class="inline-flex items-center justify-center rounded-xl bg-indigo-500 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-600 transition shadow-lg shadow-indigo-500/20">
                        Continuă testul
                    </a>
                </div>
            @endif

            {{-- KPI --}}
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-6">
                    <div class="text-xs text-slate-400 uppercase">Teste totale</div>
                    <div class="mt-2 text-3xl font-semibold text-white">{{ $totalAttempts }}</div>
                </div>

                <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-6">
                    <div class="text-xs text-slate-400 uppercase">Finalizate</div>
                    <div class="mt-2 text-3xl font-semibold text-white">{{ $finishedAttempts }}</div>
                </div>

                <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-6">
                    <div class="text-xs text-slate-400 uppercase">Scor maxim</div>
                    <div class="mt-2 text-3xl font-semibold text-white">{{ $bestPercent }}%</div>
                </div>

                <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-6">
                    <div class="text-xs text-slate-400 uppercase">Medie</div>
                    <div class="mt-2 text-3xl font-semibold text-white">{{ $avgPercent }}%</div>
                </div>
            </div>

            {{-- PROGRES PE CATEGORII --}}
            <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 overflow-hidden">
                <div class="px-6 py-4 border-b border-white/10">
                    <div class="font-semibold text-white">Progres pe categorii</div>
                </div>

                <div class="p-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($categoryProgress as $cat)
                        <div class="rounded-xl bg-white/5 ring-1 ring-white/10 p-4">
                            <div class="flex items-center justify-between">
                                <div class="font-medium text-white">{{ $cat->name }}</div>
                                <div class="text-xs text-slate-300">
                                    {{ $cat->finished_count }} teste
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ULTIMELE TESTE --}}
            <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 overflow-hidden">
                <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
                    <div class="font-semibold text-white">Ultimele teste</div>
                    <a href="{{ route('tests.history') }}" class="text-sm text-indigo-400 hover:text-indigo-300">
                        Vezi toate
                    </a>
                </div>

                @if($recentAttempts->isEmpty())
                    <div class="p-6 text-sm text-slate-300">
                        Nu ai teste finalizate încă.
                    </div>
                @else
                    <div class="divide-y divide-white/10">
                        @foreach($recentAttempts as $a)
                            @php
                                $pct = $a->total_questions > 0
                                    ? round(($a->correct_count / $a->total_questions) * 100)
                                    : 0;
                            @endphp

                            <div class="p-6 flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-white">
                                        {{ $a->category->name ?? 'Test' }}
                                    </div>
                                    <div class="text-xs text-slate-400">
                                        {{ $a->finished_at->format('d.m.Y H:i') }}
                                    </div>
                                </div>

                                <div class="flex items-center gap-4">
                                    <div class="text-sm font-semibold text-white">
                                        {{ $pct }}%
                                    </div>

                                    <a href="{{ route('tests.result', $a) }}"
                                       class="rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/10 hover:bg-white/15 transition">
                                        Detalii
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
