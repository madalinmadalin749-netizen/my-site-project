<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl">Admin • Întrebări</h2>
            <a href="{{ route('admin.questions.create') }}" class="px-3 py-2 rounded bg-black text-white text-sm">+ Întrebare</a>
<form method="POST" action="{{ route('admin.questions.import') }}" enctype="multipart/form-data" class="flex gap-3 items-end">
    @csrf

    <div>
        <label class="block text-sm font-medium">Import CSV</label>
        <input type="file" name="csv" accept=".csv" class="mt-1 text-sm" required>
        @error('csv') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <button class="px-3 py-2 rounded border text-sm">Importă</button>
</form>
            <div class="text-xs text-gray-500 mt-2">
                Format recomandat: <code>category_slug,prompt,a,b,c,d,correct,difficulty,explanation</code>.
                Exemplu: <a class="underline" href="{{ asset('sample_questions.csv') }}">descarcă sample_questions.csv</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                @if (session('status'))
                    <div class="p-3 rounded bg-green-50 text-green-800">{{ session('status') }}</div>
                @endif

                <form method="GET" class="flex gap-3 items-end">
                    <div>
                        <label class="block text-sm font-medium">Categorie</label>
                        <select name="category_id" class="mt-1 rounded border-gray-300">
                            <option value="">Toate</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="px-3 py-2 rounded border text-sm">Filtrează</button>
                </form>
            <div class="text-xs text-gray-500 mt-2">
                Format recomandat: <code>category_slug,prompt,a,b,c,d,correct,difficulty,explanation</code>.
                Exemplu: <a class="underline" href="{{ asset('sample_questions.csv') }}">descarcă sample_questions.csv</a>
            </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="py-2 pr-4">ID</th>
                                <th class="py-2 pr-4">Categorie</th>
                                <th class="py-2 pr-4">Întrebare</th>
                                <th class="py-2 pr-4">Corect</th>
                                <th class="py-2 pr-4 text-right">Acțiuni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($questions as $q)
                                <tr class="border-b">
                                    <td class="py-3 pr-4 text-gray-600">{{ $q->id }}</td>
                                    <td class="py-3 pr-4">{{ $q->category->name }}</td>
                                    <td class="py-3 pr-4">{{ \Illuminate\Support\Str::limit($q->prompt, 80) }}</td>
                                    <td class="py-3 pr-4 font-semibold uppercase">{{ $q->correct }}</td>
                                    <td class="py-3 pr-4 text-right space-x-3">
                                        <a class="underline" href="{{ route('admin.questions.edit', $q) }}">Editează</a>
                                        <form class="inline" method="POST" action="{{ route('admin.questions.destroy', $q) }}" onsubmit="return confirm('Ștergi întrebarea?');">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 underline">Șterge</button>
                                        </form>
            <div class="text-xs text-gray-500 mt-2">
                Format recomandat: <code>category_slug,prompt,a,b,c,d,correct,difficulty,explanation</code>.
                Exemplu: <a class="underline" href="{{ asset('sample_questions.csv') }}">descarcă sample_questions.csv</a>
            </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $questions->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
