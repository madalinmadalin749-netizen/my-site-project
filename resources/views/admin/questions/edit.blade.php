<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Admin • Editează întrebare #{{ $question->id }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('admin.questions.update', $question) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium">Categorie</label>
                        <select name="category_id" class="mt-1 w-full rounded border-gray-300" required>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" @selected(old('category_id', $question->category_id) == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Întrebare</label>
                        <textarea name="prompt" rows="3" class="mt-1 w-full rounded border-gray-300" required>{{ old('prompt', $question->prompt) }}</textarea>
                        @error('prompt') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Varianta A</label>
                            <input name="a" value="{{ old('a', $question->a) }}" class="mt-1 w-full rounded border-gray-300" required>
                            @error('a') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Varianta B</label>
                            <input name="b" value="{{ old('b', $question->b) }}" class="mt-1 w-full rounded border-gray-300" required>
                            @error('b') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Varianta C (opțional)</label>
                            <input name="c" value="{{ old('c', $question->c) }}" class="mt-1 w-full rounded border-gray-300">
                            @error('c') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Varianta D (opțional)</label>
                            <input name="d" value="{{ old('d', $question->d) }}" class="mt-1 w-full rounded border-gray-300">
                            @error('d') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Răspuns corect</label>
                            <select name="correct" class="mt-1 w-full rounded border-gray-300" required>
                                @foreach(['a'=>'A','b'=>'B','c'=>'C','d'=>'D'] as $val => $label)
                                    <option value="{{ $val }}" @selected(old('correct', strtolower($question->correct)) == $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('correct') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Dificultate (1-5)</label>
                            <input type="number" name="difficulty" value="{{ old('difficulty', $question->difficulty) }}" min="1" max="5" class="mt-1 w-full rounded border-gray-300" required>
                            @error('difficulty') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Explicație (opțional)</label>
                        <textarea name="explanation" rows="3" class="mt-1 w-full rounded border-gray-300">{{ old('explanation', $question->explanation) }}</textarea>
                        @error('explanation') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <button class="px-4 py-2 rounded bg-black text-white">Salvează</button>
                    <a class="ml-3 underline" href="{{ route('admin.questions.index') }}">Înapoi</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
