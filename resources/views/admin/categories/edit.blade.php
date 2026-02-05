<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Admin • Editează categorie</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-4">
                    @csrf @method('PUT')

                    <div>
                        <label class="block text-sm font-medium">Nume</label>
                        <input name="name" value="{{ old('name', $category->name) }}" class="mt-1 w-full rounded border-gray-300" required>
                        @error('name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Slug (opțional)</label>
                        <input name="slug" value="{{ old('slug', $category->slug) }}" class="mt-1 w-full rounded border-gray-300">
                        @error('slug') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <button class="px-4 py-2 rounded bg-black text-white">Salvează</button>
                    <a class="ml-3 underline" href="{{ route('admin.categories.index') }}">Înapoi</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
