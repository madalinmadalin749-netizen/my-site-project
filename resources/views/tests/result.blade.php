<x-app-layout>
    <div class="relative overflow-hidden min-h-[calc(100vh-4rem)] bg-gradient-to-b from-slate-950 via-slate-950 to-slate-900 pb-16">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-32 left-1/2 h-[520px] w-[520px] -translate-x-1/2 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div class="absolute top-40 right-[-120px] h-[420px] w-[420px] rounded-full bg-cyan-400/10 blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <div class="text-xs uppercase tracking-wider text-slate-400">Rezultat</div>
                    <h1 class="mt-1 text-2xl sm:text-3xl font-semibold tracking-tight text-white">
                        {{ $attempt->category->name ?? 'Test' }}
                    </h1>
                    <p class="mt-1 text-sm text-slate-300">
                        Vezi scorul și întrebările.
                    </p>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('tests.index') }}"
                       class="inline-flex items-center justify-center rounded-xl bg-white/10 px-4 py-2 text-sm font-medium text-white ring-1 ring-white/10 backdrop-blur hover:bg-white/15 transition">
                        Test nou
                    </a>
                    <a href="{{ route('tests.history') }}"
                       class="inline-flex items-center justify-center rounded-xl bg-indigo-500/90 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 transition shadow-lg shadow-indigo-500/20">
                        Istoric
                    </a>
                </div>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur p-6">
                    <div class="text-xs uppercase tracking-wider text-slate-400">Scor</div>
                    <div class="mt-2 text-3xl font-semibold text-white">{{ $percent }}%</div>
                    <div class="mt-2 text-sm text-slate-300">{{ $correct }}/{{ $total }} corecte</div>
                </div>

                <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur p-6">
                    <div class="text-xs uppercase tracking-wider text-slate-400">Status</div>
                    <div class="mt-2 text-lg font-semibold text-white">
                        {{ $attempt->finished_at ? 'Finalizat' : 'În lucru' }}
                    </div>
                    <div class="mt-2 text-sm text-slate-300">
                        {{ $attempt->finished_at ? \Illuminate\Support\Carbon::parse($attempt->finished_at)->format('d.m.Y H:i') : '—' }}
                    </div>
                </div>

                <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur p-6">
                    <div class="text-xs uppercase tracking-wider text-slate-400">Următorul pas</div>
                    <div class="mt-2 text-sm text-slate-300">
                        Fă încă un test azi ca să-ți crești media.
                    </div>

                    <div class="mt-4 flex gap-3">
                        <a href="{{ route('tests.index') }}"
                           class="inline-flex flex-1 items-center justify-center rounded-xl bg-emerald-500/90 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500 transition shadow-lg shadow-emerald-500/20">
                            Start
                        </a>

                        <button type="button" onclick="openPaywall()"
                                class="inline-flex flex-1 items-center justify-center rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/10 hover:bg-white/15 transition">
                            Abonamente
                        </button>
                    </div>
                </div>
            </div>

            {{-- Questions --}}
            <div class="mt-8 rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur overflow-hidden">
                <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
                    <div class="font-semibold text-white">Întrebări</div>
                    <div class="text-sm text-slate-300">{{ count($answers ?? []) }} iteme</div>
                </div>

                <div class="divide-y divide-white/10">
                    @foreach(($answers ?? []) as $a)
                        @php
                            $q = $a->question ?? $a->aiQuestion ?? null;
                            $sel = $a->selected ?? null;  // FIX: selected
                            $isCorrect = (bool)($a->is_correct ?? false);
                        @endphp

                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div class="text-white font-semibold leading-snug">
                                    {{ $q->prompt ?? 'Întrebare' }}
                                </div>

                                <div class="shrink-0">
                                    @if(empty($sel))
                                        <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs text-slate-200 ring-1 ring-white/10">
                                            N/A
                                        </span>
                                    @elseif($isCorrect)
                                        <span class="inline-flex items-center rounded-full bg-emerald-500/15 px-3 py-1 text-xs text-emerald-200 ring-1 ring-emerald-400/30">
                                            Corect ({{ strtoupper($sel) }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-rose-500/15 px-3 py-1 text-xs text-rose-200 ring-1 ring-rose-400/30">
                                            Greșit ({{ strtoupper($sel) }})
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($q)
                                <div class="mt-4 grid gap-2 sm:grid-cols-2">
                                    @foreach(['a','b','c','d'] as $k)
                                        @php $txt = $q->{$k} ?? null; @endphp
                                        @continue(empty($txt))
                                        <div class="rounded-xl bg-white/5 ring-1 ring-white/10 p-3 text-slate-300">
                                            <div class="text-xs text-slate-400 uppercase">{{ $k }}</div>
                                            <div class="mt-1">{{ $txt }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Paywall Modal --}}
            <div id="paywallModal" class="hidden fixed inset-0 z-50">
                <div class="absolute inset-0 bg-black/60" onclick="closePaywall()"></div>
                <div class="relative mx-auto mt-24 max-w-lg px-4">
                    <div class="rounded-2xl bg-slate-900 ring-1 ring-white/10 p-6 shadow-2xl">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="text-lg font-semibold text-white">Abonamente</div>
                                <div class="mt-1 text-sm text-slate-300">
                                    Deblochează statistici avansate și seturi nelimitate.
                                </div>
                            </div>
                            <button type="button" onclick="closePaywall()"
                                    class="rounded-xl bg-white/10 px-3 py-2 text-sm font-semibold text-white ring-1 ring-white/10 hover:bg-white/15">
                                Închide
                            </button>
                        </div>

                        <div class="mt-5 grid gap-3">
                            <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-5">
                                <div class="flex items-center justify-between">
                                    <div class="text-white font-semibold">Pro</div>
                                    <div class="text-slate-200 font-semibold">19 lei/lună</div>
                                </div>
                                <ul class="mt-3 space-y-2 text-sm text-slate-300">
                                    <li>• Statistici pe categorii</li>
                                    <li>• Istoric complet</li>
                                    <li>• Seturi nelimitate</li>
                                </ul>
                            </div>

                            <a href="#"
                               class="inline-flex items-center justify-center rounded-xl bg-indigo-500/90 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500 transition shadow-lg shadow-indigo-500/20">
                                Activează abonamentul
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function openPaywall() { document.getElementById('paywallModal').classList.remove('hidden'); }
                function closePaywall() { document.getElementById('paywallModal').classList.add('hidden'); }
            </script>
        </div>
    </div>
</x-app-layout>
