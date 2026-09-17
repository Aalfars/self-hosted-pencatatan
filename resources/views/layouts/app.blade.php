<!DOCTYPE html>
<html lang="id" data-theme="dark" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Financee — Pencatatan Keuangan')</title>

    <!-- PWA Metadata & Web App Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0b0f17">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Financee">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">

    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        // Init theme immediately to prevent flash of wrong theme
        (function() {
            const savedTheme = localStorage.getItem('financee-theme') || 
                (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
            document.documentElement.setAttribute('data-theme', savedTheme);
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <style>
        :root, [data-theme="dark"] {
            --bg-app: #0b0f17;
            --bg-surface: #111827;
            --bg-surface-elevated: #182234;
            --bg-surface-hover: #1f2b42;
            --bg-subtle: #0f172a;
            --border-color: rgba(255, 255, 255, 0.12);
            --border-color-subtle: rgba(255, 255, 255, 0.07);
            --border-color-hover: rgba(255, 255, 255, 0.24);

            /* HIGH CONTRAST TYPOGRAPHY IN DARK MODE */
            --text-primary: #ffffff;          /* Pure crisp white (17:1 contrast) */
            --text-secondary: #f1f5f9;        /* Slate 100 - bright silver (15:1 contrast) */
            --text-muted: #cbd5e1;            /* Slate 300 - clear, sharp secondary text (9.5:1 contrast) */
            --text-faint: #94a3b8;            /* Slate 400 - clean labels (5.5:1 contrast) */

            --brand-primary: #3b82f6;
            --brand-primary-soft: rgba(59, 130, 246, 0.2);
            --brand-primary-hover: #60a5fa;
            --brand-btn-text: #ffffff;

            --income: #34d399;
            --income-soft: rgba(52, 211, 153, 0.16);
            --income-border: rgba(52, 211, 153, 0.35);

            --expense: #fb7185;
            --expense-soft: rgba(251, 113, 133, 0.16);
            --expense-border: rgba(251, 113, 133, 0.35);

            --warning: #fbbf24;
            --warning-soft: rgba(251, 191, 36, 0.16);

            --card-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.6);
            --card-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.4);
            --bottom-nav-bg: rgba(17, 24, 39, 0.95);
            --chart-grid: rgba(255, 255, 255, 0.08);
            --chart-text: #cbd5e1;
        }

        [data-theme="light"] {
            --bg-app: #f8fafc;
            --bg-surface: #ffffff;
            --bg-surface-elevated: #f1f5f9;
            --bg-surface-hover: #e2e8f0;
            --bg-subtle: #f8fafc;
            --border-color: #e2e8f0;
            --border-color-subtle: #edf2f7;
            --border-color-hover: #cbd5e1;

            --text-primary: #0f172a;
            --text-secondary: #334155;
            --text-muted: #64748b;
            --text-faint: #94a3b8;

            --brand-primary: #0f172a;
            --brand-primary-soft: rgba(15, 23, 42, 0.08);
            --brand-primary-hover: #1e293b;
            --brand-btn-text: #ffffff;

            --income: #059669;
            --income-soft: rgba(5, 150, 105, 0.1);
            --income-border: rgba(5, 150, 105, 0.2);

            --expense: #e11d48;
            --expense-soft: rgba(225, 29, 72, 0.1);
            --expense-border: rgba(225, 29, 72, 0.2);

            --warning: #d97706;
            --warning-soft: rgba(217, 119, 6, 0.1);

            --card-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05), 0 4px 6px -2px rgba(15, 23, 42, 0.02);
            --card-shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.06);
            --bottom-nav-bg: rgba(255, 255, 255, 0.94);
            --chart-grid: rgba(0, 0, 0, 0.06);
            --chart-text: #64748b;
        }

        /* High Contrast Utility Overrides for Dark Mode */
        [data-theme="dark"] .text-body,
        [data-bs-theme="dark"] .text-body {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .text-muted,
        [data-bs-theme="dark"] .text-muted {
            color: var(--text-muted) !important;
        }

        [data-theme="dark"] .text-secondary,
        [data-bs-theme="dark"] .text-secondary {
            color: var(--text-muted) !important;
        }

        [data-theme="dark"] .bg-secondary-subtle,
        [data-bs-theme="dark"] .bg-secondary-subtle {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
        }

        [data-theme="dark"] .bg-primary-subtle,
        [data-bs-theme="dark"] .bg-primary-subtle {
            background-color: rgba(59, 130, 246, 0.22) !important;
            color: #bfdbfe !important;
            border: 1px solid rgba(59, 130, 246, 0.35) !important;
        }

        [data-theme="dark"] .bg-success-subtle,
        [data-bs-theme="dark"] .bg-success-subtle {
            background-color: rgba(16, 185, 129, 0.22) !important;
            color: #a7f3d0 !important;
            border: 1px solid rgba(16, 185, 129, 0.35) !important;
        }

        [data-theme="dark"] .bg-danger-subtle,
        [data-bs-theme="dark"] .bg-danger-subtle {
            background-color: rgba(244, 63, 94, 0.22) !important;
            color: #fecdd3 !important;
            border: 1px solid rgba(244, 63, 94, 0.35) !important;
        }

        [data-theme="dark"] .bg-warning-subtle,
        [data-bs-theme="dark"] .bg-warning-subtle {
            background-color: rgba(245, 158, 11, 0.22) !important;
            color: #fde68a !important;
            border: 1px solid rgba(245, 158, 11, 0.35) !important;
        }

        [data-theme="dark"] .form-control::placeholder {
            color: #94a3b8;
            opacity: 0.75;
        }

        * {
            -webkit-tap-highlight-color: transparent;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-app);
            color: var(--text-primary);
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            padding-bottom: 96px;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        @media (min-width: 992px) {
            body {
                padding-bottom: 48px;
            }
        }

        /* Typography & Numerals */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            letter-spacing: -0.025em;
            color: var(--text-primary);
        }

        .tabular-nums {
            font-variant-numeric: tabular-nums;
            font-feature-settings: "tnum" 1;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* Top Navbar */
        .app-navbar {
            background-color: var(--bg-surface);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1020;
            backdrop-filter: blur(12px);
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--brand-primary), #6366f1);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.35);
        }

        .brand-title {
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            margin: 0;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .brand-tag {
            font-size: 0.68rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Action Buttons */
        .btn-theme-toggle {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background: var(--bg-surface);
            color: var(--text-secondary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-theme-toggle:hover {
            background: var(--bg-surface-hover);
            color: var(--text-primary);
            border-color: var(--border-color-hover);
        }

        .btn-primary-action {
            background: var(--brand-primary);
            color: var(--brand-btn-text);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 8px 16px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: var(--card-shadow-sm);
            transition: all 0.15s ease;
        }
        .btn-primary-action:hover {
            background: var(--brand-primary-hover);
            color: var(--brand-btn-text);
            transform: translateY(-1px);
        }

        .btn-ghost-action {
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 8px 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s ease;
        }
        .btn-ghost-action:hover {
            background: var(--bg-surface-hover);
            color: var(--text-primary);
            border-color: var(--border-color-hover);
        }

        /* Base Card Panel */
        .card-panel {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: var(--card-shadow-sm);
            transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        /* Form Controls */
        .form-control, .form-select {
            background-color: var(--bg-surface-elevated);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.9rem;
            padding: 9px 13px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .form-control:focus, .form-select:focus {
            background-color: var(--bg-surface-elevated);
            color: var(--text-primary);
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px var(--brand-primary-soft);
        }
        .form-control::placeholder {
            color: var(--text-muted);
            opacity: 0.8;
        }
        .form-label {
            color: var(--text-secondary);
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
        }

        /* Alerts & Toast */
        .custom-alert {
            background-color: var(--income-soft);
            border: 1px solid var(--income-border);
            color: var(--income);
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Bottom Nav for Mobile */
        .bottom-nav {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--bottom-nav-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 8px 12px max(10px, env(safe-area-inset-bottom));
            z-index: 1030;
            transition: background 0.2s ease, border-color 0.2s ease;
        }
        .bottom-nav-item {
            color: var(--text-muted);
            font-size: 0.68rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 8px;
            transition: color 0.15s ease;
        }
        .bottom-nav-item i {
            font-size: 1.25rem;
        }
        .bottom-nav-item.active, .bottom-nav-item:hover {
            color: var(--text-primary);
        }

        /* Floating Add Button for Mobile */
        .fab-mobile {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--brand-primary);
            color: var(--brand-btn-text);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
            margin-top: -24px;
            transition: transform 0.15s ease;
        }
        .fab-mobile:active {
            transform: scale(0.92);
        }

        @media (min-width: 992px) {
            .bottom-nav {
                display: none !important;
            }
        }

        /* Modals (Clean Desktop, Bottom-sheet on Mobile) */
        .modal-content {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
        }
        .btn-close {
            filter: var(--bs-btn-close-filter, invert(var(--close-invert, 1)));
            opacity: 0.6;
        }
        [data-theme="light"] {
            --close-invert: 0;
        }

        @media (max-width: 991.98px) {
            .modal.sheet-modal .modal-dialog {
                margin: 0;
                display: flex;
                align-items: flex-end;
                min-height: 100%;
                max-width: 100%;
            }
            .modal.sheet-modal .modal-content {
                width: 100%;
                border-radius: 24px 24px 0 0;
                border-bottom: none;
                max-height: 90vh;
                overflow-y: auto;
            }
            .modal.sheet-modal.fade .modal-dialog {
                transform: translateY(100%);
                transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            }
            .modal.sheet-modal.show .modal-dialog {
                transform: translateY(0);
            }
        }

        .sheet-handle {
            width: 36px;
            height: 4px;
            border-radius: 4px;
            background: var(--border-color-hover);
            margin: 10px auto 4px;
        }
        @media (min-width: 992px) {
            .sheet-handle {
                display: none;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: var(--border-color-hover);
            border-radius: 6px;
        }
    </style>
</head>
<body>

    <!-- Top App Navigation -->
    <nav class="app-navbar py-3">
        <div class="container-xl d-flex justify-content-between align-items-center">
            <!-- Brand -->
            <a href="{{ route('transactions.index') }}" class="d-flex align-items-center gap-3">
                <div class="brand-icon">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div>
                    <div class="brand-title">Financee</div>
                    <div class="brand-tag">Pencatatan Keuangan</div>
                </div>
            </a>

            <!-- Right Actions -->
            <div class="d-flex align-items-center gap-2">
                <!-- Date Pill (Desktop) -->
                <div class="d-none d-md-flex align-items-center gap-2 px-3 py-1 rounded-pill"
                     style="background: var(--bg-surface-elevated); border: 1px solid var(--border-color); font-size: 0.8rem; font-weight: 600; color: var(--text-secondary);">
                    <i class="bi bi-calendar3" style="color: var(--brand-primary);"></i>
                    <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>

                <!-- PWA Install Button (Dynamic) -->
                <button type="button" class="btn-ghost-action d-none" id="pwaInstallBtn" title="Install Aplikasi Financee">
                    <i class="bi bi-download"></i>
                    <span class="d-none d-md-inline">Install App</span>
                </button>

                <!-- Export Button (Desktop) -->
                <a href="{{ route('report.export', request()->query()) }}" class="btn-ghost-action d-none d-md-inline-flex" title="Export ke Excel">
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                    <span>Export</span>
                </a>

                <!-- Theme Switcher -->
                <button type="button" class="btn-theme-toggle" id="themeToggleBtn" title="Ganti Mode Gelap / Terang">
                    <i class="bi bi-moon-stars" id="themeIcon"></i>
                </button>

                <!-- User Profile Dropdown -->
                @auth
                <div class="dropdown">
                    <button type="button" class="btn-ghost-action" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 6px 12px;">
                        <i class="bi bi-person-circle" style="color: var(--brand-primary); font-size: 1.1rem;"></i>
                        <span class="d-none d-sm-inline fw-semibold">{{ Auth::user()->name }}</span>
                        <i class="bi bi-chevron-down ms-1" style="font-size: 0.7rem; color: var(--text-muted);"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border: 1px solid var(--border-color); background: var(--bg-surface); border-radius: 14px; min-width: 210px; padding: 8px;">
                        <li class="px-3 py-2 border-bottom" style="border-color: var(--border-color) !important;">
                            <div class="fw-bold small text-body">{{ Auth::user()->name }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">{{ Auth::user()->email }}</div>
                        </li>
                        <li class="pt-1">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger d-flex align-items-center gap-2 rounded-2" style="font-size: 0.85rem;">
                                    <i class="bi bi-box-arrow-right"></i> Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                @endauth

                <!-- Tambah Transaksi Button (Desktop) -->
                <button type="button" class="btn-primary-action d-none d-lg-inline-flex" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="bi bi-plus-lg"></i>
                    <span>Catat Transaksi</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="container-xl pt-4">
        @if (session('success'))
            <div class="alert custom-alert d-flex align-items-center justify-content-between mb-4" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bottom Navigation for Mobile Devices -->
    <nav class="bottom-nav">
        <a href="{{ route('transactions.index') }}" class="bottom-nav-item active">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Beranda</span>
        </a>
        <a href="#budget-section" class="bottom-nav-item">
            <i class="bi bi-pie-chart-fill"></i>
            <span>Budget</span>
        </a>

        <!-- Mobile Center Add Button -->
        <button type="button" class="fab-mobile" data-bs-toggle="modal" data-bs-target="#addModal" aria-label="Tambah Transaksi">
            <i class="bi bi-plus-lg"></i>
        </button>

        <a href="#laporan-section" class="bottom-nav-item">
            <i class="bi bi-graph-up"></i>
            <span>Laporan</span>
        </a>
        <a href="{{ route('report.export', request()->query()) }}" class="bottom-nav-item">
            <i class="bi bi-download"></i>
            <span>Export</span>
        </a>
    </nav>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
                const current = html.getAttribute('data-theme');
                const next = current === 'dark' ? 'light' : 'dark';
                html.setAttribute('data-theme', next);
                html.setAttribute('data-bs-theme', next);
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
                pwaInstallBtn.classList.remove('d-none');
                pwaInstallBtn.classList.add('d-inline-flex');
            }
        });

        if (pwaInstallBtn) {
            pwaInstallBtn.addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    if (outcome === 'accepted') {
                        pwaInstallBtn.classList.add('d-none');
                        pwaInstallBtn.classList.remove('d-inline-flex');
                    }
                    deferredPrompt = null;
                }
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
