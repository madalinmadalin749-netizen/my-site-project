<x-app-layout>
    <x-slot name="header">
        @php
            $total = $answers->count();
            $answered = $answers->filter(fn($r) => !is_null($r->selected))->count();
            $pct = $total > 0 ? (int) round($answered * 100 / $total) : 0;
        @endphp

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Test: {{ $attempt->category->name ?? 'Categorie' }}
                </h2>
                <div class="text-sm text-gray-500">
                    Progres: <span class="font-semibold text-gray-800" id="answeredCountTop">{{ $answered }}</span>/<span class="font-semibold text-gray-800">{{ $total }}</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard') }}"
                   class="px-4 py-2 rounded-lg bg-gray-100 text-gray-900 text-sm font-medium hover:bg-gray-200">
                    Dashboard
                </a>

                {{-- Submit (sus) --}}
                <button type="submit"
                        form="submitForm"
                        onclick="return confirmSubmitLive({{ $total }});"
                        class="px-4 py-2 rounded-lg bg-black text-white text-sm font-medium hover:bg-gray-900">
                    Finalizează
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            @if (session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Summary/progress card --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center justify-between gap-4">
                    <div class="text-sm text-gray-600">
                        Răspunsuri completate:
                        <span class="font-semibold text-gray-900" id="answeredCount">{{ $answered }}</span>
                        / {{ $total }}
                    </div>

                    <div class="text-sm text-gray-600">
                        Întrebări: <span class="font-semibold text-gray-900">{{ $total }}</span>
                    </div>
                </div>

                {{-- bară progres (inline, ca să nu o mai pierzi) --}}
                <div style="margin-top:10px;height:8px;background:#f3f4f6;border-radius:9999px;overflow:hidden;">
                    <div id="progressBar"
                         style="height:8px;background:#111827;width: {{ $pct }}%;border-radius:9999px;"></div>
                </div>
            </div>

            {{-- Formular principal --}}
            <form id="submitForm" method="POST" action="{{ route('tests.submit', $attempt) }}">
                @csrf

                <div class="space-y-4">
                    @foreach($answers as $index => $row)
                        @php
                            // întrebarea poate fi clasică sau AI
                            $q = $row->question ?? $row->aiQuestion;

                            // cheie unică pentru submit
                            $key = $row->question_id ? "q_{$row->question_id}" : "ai_{$row->ai_question_id}";
                            $name = "answers[{$key}]";

                            $selected = $row->selected ? strtolower((string)$row->selected) : null;

                            $optMap = [
                                'a' => $q?->a,
                                'b' => $q?->b,
                                'c' => $q?->c,
                                'd' => $q?->d,
                            ];
                        @endphp

                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div class="font-semibold text-gray-900">
                                    {{ $index + 1 }}. {{ $q?->prompt ?? 'Întrebare' }}
                                </div>

                                @if($selected)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-100">
                                        Răspuns ales
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-500 border border-gray-100">
                                        Necompletat
                                    </span>
                                @endif
                            </div>

                            <div class="mt-4 space-y-2">
                                @foreach($optMap as $opt => $label)
                                    @continue(empty($label))

                                    <label class="flex items-start gap-3 rounded-xl border border-gray-100 p-3 hover:border-gray-200 cursor-pointer">
                                        <input type="radio"
                                               name="{{ $name }}"
                                               value="{{ $opt }}"
                                               @checked($selected === $opt)
                                               class="mt-1">
                                        <div class="text-sm text-gray-900">
                                            <span class="font-semibold uppercase">{{ $opt }}.</span>
                                            {{ $label }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Sticky footer --}}
                <div class="sticky bottom-4 mt-6">
                    <div class="bg-white/90 backdrop-blur rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Progres:
                            <span class="font-semibold text-gray-900" id="answeredCountFooter">{{ $answered }}</span>
                            / {{ $total }}
                        </div>

                        <button type="submit"
                                onclick="return confirmSubmitLive({{ $total }});"
                                class="px-5 py-2 rounded-xl bg-black text-white text-sm font-medium hover:bg-gray-900">
                            Finalizează testul
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        function confirmSubmitLive(total) {
            // contează câte întrebări au un radio bifat (un radio per întrebare)
            const answered = document.querySelectorAll('#submitForm input[type="radio"]:checked').length;
            if (answered >= total) return true;
            return confirm(`Mai ai ${total - answered} întrebări necompletate. Sigur vrei să finalizezi?`);
        }

        function updateProgress() {
            const total = {{ $total }};
            const answered = document.querySelectorAll('#submitForm input[type="radio"]:checked').length;

            const pct = total > 0 ? Math.round(answered * 100 / total) : 0;

            const el1 = document.getElementById('answeredCount');
            const el2 = document.getElementById('answeredCountTop');
            const el3 = document.getElementById('answeredCountFooter');
            const bar = document.getElementById('progressBar');

            if (el1) el1.textContent = answered;
            if (el2) el2.textContent = answered;
            if (el3) el3.textContent = answered;
            if (bar) bar.style.width = pct + '%';
        }

        document.addEventListener('change', (e) => {
            if (e.target && e.target.matches('#submitForm input[type="radio"]')) {
                updateProgress();
            }
        });
    </script>
</x-app-layout>
