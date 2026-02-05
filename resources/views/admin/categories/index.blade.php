<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl">Admin • Categorii</h2>
            <a href="{{ route('admin.categories.create') }}" class="px-3 py-2 rounded bg-black text-white text-sm">+ Categorie</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                @if (session('status'))
                    <div class="p-3 rounded bg-green-50 text-green-800">{{ session('status') }}</div>
                @endif

                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2 pr-4">Nume</th>
                            <th class="py-2 pr-4">Slug</th>
                            <th class="py-2 pr-4 text-right">Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $c)
                            <tr class="border-b">
                                <td class="py-3 pr-4 font-medium">{{ $c->name }}</td>
                                <td class="py-3 pr-4 text-gray-600">{{ $c->slug }}</td>
                                <td class="py-3 pr-4 text-right space-x-3">
                                    <a class="underline" href="{{ route('admin.categories.edit', $c) }}">Editează</a>
                                    <form class="inline" method="POST" action="{{ route('admin.categories.destroy', $c) }}" onsubmit="return confirm('Ștergi categoria?');">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 underline">Șterge</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $categories->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
