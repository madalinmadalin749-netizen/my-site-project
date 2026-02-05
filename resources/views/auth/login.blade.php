<x-layouts.auth>
    <!-- background -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950 via-slate-950 to-black"></div>
        <div class="absolute -top-24 left-1/2 h-[520px] w-[720px] -translate-x-1/2 rounded-full bg-indigo-600/25 blur-3xl"></div>
        <div class="absolute top-[35%] -left-40 h-[520px] w-[520px] rounded-full bg-cyan-500/20 blur-3xl"></div>
        <div class="absolute bottom-[-160px] right-[-160px] h-[560px] w-[560px] rounded-full bg-fuchsia-500/15 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.08]"
             style="background-image: linear-gradient(to right, rgba(255,255,255,.08) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(255,255,255,.08) 1px, transparent 1px);
                    background-size: 64px 64px;"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-black/0 via-black/0 to-black/40"></div>
    </div>

    <div class="mx-auto flex min-h-screen max-w-6xl items-center justify-center px-4 py-12 sm:px-6">
        <div class="w-full max-w-md">
            <!-- brand -->
            <a href="{{ route('landing') }}" class="mb-6 flex items-center gap-2">
                <img src="{{ asset('brand/respins-logo.svg') }}" class="h-9 w-9" alt="respins.ro">
                <span class="text-lg font-bold tracking-tight">
                    respins<span class="text-indigo-300">.ro</span>
                </span>
            </a>

            <div class="rounded-3xl bg-gradient-to-b from-white/10 to-white/5 p-6 ring-1 ring-white/15 backdrop-blur-xl shadow-2xl">
                <h1 class="text-xl font-bold">Autentificare</h1>
                <p class="mt-1 text-sm text-slate-300">Intră în cont ca să continui progresul.</p>

                <x-auth-session-status class="mb-4 mt-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="email" :value="__('Email')" class="text-slate-200" />
                        <x-text-input id="email"
                                      class="mt-1 block w-full rounded-2xl border-white/10 bg-white/5 text-slate-100 placeholder:text-slate-500 focus:border-white/20 focus:ring-white/20"
                                      type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('Parolă')" class="text-slate-200" />
                        <x-text-input id="password"
                                      class="mt-1 block w-full rounded-2xl border-white/10 bg-white/5 text-slate-100 placeholder:text-slate-500 focus:border-white/20 focus:ring-white/20"
                                      type="password" name="password" required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox"
                                   class="rounded border-white/20 bg-white/10 text-white focus:ring-white/20"
                                   name="remember">
                            <span class="ms-2 text-sm text-slate-300">{{ __('Ține-mă minte') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-slate-300 hover:text-white" href="{{ route('password.request') }}">
                                {{ __('Ai uitat parola?') }}
                            </a>
                        @endif
                    </div>

                    <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-slate-950 hover:bg-slate-100">
                        Login
                    </button>

                    <p class="text-center text-sm text-slate-300">
                        Nu ai cont?
                        <a href="{{ route('register') }}" class="font-semibold text-white hover:underline">
                            Creează cont
                        </a>
                    </p>
                </form>
            </div>

            <p class="mt-6 text-center text-xs text-slate-500">
                © {{ date('Y') }} respins.ro
            </p>
        </div>
    </div>
</x-layouts.auth>
