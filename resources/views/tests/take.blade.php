<x-app-layout>
    <div class="min-h-[calc(100vh-4rem)] bg-gradient-to-b from-slate-950 via-slate-950 to-slate-900">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
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
                    <div class="rounded-xl bg-white/5 ring-1 ring-white/10 px-3 py-2 text-xs text-slate-200">
                        Progres: <span class="font-semibold text-white">{{ max(0, $currentIndex - 1) }}</span>/{{ $totalQuestions }}
                    </div>

                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center justify-center rounded-xl bg-white/10 px-4 py-2 text-sm font-medium text-white ring-1 ring-white/10 backdrop-blur hover:bg-white/15 transition">
                        Ieși
                    </a>
                </div>
            </div>

            {{-- progress bar --}}
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
                        $answers = [
                            'A' => $question->a,
                            'B' => $question->b,
                            'C' => $question->c,
                        ];
                    @endphp

                    @foreach($answers as $key => $text)
                        @if($text)
                            <label class="group block cursor-pointer rounded-2xl bg-white/5 ring-1 ring-white/10 px-4 py-4 hover:bg-white/10 transition">
                                <div class="flex items-start gap-3">
                                    <input
                                        type="radio"
                                        name="choice"
                                        value="{{ $key }}"
                                        class="mt-1 h-4 w-4 accent-indigo-400"
                                        required
                                    />
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-500/15 ring-1 ring-indigo-400/30 text-xs font-semibold text-indigo-200">
                                                {{ $key }}
                                            </span>
                                            <div class="text-sm font-semibold text-white">Varianta {{ $key }}</div>
                                        </div>
                                        <div class="mt-2 text-sm text-slate-300 leading-relaxed">
                                            {{ $text }}
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endif
                    @endforeach

                    <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-xs text-slate-400">
                            Alege varianta și mergi mai departe.
                        </div>

                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-indigo-500/90 px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-500 transition shadow-lg shadow-indigo-500/20">
                            {{ $isLast ? 'Finalizează ultima întrebare' : 'Următoarea' }}
                        </button>
                    </div>
                </form>
            </div>

            @if($isLast)
                <form method="POST" action="{{ route('tests.submit', $attempt) }}" class="mt-6">
                    @csrf
                    <button type="submit"
                            class="w-full rounded-2xl bg-emerald-500/90 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-500 transition shadow-lg shadow-emerald-500/20">
                        Finalizează testul
                    </button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
