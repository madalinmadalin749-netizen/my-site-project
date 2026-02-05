<nav class="sticky top-0 z-40 backdrop-blur bg-slate-950/80 border-b border-white/10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">

            {{-- Logo --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-500/90 text-white font-bold">
                        R
                    </div>
                    <span class="font-semibold tracking-tight text-white">
                        respins<span class="text-indigo-400">.ro</span>
                    </span>
                </a>
            </div>

            {{-- Desktop menu --}}
            <div class="hidden md:flex items-center gap-6 text-sm font-medium">
                <a href="{{ route('dashboard') }}"
                   class="{{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-300 hover:text-white' }}">
                    Dashboard
                </a>

                <a href="{{ route('tests.index') }}"
                   class="{{ request()->routeIs('tests.*') ? 'text-white' : 'text-slate-300 hover:text-white' }}">
                    Test nou
                </a>

                <a href="{{ route('tests.history') }}"
                   class="{{ request()->routeIs('tests.history') ? 'text-white' : 'text-slate-300 hover:text-white' }}">
                    Istoric
                </a>
            </div>

            {{-- User --}}
            <div class="relative">
                <details class="group">
                    <summary class="flex cursor-pointer items-center gap-2 rounded-xl bg-white/5 px-3 py-2 text-sm text-white ring-1 ring-white/10 hover:bg-white/10">
                        {{ Auth::user()->name ?? 'User' }}
                        <svg class="h-4 w-4 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>

                    <div class="absolute right-0 mt-2 w-40 rounded-xl bg-slate-900 ring-1 ring-white/10 shadow-lg">
                        <a href="{{ route('dashboard') }}"
                           class="block px-4 py-2 text-sm text-slate-300 hover:bg-white/5 hover:text-white">
                            Profil
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-slate-300 hover:bg-white/5 hover:text-white">
                                Logout
                            </button>
                        </form>
                    </div>
                </details>
            </div>

        </div>
    </div>
</nav>
