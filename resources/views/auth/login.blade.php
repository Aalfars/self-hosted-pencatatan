<!DOCTYPE html>
<html lang="id" data-theme="dark" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#070b12">
    <title>Masuk — Financee</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons for clean glyphs -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- PWA Metadata -->
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

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

    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 dark:bg-[#070b12] dark:text-slate-100 flex flex-col justify-center items-center p-4 sm:p-6 relative overflow-hidden font-sans selection:bg-blue-500 selection:text-white transition-colors duration-300">

    <!-- Ambient Glowing Background Elements -->
    <div class="pointer-events-none absolute -top-40 -left-40 w-96 h-96 bg-blue-500/15 dark:bg-blue-600/20 rounded-full blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-500/15 dark:bg-indigo-600/20 rounded-full blur-3xl"></div>
    <div class="pointer-events-none absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-sky-500/5 dark:bg-blue-500/5 rounded-full blur-[100px]"></div>

    <!-- Theme Toggle Floating Button -->
    <div class="fixed top-5 right-5 z-20">
        <button type="button" id="themeToggleBtn" aria-label="Ganti Mode Tema" 
                class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-slate-200/80 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-500/40 dark:hover:border-blue-500/40 shadow-sm hover:shadow transition-all duration-200 active:scale-95 cursor-pointer">
            <i class="bi bi-sun text-lg transition-transform duration-200 hover:rotate-45" id="themeIcon"></i>
        </button>
    </div>

    <!-- Main Auth Card -->
    <main class="w-full max-w-[420px] relative z-10">
        <div class="bg-white/85 dark:bg-slate-900/70 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800/80 rounded-3xl p-7 sm:p-9 shadow-2xl shadow-slate-900/5 dark:shadow-black/50 transition-all duration-300">
            
            <!-- Brand Header -->
            <div class="flex flex-col items-center text-center mb-8">
                <div class="w-13 h-13 rounded-2xl bg-gradient-to-tr from-blue-600 via-blue-500 to-indigo-500 flex items-center justify-center text-white text-2xl shadow-lg shadow-blue-500/25 ring-4 ring-blue-500/10 mb-4 transition-transform hover:scale-105 duration-200">
                    <i class="bi bi-wallet2"></i>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    Masuk ke Financee
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1.5">
                    Kelola pencatatan keuangan pribadi Anda
                </p>
            </div>

            <!-- Flash Notifications -->
            @if (session('success'))
                <div class="p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/25 text-emerald-600 dark:text-emerald-400 text-sm flex items-center gap-2.5 mb-5 animate-in fade-in slide-in-from-top-2 duration-200">
                    <i class="bi bi-check-circle-fill text-emerald-500 text-base shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/25 text-rose-600 dark:text-rose-400 text-sm flex items-center gap-2.5 mb-5 animate-in fade-in slide-in-from-top-2 duration-200">
                    <i class="bi bi-exclamation-circle-fill text-rose-500 text-base shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="emailInput" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-2">
                        Alamat Email
                    </label>
                    <div class="relative flex items-center group">
                        <span class="absolute left-3.5 text-slate-400 group-focus-within:text-blue-500 transition-colors pointer-events-none text-base">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input type="email" name="email" id="emailInput" 
                               value="{{ old('email') }}" required autofocus
                               placeholder="nama@email.com"
                               class="w-full pl-10 pr-4 py-3 bg-slate-100/70 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800/80 focus:bg-white dark:focus:bg-slate-800 border border-slate-200 dark:border-slate-700/80 rounded-xl text-slate-900 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/15 transition-all text-sm font-medium">
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="passwordInput" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                            Kata Sandi
                        </label>
                    </div>
                    <div class="relative flex items-center group">
                        <span class="absolute left-3.5 text-slate-400 group-focus-within:text-blue-500 transition-colors pointer-events-none text-base">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" name="password" id="passwordInput" required
                               placeholder="••••••••"
                               class="w-full pl-10 pr-11 py-3 bg-slate-100/70 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800/80 focus:bg-white dark:focus:bg-slate-800 border border-slate-200 dark:border-slate-700/80 rounded-xl text-slate-900 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/15 transition-all text-sm font-medium">
                        <button type="button" id="togglePasswordBtn" aria-label="Lihat Kata Sandi"
                                class="absolute right-3.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors p-1 cursor-pointer">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2.5 cursor-pointer select-none">
                        <input type="checkbox" name="remember" id="remember" checked
                               class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500/30 bg-slate-100 dark:bg-slate-800 cursor-pointer accent-blue-600">
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">
                            Ingat saya di perangkat ini
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" 
                            class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold text-sm shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2 group cursor-pointer">
                        <span>Masuk Sekarang</span>
                        <i class="bi bi-box-arrow-in-right text-lg transition-transform group-hover:translate-x-0.5"></i>
                    </button>
                </div>
            </form>

            <!-- Footer -->
            <div class="text-center text-sm text-slate-500 dark:text-slate-400 mt-7 pt-5 border-t border-slate-200/70 dark:border-slate-800/80">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 underline decoration-blue-500/40 underline-offset-4 transition-colors">
                    Daftar Akun Baru
                </a>
            </div>
        </div>
    </main>

    <!-- Client Script for Interactive Elements -->
    <script>
        // Password Visibility Toggle
        (function() {
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const passwordInput = document.getElementById('passwordInput');
            const toggleIcon = document.getElementById('togglePasswordIcon');

            if (toggleBtn && passwordInput && toggleIcon) {
                toggleBtn.addEventListener('click', function() {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    toggleIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
                });
            }
        })();

        // Theme Toggle Controller
        (function() {
            const themeToggleBtn = document.getElementById('themeToggleBtn');
            const themeIcon = document.getElementById('themeIcon');
            const html = document.documentElement;

            function updateIcon(theme) {
                if (theme === 'dark') {
                    themeIcon.className = 'bi bi-sun';
                } else {
                    themeIcon.className = 'bi bi-moon-stars';
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
            });
        })();
    </script>
</body>
</html>
