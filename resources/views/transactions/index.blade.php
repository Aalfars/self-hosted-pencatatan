@extends('layouts.app')

@section('title', 'Dashboard Keuangan — Financee')

@php
    if (!function_exists('categoryMeta')) {
        function categoryMeta($cat) {
            $map = [
                'Makan'        => ['icon' => 'bi-cup-hot-fill',     'color' => 'amber'],
                'Jajan'        => ['icon' => 'bi-cup-straw',        'color' => 'orange'],
                'Bensin'       => ['icon' => 'bi-fuel-pump-fill',   'color' => 'blue'],
                'Kewajiban'    => ['icon' => 'bi-shield-check',     'color' => 'indigo'],
                'Donasi/Amal'  => ['icon' => 'bi-heart-fill',       'color' => 'rose'],
                'Beli Barang'  => ['icon' => 'bi-bag-fill',         'color' => 'purple'],
                'Transportasi' => ['icon' => 'bi-car-front-fill',   'color' => 'cyan'],
                'Tagihan'      => ['icon' => 'bi-receipt-cutoff',   'color' => 'violet'],
                'Kesehatan'    => ['icon' => 'bi-heart-pulse-fill', 'color' => 'emerald'],
                'Hiburan'      => ['icon' => 'bi-controller',       'color' => 'fuchsia'],
            ];
            return $map[$cat] ?? ['icon' => 'bi-tag-fill', 'color' => 'slate'];
        }
    }
    $saldo = $totalPemasukan - $totalPengeluaran;
    $totalBudgetSpent = $budgets->sum('spent');
    $totalBudgetLimit = $budgets->sum('amount');
    $budgetOverallPercent = $totalBudgetLimit > 0 ? round(($totalBudgetSpent / $totalBudgetLimit) * 100) : 0;
@endphp

@section('content')

<style>
    /* Category Badges & Squircles */
    .icon-squircle {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .icon-squircle-sm {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }

    /* Color Tokens for Categories with High Contrast */
    .cat-amber   { background: rgba(245, 158, 11, 0.2); color: #fbbf24; }
    .cat-orange  { background: rgba(249, 115, 22, 0.2); color: #fb923c; }
    .cat-blue    { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }
    .cat-indigo  { background: rgba(99, 102, 241, 0.2); color: #818cf8; }
    .cat-rose    { background: rgba(244, 63, 94, 0.2); color: #fb7185; }
    .cat-purple  { background: rgba(168, 85, 247, 0.2); color: #c084fc; }
    .cat-cyan    { background: rgba(6, 182, 212, 0.2); color: #22d3ee; }
    .cat-violet  { background: rgba(139, 92, 246, 0.2); color: #a78bfa; }
    .cat-emerald { background: rgba(16, 185, 129, 0.2); color: #34d399; }
    .cat-fuchsia { background: rgba(217, 70, 239, 0.2); color: #e879f9; }
    .cat-slate   { background: rgba(255, 255, 255, 0.14); color: #ffffff; }

    /* Hero Overview Card */
    .hero-overview {
        background-color: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 24px;
        box-shadow: var(--card-shadow-sm);
        position: relative;
        overflow: hidden;
    }
    .hero-eyebrow {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-muted);
    }
    .hero-balance-val {
        font-size: 2.25rem;
        font-weight: 800;
        letter-spacing: -0.035em;
        color: var(--text-primary);
        line-height: 1.15;
        margin: 6px 0 16px;
    }
    @media (max-width: 576px) {
        .hero-overview {
            padding: 18px 16px;
            border-radius: 16px;
        }
        .hero-balance-val {
            font-size: 1.7rem;
            margin: 4px 0 12px;
        }
    }

    .stat-pill {
        background: var(--bg-surface-elevated);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        min-width: 0;
    }
    @media (max-width: 576px) {
        .stat-pill {
            padding: 10px 10px;
            gap: 8px;
            border-radius: 12px;
        }
    }
    .stat-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    @media (max-width: 576px) {
        .stat-icon {
            width: 32px;
            height: 32px;
            font-size: 0.95rem;
            border-radius: 8px;
        }
    }
    .stat-icon-income  { background: var(--income-soft); color: var(--income); }
    .stat-icon-expense { background: var(--expense-soft); color: var(--expense); }
    .stat-label {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    @media (max-width: 576px) {
        .stat-label {
            font-size: 0.65rem;
        }
    }
    .stat-value {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    @media (max-width: 576px) {
        .stat-value {
            font-size: 0.92rem;
        }
    }

    /* Smart Command Bar (Quick Input) */
    .command-box {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 14px 16px;
        box-shadow: var(--card-shadow-sm);
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .command-box:focus-within {
        border-color: var(--brand-primary);
        box-shadow: 0 0 0 3px var(--brand-primary-soft);
    }
    .command-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .command-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .command-badge-key {
        font-size: 0.68rem;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 6px;
        background: var(--bg-surface-elevated);
        border: 1px solid var(--border-color);
        color: var(--text-muted);
    }
    .command-input-group {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    .command-input {
        flex: 1;
        background: var(--bg-surface-elevated);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 10px 14px;
        color: var(--text-primary);
        font-size: 0.92rem;
        outline: none;
        transition: border-color 0.15s ease;
    }
    .command-input:focus {
        border-color: var(--brand-primary);
    }
    .command-btn {
        background: var(--brand-primary);
        color: var(--brand-btn-text);
        border: none;
        border-radius: 10px;
        padding: 0 16px;
        height: 42px;
        font-weight: 600;
        font-size: 0.88rem;
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        flex-shrink: 0;
        transition: all 0.15s ease;
    }
    .command-btn:hover {
        background: var(--brand-primary-hover);
        color: var(--brand-btn-text);
    }
    .command-preview {
        margin-top: 10px;
        background: var(--bg-surface-elevated);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 0.82rem;
        animation: fadeIn 0.2s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Segmented Filter Control */
    .segmented-control {
        display: flex;
        background: var(--bg-surface-elevated);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 4px;
        gap: 4px;
    }
    .segmented-btn {
        flex: 1;
        text-align: center;
        padding: 7px 0;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.15s ease;
        border: none;
        background: transparent;
    }
    .segmented-btn.active {
        background: var(--bg-surface);
        color: var(--text-primary);
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid var(--border-color);
    }

    /* Category Chips Carousel */
    .chip-scroll {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
        scroll-snap-type: x proximity;
    }
    .chip-scroll::-webkit-scrollbar { display: none; }
    .cat-chip {
        flex: 0 0 auto;
        scroll-snap-align: start;
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
        padding: 6px 13px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
    }
    .cat-chip:hover {
        background: var(--bg-surface-hover);
        color: var(--text-primary);
    }
    .cat-chip.active {
        background: var(--brand-primary);
        border-color: var(--brand-primary);
        color: var(--brand-btn-text);
    }

    /* Transaction Rows */
    .txn-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: all 0.15s ease;
        cursor: pointer;
    }
    .txn-card:hover {
        background: var(--bg-surface-hover);
        border-color: var(--border-color-hover);
        transform: translateY(-1px);
    }
    .txn-thumb {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        object-fit: cover;
        flex-shrink: 0;
        cursor: pointer;
        border: 1px solid var(--border-color);
    }
    .txn-content {
        flex: 1;
        min-width: 0;
    }
    .txn-title {
        font-weight: 600;
        font-size: 0.92rem;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .txn-meta {
        font-size: 0.76rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 2px;
    }
    .txn-amount-box {
        text-align: right;
    }
    .txn-amount {
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: -0.01em;
    }
    .amount-income  { color: var(--income); }
    .amount-expense { color: var(--text-primary); }

    /* Action Buttons inside row */
    .btn-icon-soft {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid transparent;
        background: transparent;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-icon-soft:hover {
        background: var(--bg-surface-elevated);
        border-color: var(--border-color);
        color: var(--text-primary);
    }

    @media (max-width: 576px) {
        .chip-scroll {
            padding-bottom: 6px;
            -webkit-overflow-scrolling: touch;
        }
        .txn-card {
            padding: 10px 12px;
            gap: 10px;
            border-radius: 12px;
        }
        .txn-thumb {
            width: 38px;
            height: 38px;
            border-radius: 10px;
        }
        .icon-squircle {
            width: 38px;
            height: 38px;
            font-size: 1rem;
            border-radius: 10px;
        }
        .txn-title {
            font-size: 0.88rem;
        }
        .txn-meta {
            font-size: 0.72rem;
            gap: 6px;
        }
        .txn-amount {
            font-size: 0.88rem;
        }
        .btn-icon-soft {
            width: 36px;
            height: 36px;
        }
    }

    /* Budget Item Card */
    .budget-card {
        background: var(--bg-surface-elevated);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 14px 16px;
        margin-bottom: 10px;
    }
    .budget-progress-track {
        height: 7px;
        border-radius: 999px;
        background: var(--bg-surface-hover);
        overflow: hidden;
        margin: 8px 0;
    }
    .budget-progress-fill {
        height: 100%;
        border-radius: 999px;
        transition: width 0.4s ease;
    }
    .progress-safe { background: var(--income); }
    .progress-warn { background: var(--warning); }
    .progress-over { background: var(--expense); }

    /* Section Headings */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }
    .section-title {
        font-size: 0.95rem;
        font-weight: 700;
        margin: 0;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-action {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--brand-primary);
        background: transparent;
        border: none;
        padding: 0;
        display: flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
    }
    .section-action:hover {
        text-decoration: underline;
    }

    /* Flash Highlight Animation */
    .field-flash {
        animation: flashHighlight 1.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes flashHighlight {
        0% { box-shadow: 0 0 0 3px var(--brand-primary-soft); border-color: var(--brand-primary); }
        100% { box-shadow: none; }
    }
</style>

<div class="row g-4 mb-5">
    {{-- ===================== LEFT / MAIN FEED COLUMN ===================== --}}
    <div class="col-12 col-lg-7 col-xl-7">
        
        {{-- 1. HERO BALANCE CARD --}}
        <div class="hero-overview mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <span class="hero-eyebrow">Saldo Bersih Periode Ini</span>
                <span class="badge {{ $saldo >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-2 py-1 font-monospace" style="font-size: 0.72rem; font-weight: 600;">
                    <i class="bi {{ $saldo >= 0 ? 'bi-shield-check' : 'bi-exclamation-triangle' }} me-1"></i>
                    {{ $saldo >= 0 ? 'Arus Kas Sehat' : 'Defisit Pengeluaran' }}
                </span>
            </div>

            <div class="hero-balance-val tabular-nums">
                Rp {{ number_format($saldo, 0, ',', '.') }}
            </div>

            <div class="row g-2">
                <div class="col-6">
                    <div class="stat-pill">
                        <div class="stat-icon stat-icon-income">
                            <i class="bi bi-arrow-down-left"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="stat-label">Pemasukan</div>
                            <div class="stat-value tabular-nums text-success">
                                +{{ number_format($totalPemasukan, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-pill">
                        <div class="stat-icon stat-icon-expense">
                            <i class="bi bi-arrow-up-right"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="stat-label">Pengeluaran</div>
                            <div class="stat-value tabular-nums text-danger">
                                -{{ number_format($totalPengeluaran, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. SMART COMMAND BAR (INPUT CEPAT) --}}
        <div class="command-box mb-4">
            <div class="command-header">
                <span class="command-label">
                    <i class="bi bi-terminal" style="color: var(--brand-primary);"></i>
                    Input Cepat
                </span>
                <span class="command-badge-key d-none d-sm-inline-block">Tekan Enter ↵</span>
            </div>
            <div class="command-input-group">
                <input type="text" id="quickText" class="command-input"
                       placeholder="Cth: Makan siang 35k atau Gaji freelance 3jt...">
                <button type="button" id="quickParseBtn" class="command-btn" title="Proses dan isi otomatis">
                    <i class="bi bi-arrow-return-left" id="quickParseIcon"></i>
                    <span class="d-none d-sm-inline">Proses</span>
                </button>
            </div>

            <div id="quickPreview" class="command-preview d-none">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="fw-bold text-success"><i class="bi bi-check2-circle me-1"></i> Data Terdeteksi:</span>
                    <button type="button" class="btn-close" style="font-size: 0.65rem;" onclick="document.getElementById('quickPreview').classList.add('d-none')"></button>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="badge bg-secondary-subtle" style="color: var(--text-primary);"><i class="bi bi-pencil me-1"></i><span id="qpTitle">-</span></span>
                    <span class="badge bg-primary-subtle text-primary"><i class="bi bi-tag me-1"></i><span id="qpCategory">-</span></span>
                    <span class="badge bg-success-subtle text-success tabular-nums"><i class="bi bi-cash me-1"></i><span id="qpAmount">-</span></span>
                    <span class="badge bg-secondary-subtle" style="color: var(--text-primary);"><i class="bi bi-calendar3 me-1"></i><span id="qpDate">-</span></span>
                </div>
                <div class="small text-muted mt-2" style="font-size: 0.74rem;">
                    Formulir di modal telah terisi otomatis. Klik tombol "Catat Transaksi" untuk meninjau atau menyimpan.
                </div>
            </div>
        </div>

        {{-- 3. FILTER TIPE & KATEGORI --}}
        <div class="mb-3">
            @php
                $tipeOptions = ['' => 'Semua', 'pengeluaran' => 'Pengeluaran', 'pemasukan' => 'Pemasukan'];
                $baseQuery = request()->except('type');
            @endphp
            <div class="segmented-control mb-3">
                @foreach ($tipeOptions as $val => $label)
                    <a href="{{ route('transactions.index', array_merge($baseQuery, $val ? ['type' => $val] : [])) }}"
                       class="segmented-btn {{ request('type', '') == $val ? 'active' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Kategori Horizontal Scroll --}}
            <div class="chip-scroll mb-3">
                @php $catBaseQuery = request()->except('kategori'); @endphp
                <a href="{{ route('transactions.index', $catBaseQuery) }}"
                   class="cat-chip {{ !request('kategori') ? 'active' : '' }}">
                    <i class="bi bi-grid"></i> Semua Kategori
                </a>
                @foreach ($categories as $cat)
                    @php $meta = categoryMeta($cat); @endphp
                    <a href="{{ route('transactions.index', array_merge($catBaseQuery, ['kategori' => $cat])) }}"
                       class="cat-chip {{ request('kategori') == $cat ? 'active' : '' }}">
                        <i class="bi {{ $meta['icon'] }}"></i> {{ $cat }}
                    </a>
                @endforeach
            </div>

            {{-- Toolbar Pencarian & Urutan --}}
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-semibold text-muted">
                    Menampilkan {{ $transactions->total() }} Transaksi
                </span>
                <button class="btn-ghost-action" style="font-size: 0.8rem; padding: 4px 10px;" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilter">
                    <i class="bi bi-sliders"></i> Filter & Cari
                </button>
            </div>

            <div class="collapse mb-3 {{ request()->hasAny(['q', 'bulan', 'sort']) ? 'show' : '' }}" id="advancedFilter">
                <form action="{{ route('transactions.index') }}" method="GET" class="card-panel p-3">
                    @foreach (request()->except(['q','bulan','sort']) as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach
                    <div class="row g-2">
                        <div class="col-12 col-sm-7">
                            <label class="form-label">Kata Kunci</label>
                            <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari judul / catatan..." value="{{ request('q') }}">
                        </div>
                        <div class="col-12 col-sm-5">
                            <label class="form-label">Bulan</label>
                            <input type="month" name="bulan" class="form-control form-control-sm" value="{{ request('bulan') }}">
                        </div>
                        <div class="col-7 col-sm-8">
                            <label class="form-label">Urutan</label>
                            <select name="sort" class="form-select form-select-sm">
                                <option value="date_desc" {{ request('sort', 'date_desc') == 'date_desc' ? 'selected' : '' }}>Tanggal Terbaru</option>
                                <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Tanggal Terlama</option>
                                <option value="amount_desc" {{ request('sort') == 'amount_desc' ? 'selected' : '' }}>Nominal Terbesar</option>
                                <option value="amount_asc" {{ request('sort') == 'amount_asc' ? 'selected' : '' }}>Nominal Terkecil</option>
                            </select>
                        </div>
                        <div class="col-5 col-sm-4 d-grid">
                            <label class="form-label d-none d-sm-block">&nbsp;</label>
                            <button class="btn btn-primary-action btn-sm justify-content-center" type="submit">Terapkan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 4. DAFTAR TRANSAKSI --}}
        <div class="d-flex flex-column gap-2 mb-4">
            @forelse ($transactions as $trx)
                @php $meta = categoryMeta($trx->category); @endphp
                <div class="txn-card" data-edit-target="#editModal{{ $trx->id }}">
                    @if ($trx->image)
                        <img src="{{ $trx->image_url }}" class="txn-thumb" alt="bukti"
                             data-bs-toggle="modal" data-bs-target="#previewModal{{ $trx->id }}" title="Lihat Bukti Foto">
                    @else
                        <div class="icon-squircle cat-{{ $meta['color'] }}">
                            <i class="bi {{ $meta['icon'] }}"></i>
                        </div>
                    @endif

                    <div class="txn-content">
                        <div class="txn-title">{{ $trx->title }}</div>
                        <div class="txn-meta">
                            <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.7rem; font-weight: 600;">{{ $trx->category }}</span>
                            <span>&bull;</span>
                            <span>{{ $trx->date->translatedFormat('d M Y') }}</span>
                            @if ($trx->description)
                                <span>&bull;</span>
                                <span class="text-truncate d-none d-sm-inline" style="max-width: 140px;">{{ $trx->description }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="txn-amount-box">
                        <div class="txn-amount tabular-nums {{ $trx->type == 'pemasukan' ? 'amount-income' : 'amount-expense' }}">
                            {{ $trx->type == 'pemasukan' ? '+' : '-' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                        </div>
                    </div>

                    {{-- Actions Dropdown --}}
                    <div class="dropdown">
                        <button type="button" class="btn-icon-soft" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu Aksi">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border: 1px solid var(--border-color); background: var(--bg-surface); border-radius: 12px; font-size: 0.85rem;">
                            <li>
                                <button type="button" class="dropdown-item py-2 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#editModal{{ $trx->id }}">
                                    <i class="bi bi-pencil" style="color: var(--brand-primary);"></i> Edit Transaksi
                                </button>
                            </li>
                            @if ($trx->image)
                                <li>
                                    <button type="button" class="dropdown-item py-2 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#previewModal{{ $trx->id }}">
                                        <i class="bi bi-image" style="color: var(--text-muted);"></i> Lihat Bukti
                                    </button>
                                </li>
                            @endif
                            <li><hr class="dropdown-divider" style="border-color: var(--border-color);"></li>
                            <li>
                                <button type="button" class="dropdown-item py-2 text-danger d-flex align-items-center gap-2 btn-delete-trigger"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        data-action="{{ route('transactions.destroy', $trx) }}"
                                        data-title="{{ $trx->title }}">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Modal Preview Gambar --}}
                @if ($trx->image)
                    <div class="modal fade" id="previewModal{{ $trx->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header border-0 pb-0">
                                    <h6 class="modal-title font-semibold">Bukti Transaksi — {{ $trx->title }}</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center p-4">
                                    <img src="{{ $trx->image_url }}" class="img-fluid rounded-3 shadow-sm" alt="bukti transaksi" style="max-height: 70vh;">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Modal Edit Transaksi --}}
                <div class="modal fade sheet-modal" id="editModal{{ $trx->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="sheet-handle"></div>
                            <form action="{{ route('transactions.update', $trx) }}" method="POST" enctype="multipart/form-data">
                                @csrf @method('PUT')
                                <div class="modal-header border-0 pb-2">
                                    <h6 class="modal-title font-semibold">Edit Transaksi</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body pt-0">
                                    <div class="mb-3">
                                        <label class="form-label">Tipe Transaksi</label>
                                        <div class="segmented-control">
                                            <label class="segmented-btn {{ $trx->type == 'pengeluaran' ? 'active' : '' }}" style="cursor: pointer;">
                                                <input type="radio" name="type" value="pengeluaran" class="d-none" {{ $trx->type == 'pengeluaran' ? 'checked' : '' }} onchange="this.parentElement.parentElement.querySelectorAll('.segmented-btn').forEach(b => b.classList.remove('active')); this.parentElement.classList.add('active');">
                                                Pengeluaran
                                            </label>
                                            <label class="segmented-btn {{ $trx->type == 'pemasukan' ? 'active' : '' }}" style="cursor: pointer;">
                                                <input type="radio" name="type" value="pemasukan" class="d-none" {{ $trx->type == 'pemasukan' ? 'checked' : '' }} onchange="this.parentElement.parentElement.querySelectorAll('.segmented-btn').forEach(b => b.classList.remove('active')); this.parentElement.classList.add('active');">
                                                Pemasukan
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Judul Transaksi</label>
                                        <input type="text" name="title" class="form-control" value="{{ $trx->title }}" required>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        <div class="col-12 col-sm-7">
                                            <label class="form-label">Jumlah (Rp)</label>
                                            <input type="number" step="0.01" name="amount" class="form-control tabular-nums" value="{{ $trx->amount }}" required>
                                        </div>
                                        <div class="col-12 col-sm-5">
                                            <label class="form-label">Kategori</label>
                                            <input type="text" name="category" list="category-list" class="form-control" value="{{ $trx->category }}" required>
                                        </div>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        <div class="col-12 col-sm-6">
                                            <label class="form-label">Tanggal</label>
                                            <input type="date" name="date" class="form-control" value="{{ $trx->date->format('Y-m-d') }}" required>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <label class="form-label">Ganti Bukti (opsional)</label>
                                            <input type="file" name="image" class="form-control" accept="image/*">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Catatan</label>
                                        <textarea name="description" class="form-control" rows="2">{{ $trx->description }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="submit" class="btn btn-primary-action w-100 justify-content-center">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card-panel p-5 text-center text-muted">
                    <div class="icon-squircle cat-slate mx-auto mb-3" style="width: 54px; height: 54px; font-size: 1.5rem;">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <div class="fw-bold mb-1" style="color: var(--text-primary); font-size: 1.05rem;">Belum Ada Transaksi</div>
                    <p class="small text-muted mb-3">Tidak ada transaksi yang cocok dengan filter saat ini.</p>
                    <button type="button" class="btn btn-primary-action btn-sm mx-auto" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-plus-lg"></i> Catat Transaksi Baru
                    </button>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mb-4">
            {{ $transactions->links() }}
        </div>
    </div>

    {{-- ===================== RIGHT / SIDEBAR COLUMN ===================== --}}
    <div class="col-12 col-lg-5 col-xl-5">

        {{-- 1. WIDGET BUDGET BULAN INI --}}
        <div class="card-panel p-3 p-sm-4 mb-4" id="budget-section">
            <div class="section-header">
                <h6 class="section-title">
                    <i class="bi bi-pie-chart" style="color: var(--brand-primary);"></i>
                    Budget Bulan Ini
                </h6>
                <button class="section-action" type="button" data-bs-toggle="modal" data-bs-target="#budgetModal">
                    <i class="bi bi-gear-fill"></i> Atur
                </button>
            </div>

            @if ($budgets->isNotEmpty())
                <div class="mb-3 d-flex justify-content-between align-items-center small text-muted">
                    <span>Total Terpakai: <strong class="tabular-nums" style="color: var(--text-primary);">Rp {{ number_format($totalBudgetSpent, 0, ',', '.') }}</strong></span>
                    <span class="badge {{ $budgetOverallPercent > 100 ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' }} rounded-pill font-monospace">
                        {{ $budgetOverallPercent }}%
                    </span>
                </div>

                @foreach ($budgets as $budget)
                    @php
                        $meta = categoryMeta($budget->category);
                        $barClass = $budget->is_over ? 'progress-over' : ($budget->percent >= 80 ? 'progress-warn' : 'progress-safe');
                    @endphp
                    <div class="budget-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <div class="icon-squircle-sm cat-{{ $meta['color'] }}">
                                    <i class="bi {{ $meta['icon'] }}"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold small" style="color: var(--text-primary); font-size: 0.9rem;">{{ $budget->category }}</div>
                                    <div class="text-muted tabular-nums" style="font-size: 0.78rem;">
                                        Rp {{ number_format($budget->spent, 0, ',', '.') }} / {{ number_format($budget->amount, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge {{ $budget->is_over ? 'bg-danger-subtle text-danger' : ($budget->percent >= 80 ? 'bg-warning-subtle text-warning' : 'bg-secondary-subtle') }} font-monospace" style="font-size: 0.75rem; color: var(--text-primary);">
                                    {{ $budget->percent }}%
                                </span>
                                <form action="{{ route('budgets.destroy', $budget) }}" method="POST" onsubmit="return confirm('Hapus budget kategori {{ $budget->category }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-icon-soft" style="width: 24px; height: 24px;" title="Hapus"><i class="bi bi-x"></i></button>
                                </form>
                            </div>
                        </div>

                        <div class="budget-progress-track">
                            <div class="budget-progress-fill {{ $barClass }}" style="width: {{ min($budget->percent, 100) }}%;"></div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-bullseye mb-2 d-block" style="font-size: 1.8rem; opacity: 0.5;"></i>
                    <div class="small fw-semibold mb-1" style="color: var(--text-primary); font-size: 0.92rem;">Belum Ada Budget Ditentukan</div>
                    <div class="small mb-3">Atur batas anggaran per kategori agar keuangan tetap terkendali.</div>
                    <button type="button" class="btn btn-ghost-action btn-sm" data-bs-toggle="modal" data-bs-target="#budgetModal">
                        <i class="bi bi-plus-lg"></i> Buat Anggaran Sekarang
                    </button>
                </div>
            @endif
        </div>

        {{-- 2. GRAFIK LAPORAN --}}
        <div id="laporan-section">
            <div class="card-panel p-3 p-sm-4 mb-4">
                <div class="section-header">
                    <h6 class="section-title">
                        <i class="bi bi-pie-chart-fill" style="color: #6366f1;"></i>
                        Pengeluaran per Kategori
                    </h6>
                    <span class="small text-muted">Bulan Berjalan</span>
                </div>
                <div style="position: relative; height: 220px;">
                    <canvas id="chartCategory"></canvas>
                </div>
            </div>

            <div class="card-panel p-3 p-sm-4 mb-4">
                <div class="section-header">
                    <h6 class="section-title">
                        <i class="bi bi-graph-up" style="color: #10b981;"></i>
                        Tren Arus Kas 6 Bulan
                    </h6>
                    <span class="small text-muted">Masuk vs Keluar</span>
                </div>
                <div style="position: relative; height: 200px;">
                    <canvas id="chartTrend"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>

<datalist id="category-list">
    @foreach (\App\Models\Transaction::DEFAULT_CATEGORIES as $cat)
        <option value="{{ $cat }}">
    @endforeach
</datalist>

{{-- ===================== MODAL TAMBAH TRANSAKSI ===================== --}}
<div class="modal fade sheet-modal" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="sheet-handle"></div>
            <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 pb-2">
                    <h6 class="modal-title font-semibold">Catat Transaksi Baru</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">

                    {{-- Tipe Selector Segmented --}}
                    <div class="mb-3">
                        <label class="form-label">Tipe Transaksi</label>
                        <div class="segmented-control" id="modalTypeGroup">
                            <label class="segmented-btn active" style="cursor: pointer;">
                                <input type="radio" name="type" id="typeExpense" value="pengeluaran" class="d-none" checked onchange="toggleModalType(this)">
                                Pengeluaran
                            </label>
                            <label class="segmented-btn" style="cursor: pointer;">
                                <input type="radio" name="type" id="typeIncome" value="pemasukan" class="d-none" onchange="toggleModalType(this)">
                                Pemasukan
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Judul Transaksi</label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Makan siang, Gaji, dsb." required value="{{ old('title') }}">
                        @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-12 col-sm-7">
                            <label class="form-label">Jumlah (Rp)</label>
                            <input type="number" step="0.01" min="0" name="amount" class="form-control tabular-nums" placeholder="0" required value="{{ old('amount') }}">
                            @error('amount') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-sm-5">
                            <label class="form-label">Kategori</label>
                            <input type="text" name="category" list="category-list" class="form-control" placeholder="Pilih kategori" required value="{{ old('category') }}">
                            @error('category') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="date" class="form-control" required value="{{ old('date', now()->format('Y-m-d')) }}">
                            @error('date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Bukti Gambar (opsional)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Tambahan (opsional)</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Catatan opsional...">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary-action w-100 justify-content-center">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== MODAL ATUR BUDGET ===================== --}}
<div class="modal fade sheet-modal" id="budgetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="sheet-handle"></div>
            <form action="{{ route('budgets.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-2">
                    <h6 class="modal-title font-semibold">Atur Batas Anggaran Kategori</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="mb-3">
                        <label class="form-label">Kategori Pengeluaran</label>
                        <input type="text" name="category" list="category-list" class="form-control" required placeholder="Contoh: Makan, Jajan, Bensin">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Batas Anggaran (Rp)</label>
                        <input type="number" step="0.01" min="0" name="amount" class="form-control tabular-nums" required placeholder="1500000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bulan Anggaran</label>
                        <input type="month" name="month" class="form-control" value="{{ now()->format('Y-m') }}" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary-action w-100 justify-content-center">Simpan Batas Anggaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== MODAL HAPUS TRANSAKSI ===================== --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-1">
                <h6 class="modal-title text-danger font-semibold d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi Hapus
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Yakin ingin menghapus transaksi <strong id="deleteModalTitle" class="text-body"></strong>? Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-ghost-action btn-sm" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    function toggleModalType(radio) {
        radio.closest('.segmented-control').querySelectorAll('.segmented-btn').forEach(btn => btn.classList.remove('active'));
        radio.parentElement.classList.add('active');
    }

    // ===== Command Bar Quick Input =====
    (function () {
        const quickText   = document.getElementById('quickText');
        const quickBtn    = document.getElementById('quickParseBtn');
        const quickIcon   = document.getElementById('quickParseIcon');
        const quickPreview = document.getElementById('quickPreview');
        const csrfToken   = document.querySelector('meta[name="csrf-token"]').content;
        const addForm     = document.querySelector('#addModal form');

        function flash(el) {
            el.classList.remove('field-flash');
            void el.offsetWidth;
            el.classList.add('field-flash');
        }

        function formatRupiah(n) {
            return 'Rp ' + Number(n).toLocaleString('id-ID');
        }

        async function doQuickParse() {
            const text = quickText.value.trim();
            if (!text) { quickText.focus(); return; }

            quickBtn.disabled = true;
            quickIcon.className = 'spinner-border spinner-border-sm';

            try {
                const res = await fetch('{{ route('transactions.quick-parse') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ text }),
                });

                if (!res.ok) throw new Error('Gagal memproses data');
                const data = await res.json();

                // Isi formulir modal tambah
                addForm.querySelector('[name="title"]').value = data.title;
                addForm.querySelector('[name="category"]').value = data.category;
                addForm.querySelector('[name="amount"]').value = data.amount;
                addForm.querySelector('[name="date"]').value = data.date;

                const isIncome = data.type === 'pemasukan';
                const targetRadio = addForm.querySelector(isIncome ? '#typeIncome' : '#typeExpense');
                targetRadio.checked = true;
                toggleModalType(targetRadio);

                ['title', 'category', 'amount', 'date'].forEach(function (name) {
                    const el = addForm.querySelector('[name="' + name + '"]');
                    if (el) flash(el);
                });

                // Update info di preview bar
                document.getElementById('qpTitle').innerText = data.title;
                document.getElementById('qpCategory').innerText = data.category;
                document.getElementById('qpAmount').innerText = (isIncome ? '+' : '-') + formatRupiah(data.amount);
                document.getElementById('qpDate').innerText = data.date;
                quickPreview.classList.remove('d-none');

                // Buka modal secara halus agar user bisa mengonfirmasi/menyimpan
                const modal = new bootstrap.Modal(document.getElementById('addModal'));
                modal.show();

            } catch (e) {
                alert('Gagal mendeteksi format. Silakan klik tombol "Catat Transaksi" untuk memasukkan secara manual.');
            } finally {
                quickBtn.disabled = false;
                quickIcon.className = 'bi bi-arrow-return-left';
            }
        }

        quickBtn.addEventListener('click', doQuickParse);
        quickText.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                doQuickParse();
            }
        });
    })();

    // Delete modal trigger
    document.querySelectorAll('.btn-delete-trigger').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('deleteForm').action = this.dataset.action;
            document.getElementById('deleteModalTitle').innerText = this.dataset.title;
        });
    });

    // Delegated click on transaction card to open edit modal (ignoring dropdown and thumbnail)
    document.addEventListener('click', function (e) {
        if (e.target.closest('.dropdown') || e.target.closest('.txn-thumb') || e.target.closest('.modal')) {
            return;
        }
        const card = e.target.closest('.txn-card[data-edit-target]');
        if (card) {
            const target = card.getAttribute('data-edit-target');
            const modalEl = document.querySelector(target);
            if (modalEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        }
    });

    // ===== Chart.js Configuration & Dynamic Theme Sync =====

    (function() {
        const isDark = () => document.documentElement.getAttribute('data-theme') === 'dark';
        const getColors = () => ({
            text: isDark() ? '#94a3b8' : '#64748b',
            grid: isDark() ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.06)',
            border: isDark() ? '#111827' : '#ffffff',
        });

        const palette = [
            '#3b82f6', '#10b981', '#f59e0b', '#f43f5e', 
            '#8b5cf6', '#06b6d4', '#f97316', '#a855f7'
        ];

        // 1. Category Breakdown Doughnut Chart
        const chartCategoryCtx = document.getElementById('chartCategory');
        const categoryLabels = {!! json_encode($chartCategoryLabels) !!};
        const categoryValues = {!! json_encode($chartCategoryValues) !!};

        let chartCategory = null;
        if (chartCategoryCtx) {
            chartCategory = new Chart(chartCategoryCtx, {
                type: 'doughnut',
                data: {
                    labels: categoryLabels.length ? categoryLabels : ['Belum Ada Data'],
                    datasets: [{
                        data: categoryValues.length ? categoryValues : [1],
                        backgroundColor: categoryValues.length ? palette : ['rgba(148, 163, 184, 0.2)'],
                        borderColor: getColors().border,
                        borderWidth: 2,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: getColors().text,
                                boxWidth: 10,
                                boxHeight: 10,
                                padding: 12,
                                font: { size: 11, family: 'Plus Jakarta Sans', weight: '500' }
                            }
                        }
                    }
                }
            });
        }

        // 2. 6-Month Trend Line Chart
        const chartTrendCtx = document.getElementById('chartTrend');
        let chartTrend = null;
        if (chartTrendCtx) {
            chartTrend = new Chart(chartTrendCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($trendLabels) !!},
                    datasets: [
                        {
                            label: 'Pemasukan',
                            data: {!! json_encode($trendPemasukan) !!},
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.08)',
                            tension: 0.35,
                            fill: true,
                            pointRadius: 3,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Pengeluaran',
                            data: {!! json_encode($trendPengeluaran) !!},
                            borderColor: '#f43f5e',
                            backgroundColor: 'rgba(244, 63, 94, 0.08)',
                            tension: 0.35,
                            fill: true,
                            pointRadius: 3,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                color: getColors().text,
                                boxWidth: 8,
                                font: { size: 11, family: 'Plus Jakarta Sans', weight: '600' }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: getColors().text, font: { size: 11, family: 'Plus Jakarta Sans' } },
                            grid: { color: getColors().grid }
                        },
                        y: {
                            ticks: {
                                color: getColors().text,
                                font: { size: 10, family: 'Plus Jakarta Sans' },
                                callback: function(val) {
                                    if (val >= 1000000) return (val / 1000000).toFixed(1) + 'jt';
                                    if (val >= 1000) return (val / 1000).toFixed(0) + 'rb';
                                    return val;
                                }
                            },
                            grid: { color: getColors().grid }
                        }
                    }
                }
            });
        }

        // Listen for theme switch event and update chart colors live
        window.addEventListener('themeChanged', function() {
            const colors = getColors();
            if (chartCategory) {
                chartCategory.options.plugins.legend.labels.color = colors.text;
                chartCategory.data.datasets[0].borderColor = colors.border;
                chartCategory.update();
            }
            if (chartTrend) {
                chartTrend.options.plugins.legend.labels.color = colors.text;
                chartTrend.options.scales.x.ticks.color = colors.text;
                chartTrend.options.scales.x.grid.color = colors.grid;
                chartTrend.options.scales.y.ticks.color = colors.text;
                chartTrend.options.scales.y.grid.color = colors.grid;
                chartTrend.update();
            }
        });
    })();
</script>
@endsection
