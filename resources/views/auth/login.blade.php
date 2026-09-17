<!DOCTYPE html>
<html lang="id" data-theme="dark" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0b0f17">
    <title>Masuk — Financee</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    <script>
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
            --border-color: rgba(255, 255, 255, 0.12);
            --border-color-hover: rgba(255, 255, 255, 0.24);
            --text-primary: #ffffff;
            --text-secondary: #f1f5f9;
            --text-muted: #cbd5e1;
            --brand-primary: #3b82f6;
            --brand-primary-hover: #60a5fa;
            --brand-primary-soft: rgba(59, 130, 246, 0.2);
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.5);
        }

        [data-theme="dark"] .text-muted {
            color: #cbd5e1 !important;
        }
        [data-theme="dark"] .text-body {
            color: #ffffff !important;
        }
        [data-theme="dark"] .admin-hint {
            color: #e2e8f0;
        }

        [data-theme="light"] {
            --bg-app: #f8fafc;
            --bg-surface: #ffffff;
            --bg-surface-elevated: #f1f5f9;
            --bg-surface-hover: #e2e8f0;
            --border-color: #e2e8f0;
            --border-color-hover: #cbd5e1;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --brand-primary: #2563eb;
            --brand-primary-hover: #1d4ed8;
            --brand-primary-soft: rgba(37, 99, 235, 0.1);
            --card-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.06), 0 4px 6px -2px rgba(15, 23, 42, 0.03);
        }

        * { box-sizing: border-box; }

        body {
            background-color: var(--bg-app);
            color: var(--text-primary);
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 24px 16px;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 32px 28px;
            box-shadow: var(--card-shadow);
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--brand-primary), #6366f1);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
            margin-bottom: 12px;
        }

        .form-control {
            background-color: var(--bg-surface-elevated);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.92rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .form-control:focus {
            background-color: var(--bg-surface-elevated);
            color: var(--text-primary);
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px var(--brand-primary-soft);
        }

        .form-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 0.35rem;
        }

        .btn-submit {
            background: var(--brand-primary);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 11px;
            font-weight: 600;
            font-size: 0.92rem;
            width: 100%;
            transition: all 0.15s ease;
        }
        .btn-submit:hover {
            background: var(--brand-primary-hover);
            color: #ffffff;
            transform: translateY(-1px);
        }

        .admin-hint {
            background: var(--bg-surface-elevated);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 0.78rem;
            color: var(--text-secondary);
        }

        .btn-theme-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
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
        }
    </style>
</head>
<body>

    <button type="button" class="btn-theme-toggle" id="themeToggleBtn" title="Ganti Mode Gelap / Terang">
        <i class="bi bi-moon-stars" id="themeIcon"></i>
    </button>

    <div class="auth-card">
        <div class="d-flex flex-column align-items-center text-center mb-4">
            <div class="brand-icon">
                <i class="bi bi-wallet2"></i>
            </div>
            <h4 class="fw-bold mb-1">Masuk ke Financee</h4>
            <p class="small text-muted mb-0">Catat dan pantau keuangan pribadi Anda</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success py-2 px-3 small rounded-3 mb-3">
                <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3">
                <i class="bi bi-exclamation-circle me-1"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent text-muted" style="border-color: var(--border-color);"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" id="emailInput" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label">Kata Sandi</label>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-transparent text-muted" style="border-color: var(--border-color);"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="passwordInput" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                    <label class="form-check-label small text-muted" for="remember">
                        Ingat saya
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-submit mb-3">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
            </button>
        </form>

        <!-- <div class="admin-hint mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="fw-bold"><i class="bi bi-shield-lock me-1 text-primary"></i> Akun Utama (Data Lama)</span>
                <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none" style="font-size: 0.75rem;" onclick="fillAdmin()">Gunakan</button>
            </div>
            <div>Email: <code class="text-body">admin@financee.local</code></div>
            <div>Password: <code class="text-body">admin123</code></div>
        </div> -->

        <div class="text-center small text-muted">
            Belum punya akun? <a href="{{ route('register') }}" class="fw-semibold text-primary text-decoration-none">Daftar Akun Baru</a>
        </div>
    </div>

    <script>
        function fillAdmin() {
            document.getElementById('emailInput').value = 'admin@financee.local';
            document.getElementById('passwordInput').value = 'admin123';
        }

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
                const current = html.getAttribute('data-theme');
                const next = current === 'dark' ? 'light' : 'dark';
                html.setAttribute('data-theme', next);
                html.setAttribute('data-bs-theme', next);
                localStorage.setItem('financee-theme', next);
                updateIcon(next);
            });
        })();
    </script>
</body>
</html>
