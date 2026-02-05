<x-app-layout>
    <div class="min-h-[calc(100vh-4rem)] bg-gradient-to-b from-slate-950 via-slate-950 to-slate-900">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -top-32 left-1/2 h-[520px] w-[520px] -translate-x-1/2 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div class="absolute top-40 right-[-120px] h-[420px] w-[420px] rounded-full bg-cyan-400/10 blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-white">Începe un test</h1>
                    <p class="mt-1 text-sm text-slate-300">Alege categoria și apasă Start.</p>
                </div>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($categories as $category)
                    <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur p-6 hover:bg-white/7 transition">
                        <div class="text-base font-semibold text-white">{{ $category->name }}</div>
                        <div class="mt-1 text-sm text-slate-300">
                            {{ $category->description ?? 'Teste rapide cu feedback imediat.' }}
                        </div>

                        <form method="POST" action="{{ route('tests.start') }}" class="mt-5">
                            @csrf
                            <input type="hidden" name="category_id" value="{{ $category->id }}"/>

                            <button type="submit"
                                    class="w-full rounded-xl bg-indigo-500/90 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500 transition shadow-lg shadow-indigo-500/20">
                                Start
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur p-6 text-slate-300">
                        Nu există categorii încă. Importă întrebări din Admin.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
