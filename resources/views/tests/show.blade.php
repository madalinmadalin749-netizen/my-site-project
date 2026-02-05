<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $attempt->category->name }} • Întrebare
            </h2>
            <div class="text-sm text-gray-500">
                Corecte: {{ $attempt->correct_count }} / {{ $attempt->total_questions }}
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-5">
                <div class="text-lg font-medium text-gray-900">
                    {{ $q->prompt }}
                </div>

                <form method="POST" action="{{ route('tests.answer', $attempt) }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="question_id" value="{{ $q->id }}">

                    @foreach(['a','b','c','d'] as $opt)
                        @php $val = $q->{$opt}; @endphp
                        @if($val)
                            <label class="flex gap-3 items-start p-3 rounded border hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="selected" value="{{ $opt }}" class="mt-1" required>
                                <div>
                                    <div class="font-semibold uppercase text-xs text-gray-500">{{ $opt }}</div>
                                    <div class="text-gray-900">{{ $val }}</div>
                                </div>
                            </label>
                        @endif
                    @endforeach

                    <button class="px-4 py-2 rounded bg-black text-white">
                        Trimite răspuns
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
