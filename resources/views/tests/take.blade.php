<x-app-layout>
    <div class="relative overflow-hidden min-h-[calc(100vh-4rem)] bg-gradient-to-b from-slate-950 via-slate-950 to-slate-900 pb-16">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-32 left-1/2 h-[520px] w-[520px] -translate-x-1/2 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div class="absolute top-40 right-[-120px] h-[420px] w-[420px] rounded-full bg-cyan-400/10 blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
            {{-- top bar --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="text-xs uppercase tracking-wider text-slate-400">
                        {{ $attempt->category->name ?? 'Test' }}
                    </div>
                    <h1 class="mt-1 text-xl sm:text-2xl font-semibold tracking-tight text-white">
                        Întrebarea {{ $currentIndex }} din {{ $totalQuestions }}
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    {{-- AI button --}}
                    <form method="POST" action="{{ route('tests.aiGenerate', $attempt) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/10 hover:bg-white/15 transition">
                            AI
                        </button>
                    </form>

                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center justify-center rounded-xl bg-white/10 px-4 py-2 text-sm font-medium text-white ring-1 ring-white/10 backdrop-blur hover:bg-white/15 transition">
                        Ieși
                    </a>
                </div>
            </div>

            @php
                $pct = (int) round(max(0, $currentIndex - 1) / max(1, $totalQuestions) * 100);
            @endphp
            <div class="mt-6 h-2 w-full rounded-full bg-white/10 overflow-hidden">
                <div class="h-full rounded-full bg-gradient-to-r from-indigo-400 to-cyan-300" style="width: {{ $pct }}%"></div>
            </div>

            {{-- question card --}}
            <div class="mt-6 rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur p-6 sm:p-7">
                <div class="text-sm text-slate-300">Întrebare</div>
                <div class="mt-2 text-lg sm:text-xl font-semibold text-white leading-snug">
                    {{ $question->prompt }}
                </div>

                <form method="POST" action="{{ route('tests.answer', $attempt) }}" class="mt-6 space-y-3">
                    @csrf
                    <input type="hidden" name="question_id" value="{{ $question->id }}"/>

                    @php
                        $opts = [
                            'a' => $question->a ?? null,
                            'b' => $question->b ?? null,
                            'c' => $question->c ?? null,
                            'd' => $question->d ?? null,
                        ];
                    @endphp

                    @foreach($opts as $key => $text)
                        @continue(empty($text))

                        <label class="group block cursor-pointer rounded-2xl bg-white/5 ring-1 ring-white/10 px-4 py-4 hover:bg-white/10 transition">
                            <div class="flex items-start gap-3">
                                <input
                                    type="radio"
                                    name="selected"
                                    value="{{ $key }}"
                                    class="mt-1 h-4 w-4 accent-indigo-400"
                                    required
                                />
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-500/15 ring-1 ring-indigo-400/30 text-xs font-semibold text-indigo-200 uppercase">
                                            {{ $key }}
                                        </span>
                                        <div class="text-sm font-semibold text-white">Varianta {{ strtoupper($key) }}</div>
                                    </div>
                                    <div class="mt-2 text-sm text-slate-300 leading-relaxed">{{ $text }}</div>
                                </div>
                            </div>
                        </label>
                    @endforeach

                    <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-xs text-slate-400">
                            Alege varianta și mergi mai departe.
                        </div>

                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-indigo-500/90 px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-500 transition shadow-lg shadow-indigo-500/20">
                            {{ $isLast ? 'Finalizează' : 'Următoarea' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- FEEDBACK OVERLAY (arată după answer) --}}
        @if(!empty($feedback))
            @php
                $selected = strtolower($feedback['selected'] ?? '');
                $correct  = strtolower($feedback['correct'] ?? '');
                $isCorrect = (bool)($feedback['isCorrect'] ?? false);
                $fopts = [
                    'a' => $feedback['a'] ?? null,
                    'b' => $feedback['b'] ?? null,
                    'c' => $feedback['c'] ?? null,
                    'd' => $feedback['d'] ?? null,
                ];
            @endphp

            <div id="feedbackOverlay" class="fixed inset-0 z-50">
                <div class="absolute inset-0 bg-black/70"></div>

                <div class="relative mx-auto mt-16 max-w-3xl px-4">
                    <div class="rounded-2xl bg-slate-900 ring-1 ring-white/10 p-6 shadow-2xl">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <div class="text-xs uppercase tracking-wider text-slate-400">Feedback</div>
                                <div class="mt-1 text-lg font-semibold text-white">
                                    {{ $isCorrect ? 'Corect ✅' : 'Greșit ❌' }}
                                </div>
                            </div>
                            <div class="text-xs text-slate-300">Continui automat…</div>
                        </div>

                        <div class="mt-5 text-white font-semibold leading-snug">
                            {{ $feedback['prompt'] ?? '' }}
                        </div>

                        <div class="mt-6 space-y-3">
                            @foreach($fopts as $k => $text)
                                @continue(empty($text))
                                @php
                                    $k = strtolower($k);
                                    $isSel = $k === $selected;
                                    $isCor = $k === $correct;

                                    $cls = 'bg-white/5 ring-white/10 text-slate-200';
                                    if ($isCor) $cls = 'bg-emerald-500/10 ring-emerald-400/30 text-emerald-100';
                                    if ($isSel && !$isCor) $cls = 'bg-rose-500/10 ring-rose-400/30 text-rose-100';
                                @endphp

                                <div class="rounded-2xl ring-1 {{ $cls }} px-4 py-4">
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5 inline-flex h-7 w-7 items-center justify-center rounded-lg bg-white/10 ring-1 ring-white/10 text-xs font-semibold uppercase">
                                            {{ $k }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-sm font-semibold">
                                                Varianta {{ strtoupper($k) }}
                                                @if($isCor) <span class="ml-2 text-xs">(corectă)</span> @endif
                                                @if($isSel && !$isCor) <span class="ml-2 text-xs">(aleasă)</span> @endif
                                            </div>
                                            <div class="mt-1 text-sm opacity-90">{{ $text }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 h-2 w-full rounded-full bg-white/10 overflow-hidden">
                            <div id="bar" class="h-full rounded-full bg-gradient-to-r from-indigo-400 to-cyan-300" style="width:0%"></div>
                        </div>
                    </div>
                </div>

                <script>
                    (function () {
                        const overlay = document.getElementById('feedbackOverlay');
                        const bar = document.getElementById('bar');

                        let pct = 0;
                        const tick = setInterval(() => {
                            pct += 8;
                            bar.style.width = pct + '%';
                            if (pct >= 100) clearInterval(tick);
                        }, 60);

                        setTimeout(() => {
                            overlay.remove();
                        }, 800);
                    })();
                </script>
            </div>
        @endif
    </div>
</x-app-layout>
