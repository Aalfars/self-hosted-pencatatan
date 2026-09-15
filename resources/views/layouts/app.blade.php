<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Finance App')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #14121b;
            --bg-elevated: #1b1826;
            --panel: #1c1926;
            --panel-2: #221f30;
            --border: #2e2a3d;
            --text: #f2eef8;
            --text-muted: #96909f;
            --text-faint: #726c7c;

            --accent: #ffb15e;
            --accent-ink: #2a1a06;
            --accent-soft: rgba(255, 177, 94, .16);

            --income: #7ee8b0;
            --income-soft: rgba(126, 232, 176, .14);
            --expense: #ff8b94;
            --expense-soft: rgba(255, 139, 148, .14);
            --lavender: #c9aeff;
            --lavender-soft: rgba(201, 174, 255, .16);

            --radius-lg: 26px;
            --radius-md: 18px;
            --radius-sm: 12px;
        }

        * { -webkit-tap-highlight-color: transparent; }

        body {
            background-color: var(--bg);
            background-image: radial-gradient(circle at 15% 0%, rgba(255,177,94,0.07), transparent 45%),
                               radial-gradient(circle at 85% 15%, rgba(201,174,255,0.06), transparent 40%);
            background-attachment: fixed;
            color: var(--text);
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            padding-bottom: 92px;
        }

        h1, h2, h3, h4, h5, h6 { font-weight: 800; letter-spacing: -0.02em; }

        ::-webkit-scrollbar { height: 0; width: 8px; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 8px; }

        a { text-decoration: none; }

        /* ============ TOP HEADER ============ */
        .app-header {
            padding: 22px 0 6px;
        }
        .app-header .greeting-eyebrow {
            color: var(--text-faint);
            font-size: .82rem;
            font-weight: 500;
        }
        .app-header .greeting-title {
            font-size: 1.35rem;
            font-weight: 800;
        }
        .app-header .greeting-title .wave { display: inline-block; }

        /* ============ PANELS ============ */
        .card-panel {
            background-color: var(--panel);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
        }

        /* ============ FORMS ============ */
        .form-control, .form-select {
            background-color: var(--bg-elevated);
            border: 1px solid var(--border);
            color: var(--text);
            border-radius: var(--radius-sm);
            font-family: inherit;
        }
        .form-control:focus, .form-select:focus {
            background-color: var(--bg-elevated);
            color: var(--text);
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-soft);
        }
        .form-control::placeholder { color: var(--text-faint); }
        .form-label { color: var(--text-muted); font-size: 0.83rem; font-weight: 600; margin-bottom: .4rem; }

        .btn-accent {
            background-color: var(--accent);
            border-color: var(--accent);
            color: var(--accent-ink);
            font-weight: 700;
            border-radius: 999px;
        }
        .btn-accent:hover { background-color: #ffbf7a; border-color: #ffbf7a; color: var(--accent-ink); }

        .btn-outline-info {
            border-color: var(--border);
            color: var(--text-muted);
            border-radius: 999px;
        }
        .btn-outline-info:hover { background-color: var(--panel-2); border-color: var(--border); color: var(--text); }

        .text-muted-soft { color: var(--text-muted) !important; }

        /* ============ MODALS (bottom-sheet on mobile) ============ */
        .modal-content {
            background-color: var(--panel);
            border: 1px solid var(--border);
            color: var(--text);
            border-radius: var(--radius-lg);
        }
        .btn-close { filter: invert(1); opacity: .6; }

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
                border-radius: 26px 26px 0 0;
                border-bottom: none;
                max-height: 88vh;
                overflow-y: auto;
            }
            .modal.sheet-modal.fade .modal-dialog { transform: translateY(40px); }
            .modal.sheet-modal.show .modal-dialog { transform: translateY(0); }
        }

        .sheet-handle {
            width: 40px; height: 4px; border-radius: 4px;
            background: var(--border); margin: 10px auto 2px;
        }
        @media (min-width: 992px) { .sheet-handle { display: none; } }

        /* ============ FAB ============ */
        .fab-add {
            position: fixed;
            right: 22px;
            bottom: 92px;
            width: 58px; height: 58px;
            border-radius: 50%;
            background: var(--accent);
            color: var(--accent-ink);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 10px 30px -6px rgba(255,177,94,.55);
            border: none;
            z-index: 1040;
            transition: transform .15s ease;
        }
        .fab-add:active { transform: scale(0.93); }
        @media (min-width: 992px) { .fab-add { bottom: 32px; } }

        /* ============ BOTTOM NAV (mobile only) ============ */
        .bottom-nav {
            position: fixed; left: 0; right: 0; bottom: 0;
            background: rgba(27, 24, 38, 0.9);
            backdrop-filter: blur(14px);
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-around;
            padding: 10px 0 max(10px, env(safe-area-inset-bottom));
            z-index: 1030;
        }
        .bottom-nav a {
            color: var(--text-faint);
            font-size: 0.68rem;
            display: flex; flex-direction: column; align-items: center; gap: 3px;
            font-weight: 600;
        }
        .bottom-nav a i { font-size: 1.25rem; }
        .bottom-nav a.active { color: var(--accent); }
        @media (min-width: 992px) { .bottom-nav { display: none; } body { padding-bottom: 40px; } }

        /* ============ MISC ============ */
        .badge-category { background-color: var(--lavender-soft); color: var(--lavender); border: 1px solid rgba(201,174,255,.3); font-weight: 600; }
        .badge-pemasukan { background-color: var(--income-soft); color: var(--income); border: 1px solid rgba(126,232,176,.3); }
        .badge-pengeluaran { background-color: var(--expense-soft); color: var(--expense); border: 1px solid rgba(255,139,148,.3); }

        .pagination .page-link { background-color: var(--panel); border-color: var(--border); color: var(--text); border-radius: 10px; margin: 0 2px; }
        .pagination .page-item.active .page-link { background-color: var(--accent); border-color: var(--accent); color: var(--accent-ink); }

        .alert-success.custom-alert {
            background-color: var(--income-soft);
            border: 1px solid rgba(126,232,176,.3);
            color: var(--income);
            border-radius: var(--radius-sm);
        }
    </style>
</head>
<body>

<div class="container" style="max-width: 720px;">
    <header class="app-header d-flex justify-content-between align-items-center">
        <div>
            <div class="greeting-eyebrow">{{ now()->translatedFormat('l, d F Y') }}</div>
            <div class="greeting-title">Halo! <span class="wave">👋</span></div>
        </div>
        <a href="{{ route('transactions.index') }}" class="text-decoration-none">
            <div class="d-flex align-items-center justify-content-center rounded-circle"
                 style="width:44px;height:44px;background:var(--panel);border:1px solid var(--border);">
                <i class="bi bi-wallet2" style="color:var(--accent);font-size:1.1rem;"></i>
            </div>
        </a>
    </header>

    @if (session('success'))
        <div class="alert custom-alert mt-3" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif

    @yield('content')
</div>

<nav class="bottom-nav">
    <a href="{{ route('transactions.index') }}" class="active">
        <i class="bi bi-house-door-fill"></i> Beranda
    </a>
    <a href="#laporan-section">
        <i class="bi bi-pie-chart-fill"></i> Laporan
    </a>
    <button class="fab-add" data-bs-toggle="modal" data-bs-target="#addModal" style="position: static; width: 52px; height: 52px; box-shadow: 0 6px 18px -4px rgba(255,177,94,.55); margin-top: -30px;">
        <i class="bi bi-plus-lg"></i>
    </button>
    <a href="#budget-section">
        <i class="bi bi-bullseye"></i> Budget
    </a>
    <a href="{{ route('report.export', request()->query()) }}">
        <i class="bi bi-download"></i> Export
    </a>
</nav>

<button class="fab-add d-none d-lg-flex" data-bs-toggle="modal" data-bs-target="#addModal">
    <i class="bi bi-plus-lg"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
