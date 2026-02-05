<x-layouts.auth>
    <!-- background (same as landing) -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950 via-slate-950 to-black"></div>
        <div class="absolute -top-24 left-1/2 h-[520px] w-[720px] -translate-x-1/2 rounded-full bg-indigo-600/25 blur-3xl"></div>
        <div class="absolute top-[35%] -left-40 h-[520px] w-[520px] rounded-full bg-cyan-500/20 blur-3xl"></div>
        <div class="absolute bottom-[-160px] right-[-160px] h-[560px] w-[560px] rounded-full bg-fuchsia-500/15 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.08]"
             style="background-image: linear-gradient(to right, rgba(255,255,255,.08) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(255,255,255,.08) 1px, transparent 1px);
                    background-size: 64px 64px;"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-black/0 via-black/0 to-black/40"></div>
    </div>

    <div class="mx-auto min-h-screen max-w-6xl px-4 py-10 sm:px-6">
        <!-- Topbar -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('landing') }}" class="flex items-center gap-2">
                <img src="{{ asset('brand/respins-logo.svg') }}" class="h-9 w-9" alt="respins.ro">
                <span class="text-lg font-bold tracking-tight">
                    respins<span class="text-indigo-300">.ro</span>
                </span>
            </a>

            <div class="flex items-center gap-2">
                <a href="{{ route('tests.index') }}"
                   class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-slate-100">
                    Test nou
                </a>
                <a href="{{ route('tests.history') }}"
                   class="rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold ring-1 ring-white/15 hover:bg-white/15">
                    Istoric
                </a>
                <a href="{{ route('profile.edit') }}"
                   class="rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold ring-1 ring-white/15 hover:bg-white/15">
                    Profil
                </a>
            </div>
        </div>

        <!-- Heading -->
        <div class="mt-8">
            <h1 class="text-2xl font-bold">Dashboard</h1>
            <p class="mt-1 text-sm text-slate-300">Progresul tău, pe scurt.</p>
        </div>

        <!-- Stats -->
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur">
                <div class="text-xs text-slate-300">Total teste</div>
                <div class="mt-2 text-3xl font-bold">{{ $totalAttempts ?? 0 }}</div>
            </div>

            <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur">
                <div class="text-xs text-slate-300">Teste finalizate</div>
                <div class="mt-2 text-3xl font-bold">{{ $finishedAttempts ?? 0 }}</div>
            </div>

            <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur">
                <div class="text-xs text-slate-300">Best scor</div>
                <div class="mt-2 text-3xl font-bold">{{ number_format($bestPercent ?? 0, 0) }}%</div>
            </div>

            <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur">
                <div class="text-xs text-slate-300">Scor mediu</div>
                <div class="mt-2 text-3xl font-bold">{{ number_format($avgPercent ?? 0, 0) }}%</div>
            </div>
        </div>

        <!-- Main -->
        <div class="mt-6 grid gap-4 lg:grid-cols-3">
            <div class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10 backdrop-blur lg:col-span-2">
                <div class="flex items-center justify-between">
                    <div class="text-sm font-semibold">Recomandare AI</div>
                    <div class="text-xs text-slate-400">bazată pe rezultate</div>
                </div>

                <div class="mt-4 rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                    <p class="text-sm text-slate-200">
                        Repetă capitolele unde ai cele mai multe greșeli, apoi refă un test de 20 întrebări.
                    </p>

                    <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                        <a href="{{ route('tests.index') }}"
                           class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-slate-100">
                            Începe un test
                        </a>
                        <a href="{{ route('tests.history') }}"
                           class="inline-flex items-center justify-center rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold ring-1 ring-white/15 hover:bg-white/15">
                            Vezi istoricul
                        </a>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10 backdrop-blur">
                <div class="text-sm font-semibold">Acțiuni rapide</div>

                <div class="mt-4 space-y-2">
                    <a href="{{ route('tests.index') }}" class="block rounded-xl bg-white/5 px-4 py-3 ring-1 ring-white/10 hover:bg-white/10">
                        <div class="text-sm font-semibold">Test nou</div>
                        <div class="mt-1 text-xs text-slate-300">Alege categorie & număr întrebări</div>
                    </a>
                    <a href="{{ route('tests.history') }}" class="block rounded-xl bg-white/5 px-4 py-3 ring-1 ring-white/10 hover:bg-white/10">
                        <div class="text-sm font-semibold">Istoric</div>
                        <div class="mt-1 text-xs text-slate-300">Rezultate, scoruri, progres</div>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="block rounded-xl bg-white/5 px-4 py-3 ring-1 ring-white/10 hover:bg-white/10">
                        <div class="text-sm font-semibold">Profil</div>
                        <div class="mt-1 text-xs text-slate-300">Setări cont</div>
                    </a>
                </div>
            </div>
        </div>

        <p class="mt-8 text-xs text-slate-400">
            Tip: consecvența bate maratoanele — 20–30 minute/zi e suficient.
        </p>
    </div>
</x-layouts.auth>
