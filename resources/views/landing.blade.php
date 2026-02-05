<!doctype html>
<html lang="ro" class="scroll-smooth">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>respins.ro — Chestionare inteligente</title>
<meta name="description" content="respins.ro — chestionare inteligente, teste randomizate, istoric și recomandări pentru progres real.">

<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="alternate icon" href="{{ asset('favicon.ico') }}">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<meta name="theme-color" content="#020617">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <!-- Background -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950 via-slate-950 to-black"></div>

        <div class="absolute -top-24 left-1/2 h-[500px] w-[700px] -translate-x-1/2 rounded-full bg-indigo-600/25 blur-3xl"></div>
        <div class="absolute top-[35%] -left-40 h-[520px] w-[520px] rounded-full bg-cyan-500/20 blur-3xl"></div>
        <div class="absolute bottom-[-160px] right-[-160px] h-[560px] w-[560px] rounded-full bg-fuchsia-500/15 blur-3xl"></div>

        <div class="absolute inset-0 opacity-[0.08]"
             style="background-image: linear-gradient(to right, rgba(255,255,255,.08) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(255,255,255,.08) 1px, transparent 1px);
                    background-size: 64px 64px;"></div>

        <div class="absolute inset-0 bg-gradient-to-b from-black/0 via-black/0 to-black/40"></div>
    </div>

    <!-- Header (sticky) -->
    <header class="sticky top-0 z-50 border-b border-white/10 bg-slate-950/60 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6">
            <a href="/" class="flex items-center gap-2">
    <img
        src="{{ asset('brand/respins-logo.svg') }}"
        alt="respins.ro"
        class="h-8 w-8"
    >

    <span class="text-lg font-bold tracking-tight text-white">
        respins<span class="text-indigo-300">.ro</span>
    </span>
</a>



            <!-- Desktop nav -->
            <nav class="hidden items-center gap-6 md:flex">
                <a href="#cum-functioneaza" class="text-sm text-slate-300 hover:text-white">Cum funcționează</a>
                <a href="#avantaje" class="text-sm text-slate-300 hover:text-white">Avantaje</a>
                <a href="#pret" class="text-sm text-slate-300 hover:text-white">Preț</a>
            </nav>

            <div class="flex items-center gap-2">
                <!-- Mobile menu button -->
                <button
                    id="mobileMenuBtn"
                    class="md:hidden rounded-xl bg-white/10 px-3 py-2 text-sm font-semibold ring-1 ring-white/15 hover:bg-white/15"
                    type="button"
                >
                    Meniu
                </button>

                @auth
                    <a href="{{ route('dashboard') }}"
                       class="rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold ring-1 ring-white/15 hover:bg-white/15">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold ring-1 ring-white/15 hover:bg-white/15">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-slate-100">
                        Începe gratuit
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Mobile nav -->
    <div id="mobileMenu" class="md:hidden hidden border-b border-white/10 bg-slate-950/80 backdrop-blur">
        <div class="mx-auto max-w-6xl px-4 py-3 sm:px-6 flex flex-col gap-2">
            <a href="#cum-functioneaza" class="rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-white/10">Cum funcționează</a>
            <a href="#avantaje" class="rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-white/10">Avantaje</a>
            <a href="#pret" class="rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-white/10">Preț</a>
        </div>
    </div>

    <script>
        const btn = document.getElementById('mobileMenuBtn');
        const menu = document.getElementById('mobileMenu');

        if (btn && menu) {
            btn.addEventListener('click', () => {
                menu.classList.toggle('hidden');
            });

            menu.querySelectorAll('a').forEach(a => {
                a.addEventListener('click', () => menu.classList.add('hidden'));
            });
        }
    </script>

    <!-- Main -->
    <main class="mx-auto max-w-6xl px-4 pb-20 pt-6 sm:px-6">
        <!-- Hero -->
        <section class="grid items-center gap-10 md:grid-cols-2">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-white/5 px-3 py-1 text-xs ring-1 ring-white/10">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    AI + chestionare • progres real, fără timp pierdut
                </div>

                <h1 class="mt-5 text-4xl font-bold tracking-tight sm:text-5xl">
                    Înveți inteligent pentru examen —
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-300 to-indigo-300">
                        nu la întâmplare
                    </span>
                </h1>

                <p class="mt-4 text-base leading-relaxed text-slate-300">
                    respins.ro îți generează teste din categorii, îți urmărește istoricul și îți arată exact
                    unde greșești. Mai puține ore, rezultate mai bune.
                </p>

                <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-950 hover:bg-slate-100">
                        Creează cont
                    </a>
                    <a href="#cum-functioneaza"
                       class="inline-flex items-center justify-center rounded-2xl bg-white/10 px-5 py-3 text-sm font-semibold ring-1 ring-white/15 hover:bg-white/15">
                        Vezi cum funcționează
                    </a>
                </div>

                <!-- Stats -->
                <div class="mt-8 grid grid-cols-3 gap-3">
                    <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10 backdrop-blur">
                        <div class="text-2xl font-bold">+1.000</div>
                        <div class="mt-1 text-xs text-slate-300">întrebări (import CSV)</div>
                    </div>
                    <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10 backdrop-blur">
                        <div class="text-2xl font-bold">AI</div>
                        <div class="mt-1 text-xs text-slate-300">analiză greșeli</div>
                    </div>
                    <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10 backdrop-blur">
                        <div class="text-2xl font-bold">Progres</div>
                        <div class="mt-1 text-xs text-slate-300">istoric & statistici</div>
                    </div>
                </div>
            </div>

            <!-- Right card preview -->
            <div class="relative">
                <div class="absolute -inset-2 rounded-[28px] bg-gradient-to-r from-cyan-500/30 to-indigo-500/30 blur-xl"></div>

                <div class="relative overflow-hidden rounded-[28px] bg-white/5 ring-1 ring-white/10 backdrop-blur">
                    <div class="border-b border-white/10 p-4">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-semibold">Preview Dashboard</div>
                            <div class="text-xs text-slate-300">respins.ro</div>
                        </div>
                    </div>

                    <div class="p-4">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                                <div class="text-xs text-slate-300">Teste finalizate</div>
                                <div class="mt-2 text-2xl font-bold">24</div>
                                <div class="mt-2 h-2 w-full rounded-full bg-white/10">
                                    <div class="h-2 w-2/3 rounded-full bg-white/40"></div>
                                </div>
                            </div>

                            <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                                <div class="text-xs text-slate-300">Best scor</div>
                                <div class="mt-2 text-2xl font-bold">86%</div>
                                <div class="mt-2 text-xs text-slate-400">+8% față de săptămâna trecută</div>
                            </div>

                            <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10 sm:col-span-2">
                                <div class="flex items-center justify-between">
                                    <div class="text-xs text-slate-300">Greșeli frecvente</div>
                                    <div class="text-xs text-slate-400">Top 3</div>
                                </div>
                                <div class="mt-3 space-y-2">
                                    <div class="flex items-center justify-between rounded-xl bg-white/5 px-3 py-2 ring-1 ring-white/10">
                                        <span class="text-sm">Prioritate / Semnale</span>
                                        <span class="text-xs text-slate-300">12 greșeli</span>
                                    </div>
                                    <div class="flex items-center justify-between rounded-xl bg-white/5 px-3 py-2 ring-1 ring-white/10">
                                        <span class="text-sm">Reguli depășire</span>
                                        <span class="text-xs text-slate-300">9 greșeli</span>
                                    </div>
                                    <div class="flex items-center justify-between rounded-xl bg-white/5 px-3 py-2 ring-1 ring-white/10">
                                        <span class="text-sm">Oprire / Staționare</span>
                                        <span class="text-xs text-slate-300">7 greșeli</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                            <div class="text-xs text-slate-300">Recomandare AI</div>
                            <div class="mt-2 text-sm text-slate-200">
                                Repetă capitolul „Prioritate” și refă un test de 20 întrebări.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sections -->
        <section id="cum-functioneaza" class="mt-16 scroll-mt-24">
            <h2 class="text-2xl font-bold">Cum funcționează</h2>
            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur">
                    <div class="text-sm font-semibold">1) Alegi categoria</div>
                    <p class="mt-2 text-sm text-slate-300">Semne, legislație, situații, etc.</p>
                </div>
                <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur">
                    <div class="text-sm font-semibold">2) Dai test</div>
                    <p class="mt-2 text-sm text-slate-300">Randomizat, cu număr de întrebări ales de tine.</p>
                </div>
                <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur">
                    <div class="text-sm font-semibold">3) Vezi progresul</div>
                    <p class="mt-2 text-sm text-slate-300">Istoric + statistici, ai clar ce trebuie repetat.</p>
                </div>
            </div>
        </section>

        <section id="avantaje" class="mt-16 scroll-mt-24">
            <h2 class="text-2xl font-bold">Avantaje</h2>
            <p class="mt-2 max-w-2xl text-sm text-slate-300">
                Tot ce îți trebuie ca să înveți rapid, fără haos: teste, progres, și un UI care te ajută — nu te încurcă.
            </p>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/10 ring-1 ring-emerald-400/20">⚡</span>
                        <div class="text-sm font-semibold">Rapid & clar</div>
                    </div>
                    <p class="mt-3 text-sm text-slate-300">Fără pagini aglomerate. În 2 click-uri începi un test.</p>
                </div>

                <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-500/10 ring-1 ring-cyan-400/20">🎯</span>
                        <div class="text-sm font-semibold">Repeti ce greșești</div>
                    </div>
                    <p class="mt-3 text-sm text-slate-300">Vezi exact unde pierzi puncte și revii țintit pe capitole.</p>
                </div>

                <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-fuchsia-500/10 ring-1 ring-fuchsia-400/20">📈</span>
                        <div class="text-sm font-semibold">Statistici reale</div>
                    </div>
                    <p class="mt-3 text-sm text-slate-300">Best %, medie, progres în timp — ca să știi că avansezi.</p>
                </div>

                <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/10 ring-1 ring-amber-400/20">🧠</span>
                        <div class="text-sm font-semibold">Recomandări (AI-ready)</div>
                    </div>
                    <p class="mt-3 text-sm text-slate-300">Pe baza istoricului, aplicația îți sugerează ce să repeți.</p>
                </div>

                <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-500/10 ring-1 ring-white/10">🗂️</span>
                        <div class="text-sm font-semibold">Admin super simplu</div>
                    </div>
                    <p class="mt-3 text-sm text-slate-300">Import din CSV și gestionezi mii de întrebări fără bătăi de cap.</p>
                </div>

                <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-500/10 ring-1 ring-indigo-400/20">🔒</span>
                        <div class="text-sm font-semibold">Cont & istoric</div>
                    </div>
                    <p class="mt-3 text-sm text-slate-300">Tot progresul tău rămâne salvat: teste, rezultate, evoluție.</p>
                </div>
            </div>
        </section>

<section id="testimoniale" class="mt-16 scroll-mt-24">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold">Rezultate reale</h2>
            <p class="mt-2 text-sm text-slate-300">Câteva exemple de progres după folosirea respins.ro.</p>
        </div>
    </div>

    <div class="mt-6 grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur">
            <div class="text-sm font-semibold">Andrei • Categoria B</div>
            <p class="mt-2 text-sm text-slate-300">
                „Am scăzut greșelile la prioritate în 3 zile. Mi-a arătat exact ce să repet.”
            </p>
            <div class="mt-4 text-xs text-slate-400">+18% la scorul mediu</div>
        </div>

        <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur">
            <div class="text-sm font-semibold">Maria • Teorie</div>
            <p class="mt-2 text-sm text-slate-300">
                „Istoricul + statistici sunt aur. Nu mai învăț haotic, merg pe capitole.”
            </p>
            <div class="mt-4 text-xs text-slate-400">7 zile consecvente</div>
        </div>

        <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur">
            <div class="text-sm font-semibold">Radu • Poliție</div>
            <p class="mt-2 text-sm text-slate-300">
                „Recomandările AI mi-au economisit timp. Teste scurte, repetări țintite.”
            </p>
            <div class="mt-4 text-xs text-slate-400">-40% greșeli repetitive</div>
        </div>
    </div>
</section>

<section id="faq" class="mt-16 scroll-mt-24">
    <h2 class="text-2xl font-bold">Întrebări frecvente</h2>

    <div class="mt-6 grid gap-4 md:grid-cols-2">
        <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur">
            <div class="text-sm font-semibold">Pot folosi gratis?</div>
            <p class="mt-2 text-sm text-slate-300">
                Da. Ai plan gratuit ca să începi imediat. Upgrade-ul e opțional.
            </p>
        </div>

        <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur">
            <div class="text-sm font-semibold">Cum sunt întrebările?</div>
            <p class="mt-2 text-sm text-slate-300">
                Le poți importa din CSV și le organizezi pe categorii. Testele sunt randomizate.
            </p>
        </div>

        <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur">
            <div class="text-sm font-semibold">Ce înseamnă „AI” aici?</div>
            <p class="mt-2 text-sm text-slate-300">
                Analiză a greșelilor + recomandări despre ce să repeți, bazat pe istoricul tău.
            </p>
        </div>

        <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur">
            <div class="text-sm font-semibold">Pot anula oricând?</div>
            <p class="mt-2 text-sm text-slate-300">
                Da. Nu te blochează nimeni. Poți schimba planul când vrei.
            </p>
        </div>
    </div>
</section>

        <section id="pret" class="mt-16 scroll-mt-24">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold">Planuri</h2>
                    <p class="mt-2 text-sm text-slate-300">Alege varianta potrivită pentru ritmul tău de învățare.</p>
                </div>
                <div class="hidden md:block text-xs text-slate-400">
                    Poți schimba planul oricând
                </div>
            </div>

            <div class="mt-6 grid gap-4 lg:grid-cols-3">
                <!-- Gratis -->
                <div class="rounded-3xl bg-white/5 p-6 ring-1 ring-white/10 backdrop-blur">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-semibold">Gratis</div>
                        <div class="text-xs text-slate-400">Pentru început</div>
                    </div>

                    <div class="mt-4 flex items-baseline gap-2">
                        <div class="text-3xl font-bold">0</div>
                        <div class="text-sm text-slate-300">lei / lună</div>
                    </div>

                    <ul class="mt-5 space-y-2 text-sm text-slate-300">
                        <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-emerald-400"></span>Teste standard</li>
                        <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-emerald-400"></span>Istoric de bază</li>
                        <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-emerald-400"></span>Acces la categorii</li>
                    </ul>

                    <a href="{{ route('register') }}"
                       class="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-white/10 px-4 py-3 text-sm font-semibold ring-1 ring-white/15 hover:bg-white/15">
                        Începe gratis
                    </a>
                </div>

                <!-- Pro (Recomandat) -->
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-white/10 to-white/5 p-6 ring-1 ring-white/15 backdrop-blur">
                    <div class="absolute -top-20 left-1/2 h-56 w-56 -translate-x-1/2 rounded-full bg-cyan-500/20 blur-3xl"></div>

                    <div class="relative flex items-center justify-between">
                        <div class="text-sm font-semibold">Pro</div>
                        <div class="rounded-full bg-white/10 px-3 py-1 text-xs ring-1 ring-white/15">
                            Recomandat
                        </div>
                    </div>

                    <div class="relative mt-4 flex items-baseline gap-2">
                        <div class="text-3xl font-bold">29</div>
                        <div class="text-sm text-slate-300">lei / lună</div>
                    </div>

                    <ul class="relative mt-5 space-y-2 text-sm text-slate-200">
                        <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-cyan-300"></span>Teste nelimitate</li>
                        <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-cyan-300"></span>Statistici avansate</li>
                        <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-cyan-300"></span>Recomandări AI (greșeli frecvente)</li>
                        <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-cyan-300"></span>Progres pe capitole</li>
                    </ul>

                    <a href="{{ route('register') }}"
                       class="relative mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-slate-950 hover:bg-slate-100">
                        Alege Pro
                    </a>
                </div>

                <!-- Premium -->
                <div class="rounded-3xl bg-white/5 p-6 ring-1 ring-white/10 backdrop-blur">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-semibold">Premium</div>
                        <div class="text-xs text-slate-400">Serios pe rezultat</div>
                    </div>

                    <div class="mt-4 flex items-baseline gap-2">
                        <div class="text-3xl font-bold">59</div>
                        <div class="text-sm text-slate-300">lei / lună</div>
                    </div>

                    <ul class="mt-5 space-y-2 text-sm text-slate-300">
                        <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-fuchsia-300"></span>Tot din Pro</li>
                        <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-fuchsia-300"></span>Plan de învățare AI</li>
                        <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-fuchsia-300"></span>Teste “weakness mode”</li>
                        <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-fuchsia-300"></span>Prioritate la feature-uri noi</li>
                    </ul>

                    <a href="{{ route('register') }}"
                       class="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-white/10 px-4 py-3 text-sm font-semibold ring-1 ring-white/15 hover:bg-white/15">
                        Alege Premium
                    </a>
                </div>
            </div>

            <p class="mt-4 text-xs text-slate-400">
                * Prețurile sunt demo acum — le conectăm la Stripe mai târziu (când vrei tu).
            </p>

            <div class="mt-8 rounded-3xl bg-gradient-to-r from-white/10 to-white/5 p-6 ring-1 ring-white/10 backdrop-blur">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-xl font-bold">Ești gata să începi?</h3>
                        <p class="mt-1 text-sm text-slate-300">Creează cont și începe cu un test de 20 întrebări.</p>
                    </div>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-950 hover:bg-slate-100">
                        Creează cont
                    </a>
                </div>
            </div>
        </section>
    </main>

    <footer class="mx-auto max-w-6xl px-4 pb-10 text-xs text-slate-400 sm:px-6">
        © {{ date('Y') }} respins.ro • Toate drepturile rezervate
    </footer>
</body>
</html>
