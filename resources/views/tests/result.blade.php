<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Rezultat
                </h2>
                <div class="text-sm text-gray-500">
                    Categoria: {{ $attempt->category->name ?? '—' }}
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('tests.index') }}"
                   class="px-4 py-2 rounded-lg bg-black text-white text-sm font-medium hover:bg-gray-900">
                    Test nou
                </a>

                <a href="{{ route('tests.history') }}"
                   class="px-4 py-2 rounded-lg bg-gray-100 text-gray-900 text-sm font-medium hover:bg-gray-200">
                    Istoric
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $total   = max(1, (int) $attempt->total_questions);
        $correct = (int) $attempt->correct_count;
        $pct     = (int) round($correct * 100 / $total);
        $pass    = $pct >= 70;

        $finishedAt = $attempt->finished_at
            ? \Illuminate\Support\Carbon::parse($attempt->finished_at)->format('d.m.Y H:i')
            : '—';
    @endphp

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- MARKER: dacă NU vezi asta, nu e view-ul corect --}}

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

            {{-- SCOR --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500">Finalizat la</div>
                        <div class="text-sm font-semibold text-gray-900">{{ $finishedAt }}</div>

                        <div class="mt-3 text-sm text-gray-500">Scor final</div>
                        <div class="text-3xl font-bold text-gray-900">{{ $pct }}%</div>
                        <div class="text-sm text-gray-600">{{ $correct }} / {{ $attempt->total_questions }} corecte</div>
                    </div>

                    <span class="px-3 py-1.5 rounded-full text-sm font-medium
                        {{ $pass ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                        {{ $pass ? 'PASS' : 'SUB 70%' }}
                    </span>
                </div>

                <div class="mt-4 h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-2 bg-orange-500" style="width: {{ $pct }}%"></div>
                </div>
            </div>

            {{-- AI CTA (vizibil și la free) --}}
            <div class="bg-white rounded-xl border border-orange-200 shadow-sm p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="text-sm font-semibold text-orange-600">AI Recomandare</div>
                        <div class="mt-1 text-lg font-semibold text-gray-900">
                            Generează un test nou pe greșelile tale
                        </div>
                        <div class="mt-1 text-sm text-gray-600">
                            Disponibil în Premium (întrebări NOI, personalizate).
                        </div>
                    </div>

                    <div>
                        @if(auth()->user()->is_premium)
                            <form method="POST" action="{{ route('tests.aiGenerate', $attempt) }}">
                                @csrf
                                <button type="submit"
                                        class="px-5 py-2.5 rounded-xl bg-orange-500 text-white text-sm font-medium hover:bg-orange-600">
                                    Generează cu AI
                                </button>
                            </form>
                        @else
                            <button onclick="openPaywall()"
                                    class="px-5 py-2.5 rounded-xl bg-orange-500 text-white text-sm font-medium hover:bg-orange-600">
                                Generează cu AI
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ÎNTREBĂRI --}}
            <div class="space-y-4">
                @foreach($answers as $i => $row)
                    @php
                        $q = $row->question ?? $row->aiQuestion;

                        $selected   = strtolower((string) ($row->selected ?? ''));
                        $correctOpt = strtolower((string) ($q->correct ?? ''));
                        $opts = ['a'=>$q->a,'b'=>$q->b,'c'=>$q->c,'d'=>$q->d];
                    @endphp

                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                        <div class="font-semibold text-gray-900">
                            {{ $i + 1 }}. {{ $q->prompt }}
                        </div>

                        <div class="mt-3 space-y-2">
                            @foreach($opts as $opt => $label)
                                @continue(empty($label))

                                @php
                                    $isCorrectOpt  = $correctOpt === $opt;
                                    $isSelectedOpt = $selected === $opt;

                                    $cls = 'border-gray-100 bg-white';
                                    if ($isCorrectOpt) $cls = 'border-green-200 bg-green-50';
                                    if ($isSelectedOpt && !$isCorrectOpt) $cls = 'border-red-200 bg-red-50';
                                @endphp

                                <div class="p-3 rounded-xl border {{ $cls }}">
                                    <span class="font-semibold uppercase">{{ $opt }}.</span> {{ $label }}
                                </div>
                            @endforeach
                        </div>

                        @if($q->explanation)
                            <div class="mt-3 text-sm text-gray-600 bg-gray-50 border border-gray-100 rounded-lg p-3">
                                <span class="font-semibold">Explicație:</span> {{ $q->explanation }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    {{-- PAYWALL MODAL --}}
    <div id="paywallModal" class="fixed inset-0 bg-black/60 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 relative">
            <button onclick="closePaywall()"
                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">✕</button>

            <div class="text-center">
                <div class="text-sm font-semibold text-orange-600">AI Premium</div>
                <div class="mt-2 text-xl font-bold text-gray-900">
                    Deblochează testele inteligente
                </div>
                <div class="mt-2 text-sm text-gray-600">
                    Primești întrebări noi, generate pe baza greșelilor tale.
                </div>
            </div>

            <ul class="mt-5 space-y-2 text-sm text-gray-700">
                <li>✔ Teste personalizate</li>
                <li>✔ Explicații</li>
                <li>✔ Progres mai rapid</li>
            </ul>

            <div class="mt-6">
                <a href="{{ route('pricing') }}"
                   class="block text-center w-full px-5 py-2.5 rounded-xl bg-orange-500 text-white font-medium hover:bg-orange-600">
                    Vezi abonamentele
                </a>
            </div>
        </div>
    </div>

    <script>
        function openPaywall() {
            document.getElementById('paywallModal').classList.remove('hidden');
        }
        function closePaywall() {
            document.getElementById('paywallModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
