<!DOCTYPE html>
<html lang="id" data-theme="dark" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Financee — Pencatatan Keuangan')</title>

    <!-- PWA Metadata & Web App Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#070b12">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Financee">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons for clean UI glyphs -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Theme Initializer (Prevent flash of unstyled theme) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('financee-theme') || 
                (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
            document.documentElement.setAttribute('data-theme', savedTheme);
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 dark:bg-[#070b12] dark:text-slate-100 font-sans selection:bg-blue-500 selection:text-white transition-colors duration-300 antialiased">

    <!-- Top App Navigation -->
    <header class="sticky top-0 z-40 w-full bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200/80 dark:border-slate-800/80 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
            <!-- Brand -->
            <a href="{{ route('transactions.index') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 via-blue-500 to-indigo-500 flex items-center justify-center text-white text-lg shadow-md shadow-blue-500/20 ring-2 ring-blue-500/10 group-hover:scale-105 transition-transform duration-200">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div>
                    <div class="font-bold text-base tracking-tight text-slate-900 dark:text-white leading-tight">
                        Financee
                    </div>
                    <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                        Pencatatan Keuangan
                    </div>
                </div>
            </a>

            <!-- Right Actions -->
            <div class="flex items-center gap-2.5">
                
                <!-- Date Pill (Desktop) -->
                <div class="hidden md:flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    <i class="bi bi-calendar3 text-blue-500"></i>
                    <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>

                <!-- PWA Install Button (Dynamic) -->
                <button type="button" id="pwaInstallBtn" title="Install Aplikasi Financee"
                        class="hidden items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200/80 dark:border-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 transition-all cursor-pointer">
                    <i class="bi bi-download text-blue-500"></i>
                    <span class="hidden sm:inline">Install App</span>
                </button>

                <!-- Export Button (Desktop) -->
                <a href="{{ route('report.export', request()->query()) }}" 
                   title="Export ke Excel"
                   class="hidden md:inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-200/80 dark:border-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white transition-all cursor-pointer">
                    <i class="bi bi-file-earmark-spreadsheet text-emerald-500 text-sm"></i>
                    <span>Export</span>
                </a>

                <!-- Theme Switcher -->
                <button type="button" id="themeToggleBtn" aria-label="Ganti Tema"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-slate-200/80 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 hover:text-blue-500 transition-all active:scale-95 cursor-pointer">
                    <i class="bi bi-sun text-base transition-transform duration-200 hover:rotate-45" id="themeIcon"></i>
                </button>

                <!-- User Profile Dropdown (Alpine.js) -->
                @auth
                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" 
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200/80 dark:border-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 transition-all cursor-pointer">
                        <i class="bi bi-person-circle text-blue-500 text-base"></i>
                        <span class="hidden sm:inline font-semibold">{{ Auth::user()->name }}</span>
                        <i class="bi bi-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                    </button>

                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                         class="absolute right-0 mt-2 w-56 rounded-2xl bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800 shadow-xl shadow-slate-900/10 dark:shadow-black/50 p-1.5 z-50 focus:outline-none"
                         style="display: none;">
                        <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-800/80 mb-1">
                            <div class="font-bold text-xs text-slate-900 dark:text-white truncate">{{ Auth::user()->name }}</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ Auth::user()->email }}</div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-full text-left flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors cursor-pointer">
                                <i class="bi bi-box-arrow-right text-sm"></i>
                                <span>Keluar dari Akun</span>
                            </button>
                        </form>
                    </div>
                </div>
                @endauth

                <!-- Tambah Transaksi Button (Desktop) -->
                <button type="button" 
                        data-bs-toggle="modal" data-bs-target="#addModal"
                        class="hidden lg:inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-xs font-semibold shadow-md shadow-blue-500/25 hover:shadow-blue-500/35 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 transition-all duration-200 cursor-pointer">
                    <i class="bi bi-plus-lg text-sm"></i>
                    <span>Catat Transaksi</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Floating Toast Notification System (Fintech Polish) -->
    <div x-data="{
            toasts: [],
            add(message, type = 'success', duration = 3500) {
                const id = Date.now();
                this.toasts.push({ id, message, type });
                setTimeout(() => this.remove(id), duration);
            },
            remove(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }
        }"
        x-init="
            @if (session('success'))
                add('{{ session('success') }}', 'success');
            @endif
            @if (session('error'))
                add('{{ session('error') }}', 'error');
            @endif
            window.addEventListener('financee:toast', (e) => {
                add(e.detail.message, e.detail.type || 'success', e.detail.duration || 3500);
            });
        "
        class="fixed top-5 right-5 z-50 flex flex-col gap-2.5 max-w-sm w-full pointer-events-none px-4 sm:px-0">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                 class="pointer-events-auto p-4 rounded-2xl backdrop-blur-xl border shadow-2xl flex items-center justify-between gap-3 text-xs font-semibold"
                 :class="{
                     'bg-slate-900/95 dark:bg-slate-900/95 border-emerald-500/30 text-emerald-300 shadow-emerald-950/20': toast.type === 'success',
                     'bg-slate-900/95 dark:bg-slate-900/95 border-rose-500/30 text-rose-300 shadow-rose-950/20': toast.type === 'error',
                     'bg-slate-900/95 border-slate-700 text-slate-100': toast.type === 'info'
                 }">
                <div class="flex items-center gap-2.5">
                    <i class="text-base" :class="{
                        'bi bi-check-circle-fill text-emerald-400': toast.type === 'success',
                        'bi bi-exclamation-circle-fill text-rose-400': toast.type === 'error',
                        'bi bi-info-circle-fill text-blue-400': toast.type === 'info'
                    }"></i>
                    <span x-text="toast.message"></span>
                </div>
                <button type="button" @click="remove(toast.id)" class="text-white/60 hover:text-white p-1 cursor-pointer">
                    ✕
                </button>
            </div>
        </template>
    </div>

    <!-- Main Content Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-28 lg:pb-12">
        @yield('content')
    </main>

    <!-- Floating Bottom Navigation for Mobile Devices -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/85 dark:bg-slate-900/85 backdrop-blur-xl border-t border-slate-200/80 dark:border-slate-800/80 px-4 py-2 transition-colors duration-300">
        <div class="max-w-md mx-auto flex items-center justify-between relative">
            
            <!-- Beranda -->
            <a href="{{ route('transactions.index') }}" 
               class="bottom-nav-item flex flex-col items-center gap-1 text-[11px] font-semibold text-blue-600 dark:text-blue-400 transition-colors">
                <i class="bi bi-grid-1x2-fill text-lg"></i>
                <span>Beranda</span>
            </a>

            <!-- Budget -->
            <a href="#budget-section" 
               class="bottom-nav-item flex flex-col items-center gap-1 text-[11px] font-medium text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                <i class="bi bi-pie-chart-fill text-lg"></i>
                <span>Budget</span>
            </a>

            <!-- Center Add FAB Button -->
            <div class="relative -top-5">
                <button type="button" 
                        data-bs-toggle="modal" data-bs-target="#addModal" 
                        aria-label="Catat Transaksi"
                        class="w-13 h-13 rounded-2xl bg-gradient-to-tr from-blue-600 via-blue-500 to-indigo-600 text-white flex items-center justify-center text-xl shadow-xl shadow-blue-500/35 ring-4 ring-white dark:ring-[#070b12] active:scale-90 hover:scale-105 transition-all duration-200 cursor-pointer">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </div>

            <!-- Laporan -->
            <a href="#laporan-section" 
               class="bottom-nav-item flex flex-col items-center gap-1 text-[11px] font-medium text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                <i class="bi bi-graph-up text-lg"></i>
                <span>Laporan</span>
            </a>

            <!-- Export -->
            <a href="{{ route('report.export', request()->query()) }}" 
               class="bottom-nav-item flex flex-col items-center gap-1 text-[11px] font-medium text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                <i class="bi bi-download text-lg"></i>
                <span>Export</span>
            </a>
        </div>
    </nav>

    <!-- Global Client Scripts -->
    <script>
        // Theme switcher logic
        (function() {
            const themeToggleBtn = document.getElementById('themeToggleBtn');
            const themeIcon = document.getElementById('themeIcon');
            const html = document.documentElement;

            function updateIcon(theme) {
                if (theme === 'dark') {
                    themeIcon.className = 'bi bi-sun';
                    themeToggleBtn.setAttribute('title', 'Beralih ke Mode Terang');
                } else {
                    themeIcon.className = 'bi bi-moon-stars';
                    themeToggleBtn.setAttribute('title', 'Beralih ke Mode Gelap');
                }
            }

            const currentTheme = html.getAttribute('data-theme') || 'dark';
            updateIcon(currentTheme);

            themeToggleBtn.addEventListener('click', function() {
                const current = html.getAttribute('data-theme') || 'dark';
                const next = current === 'dark' ? 'light' : 'dark';
                
                html.setAttribute('data-theme', next);
                html.setAttribute('data-bs-theme', next);
                
                if (next === 'dark') {
                    html.classList.add('dark');
                } else {
                    html.classList.remove('dark');
                }

                localStorage.setItem('financee-theme', next);
                updateIcon(next);

                // Dispatch event so Chart.js or other components can react
                window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: next } }));
            });
        })();

        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('PWA Service Worker terdaftar:', reg.scope))
                    .catch((err) => console.log('PWA Service Worker gagal:', err));
            });
        }

        // PWA Install Prompt handling
        let deferredPrompt;
        const pwaInstallBtn = document.getElementById('pwaInstallBtn');
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            if (pwaInstallBtn) {
                pwaInstallBtn.classList.remove('hidden');
                pwaInstallBtn.classList.add('inline-flex');
            }
        });

        if (pwaInstallBtn) {
            pwaInstallBtn.addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    if (outcome === 'accepted') {
                        pwaInstallBtn.classList.add('hidden');
                        pwaInstallBtn.classList.remove('inline-flex');
                    }
                    deferredPrompt = null;
                }
            });
        }

        // Smooth scroll for bottom nav anchor links
        document.querySelectorAll('nav a[href^="#"]').forEach(link => {
            link.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId && targetId.length > 1) {
                    const targetEl = document.querySelector(targetId);
                    if (targetEl) {
                        e.preventDefault();
                        targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
