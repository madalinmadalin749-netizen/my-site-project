<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Test nou</h2>
            <a href="{{ route('tests.history') }}" class="text-sm text-gray-600 hover:text-gray-900">
                Vezi istoricul →
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
                    <div>
                        <div class="text-sm text-gray-500">Categorii disponibile</div>
                        <div class="text-2xl font-semibold text-gray-900">{{ $categories->count() }}</div>
                    </div>

                    <div class="text-sm text-gray-500">
                        Alege o categorie și numărul de întrebări, apoi pornește testul.
                    </div>
                </div>

                @if(session('error'))
                    <div class="mb-5 p-4 rounded-lg bg-red-50 text-red-800 border border-red-200">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-5 p-4 rounded-lg bg-red-50 text-red-800 border border-red-200">
                        <div class="font-semibold mb-2">Verifică formularul:</div>
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('tests.start') }}" class="space-y-6">
                    @csrf

                    {{-- Categorie --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Categorie
                        </label>

                        <select
                            name="category_id"
                            class="w-full rounded-lg border-gray-300 focus:border-black focus:ring-black"
                            required
                        >
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" @selected(old('category_id') == $c->id)>
                                    {{ $c->name }} ({{ $c->questions_count }} întrebări)
                                </option>
                            @endforeach
                        </select>

                        <div class="mt-2 text-xs text-gray-500">
                            Recomandat: alege o categorie cu minim 20 întrebări pentru varietate.
                        </div>
                    </div>

                    {{-- Număr întrebări --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Număr întrebări
                        </label>

                        <div class="flex flex-wrap gap-2 mb-3">
                            @foreach([5, 10, 20, 30, 50] as $preset)
                                <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 hover:border-gray-300 cursor-pointer">
                                    <input
                                        type="radio"
                                        name="count_preset"
                                        value="{{ $preset }}"
                                        class="text-black focus:ring-black"
                                        onclick="document.getElementById('countInput').value=this.value;"
                                    >
                                    <span class="text-sm">{{ $preset }}</span>
                                </label>
                            @endforeach
                        </div>

                        <input
                            id="countInput"
                            type="number"
                            name="count"
                            value="{{ old('count', 20) }}"
                            min="5"
                            max="100"
                            class="w-full rounded-lg border-gray-300 focus:border-black focus:ring-black"
                        >

                        <div class="mt-2 text-xs text-gray-500">
                            Minim 5, maxim 100 (în funcție de ce ai setat în controller).
                        </div>
                    </div>

                    {{-- Start --}}
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button
                            type="submit"
                            class="w-full sm:w-auto px-5 py-3 rounded-lg bg-black text-white font-semibold hover:opacity-90"
                        >
                            Începe testul
                        </button>

                        <a
                            href="{{ route('tests.history') }}"
                            class="w-full sm:w-auto px-5 py-3 rounded-lg bg-gray-100 text-gray-800 font-semibold text-center hover:bg-gray-200"
                        >
                            Istoric
                        </a>
                    </div>
                </form>

            </div>

            {{-- Carduri categorii (preview rapid) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                @foreach($categories as $c)
                    <div class="bg-white shadow-sm sm:rounded-lg p-5 border border-gray-100">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-lg font-semibold text-gray-900">{{ $c->name }}</div>
                                <div class="text-sm text-gray-500 mt-1">{{ $c->questions_count }} întrebări</div>
                            </div>

                            <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-700">
                                categorie
                            </span>
                        </div>

                        <div class="mt-4">
                            <form method="POST" action="{{ route('tests.start') }}">
                                @csrf
                                <input type="hidden" name="category_id" value="{{ $c->id }}">
                                <input type="hidden" name="count" value="20">

                                <button class="w-full px-4 py-2 rounded-lg bg-black text-white font-semibold hover:opacity-90">
                                    Start rapid (20 întrebări)
                                </button>
                            </form>
                        </div>

                        <div class="mt-2 text-xs text-gray-500">
                            Pornește direct cu 20 întrebări din această categorie.
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
