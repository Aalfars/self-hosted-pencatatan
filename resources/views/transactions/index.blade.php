@extends('layouts.app')

@section('title', 'Dashboard Keuangan — Financee')

@php
    if (!function_exists('categoryMeta')) {
        function categoryMeta($cat) {
            $catClean = ucfirst(strtolower(trim($cat ?? '')));
            $map = [
                'Makan'        => ['icon' => 'bi-cup-hot-fill',     'color' => 'amber'],
                'Jajan'        => ['icon' => 'bi-cup-straw',        'color' => 'orange'],
                'Bensin'       => ['icon' => 'bi-fuel-pump-fill',   'color' => 'blue'],
                'Kewajiban'    => ['icon' => 'bi-shield-check',     'color' => 'indigo'],
                'Donasi/amal'  => ['icon' => 'bi-heart-fill',       'color' => 'rose'],
                'Beli barang'  => ['icon' => 'bi-bag-fill',         'color' => 'purple'],
                'Transportasi' => ['icon' => 'bi-car-front-fill',   'color' => 'cyan'],
                'Tagihan'      => ['icon' => 'bi-receipt-cutoff',   'color' => 'violet'],
                'Kesehatan'    => ['icon' => 'bi-heart-pulse-fill', 'color' => 'emerald'],
                'Hiburan'      => ['icon' => 'bi-controller',       'color' => 'fuchsia'],
                'Gaji'         => ['icon' => 'bi-cash-coin',        'color' => 'emerald'],
                'Pemasukan'    => ['icon' => 'bi-wallet2',          'color' => 'emerald'],
            ];
            return $map[$catClean] ?? ['icon' => 'bi-tag-fill', 'color' => 'slate'];
        }
    }
    $saldo = $totalPemasukan - $totalPengeluaran;
    $totalBudgetSpent = $budgets->sum('spent');
    $totalBudgetLimit = $budgets->sum('amount');
    $budgetOverallPercent = $totalBudgetLimit > 0 ? round(($totalBudgetSpent / $totalBudgetLimit) * 100) : 0;
@endphp

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-16">
    
    {{-- ===================== LEFT / MAIN FEED COLUMN ===================== --}}
    <div class="lg:col-span-7 space-y-6">
        
        {{-- 1. HERO BALANCE CARD --}}
        <div class="p-5 sm:p-7 rounded-3xl bg-white/85 dark:bg-slate-900/70 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800/80 shadow-xl shadow-slate-900/5 dark:shadow-black/40 relative overflow-hidden transition-all duration-300">
            <div class="flex flex-wrap items-center justify-between gap-2.5">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    Saldo Bersih Periode Ini
                </span>
                <span class="badge {{ $saldo >= 0 ? 'bg-success-subtle' : 'bg-danger-subtle' }} text-xs font-semibold px-3 py-1 rounded-full shadow-xs flex items-center gap-1.5">
                    <i class="bi {{ $saldo >= 0 ? 'bi-shield-check' : 'bi-exclamation-triangle' }} text-xs"></i>
                    <span>{{ $saldo >= 0 ? 'Arus Kas Sehat' : 'Defisit Pengeluaran' }}</span>
                </span>
            </div>

            <div class="text-3xl sm:text-4xl font-extrabold tracking-tight tabular-nums text-slate-900 dark:text-white my-3 break-words">
                Rp {{ number_format($saldo, 0, ',', '.') }}
            </div>

            <div class="grid grid-cols-2 gap-3 pt-1">
                <!-- Pemasukan -->
                <div class="p-3 sm:p-3.5 rounded-2xl bg-slate-100/80 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-800 flex items-center gap-3">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-500/15 text-emerald-500 flex items-center justify-center text-lg shrink-0">
                        <i class="bi bi-arrow-down-left"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Pemasukan
                        </div>
                        <div class="text-xs sm:text-sm font-bold tabular-nums text-emerald-600 dark:text-emerald-400 truncate">
                            +{{ number_format($totalPemasukan, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- Pengeluaran -->
                <div class="p-3 sm:p-3.5 rounded-2xl bg-slate-100/80 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-800 flex items-center gap-3">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-rose-500/15 text-rose-500 flex items-center justify-center text-lg shrink-0">
                        <i class="bi bi-arrow-up-right"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Pengeluaran
                        </div>
                        <div class="text-xs sm:text-sm font-bold tabular-nums text-rose-600 dark:text-rose-400 truncate">
                            -{{ number_format($totalPengeluaran, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. SMART COMMAND BAR (INPUT CEPAT) --}}
        <div class="p-4 sm:p-5 rounded-3xl bg-white/85 dark:bg-slate-900/70 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800/80 shadow-md shadow-slate-900/5 dark:shadow-black/30 transition-all focus-within:border-blue-500/50 focus-within:ring-4 focus-within:ring-blue-500/10">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                    <i class="bi bi-terminal text-blue-500 text-sm"></i>
                    Input Cepat AI
                </span>
                <span class="hidden sm:inline-block text-[11px] font-semibold px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200/80 dark:border-slate-700">
                    Tekan Enter ↵
                </span>
            </div>

            <div class="flex items-center gap-2">
                <input type="text" id="quickText" 
                       placeholder="Cth: Makan siang 35k atau Gaji freelance 3jt..."
                       class="flex-1 px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/80 rounded-xl text-slate-900 dark:text-slate-100 text-sm font-medium placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:bg-white dark:focus:bg-slate-800 transition-all">
                <button type="button" id="quickParseBtn" title="Proses dan isi otomatis"
                        class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold text-xs flex items-center gap-2 active:scale-95 shadow-md shadow-blue-500/20 transition-all cursor-pointer">
                    <i class="bi bi-arrow-return-left" id="quickParseIcon"></i>
                    <span class="hidden sm:inline">Proses</span>
                </button>
            </div>

            <!-- NLP Preview Bar -->
            <div id="quickPreview" class="hidden mt-3 p-3.5 rounded-2xl bg-slate-100/90 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700/80 text-xs">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                        <i class="bi bi-check2-circle text-sm"></i> Data Terdeteksi:
                    </span>
                    <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer" 
                            onclick="document.getElementById('quickPreview').classList.add('hidden')">
                        ✕
                    </button>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="badge bg-secondary-subtle text-slate-700 dark:text-slate-200"><i class="bi bi-pencil mr-1"></i><span id="qpTitle">-</span></span>
                    <span class="badge bg-primary-subtle text-blue-600 dark:text-blue-400"><i class="bi bi-tag mr-1"></i><span id="qpCategory">-</span></span>
                    <span class="badge bg-success-subtle text-emerald-600 dark:text-emerald-400 tabular-nums"><i class="bi bi-cash mr-1"></i><span id="qpAmount">-</span></span>
                    <span class="badge bg-secondary-subtle text-slate-700 dark:text-slate-200"><i class="bi bi-calendar3 mr-1"></i><span id="qpDate">-</span></span>
                </div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-2">
                    Formulir di modal telah terisi otomatis. Klik tombol "Catat Transaksi" untuk meninjau atau menyimpan.
                </div>
            </div>
        </div>

        {{-- 3. FILTER TIPE & KATEGORI --}}
        <div class="space-y-3" x-data="{ showFilter: {{ request()->hasAny(['q', 'bulan', 'sort']) ? 'true' : 'false' }} }">
            @php
                $tipeOptions = ['' => 'Semua', 'pengeluaran' => 'Pengeluaran', 'pemasukan' => 'Pemasukan'];
                $baseQuery = request()->except('type');
            @endphp

            <!-- Segmented Type Selector -->
            <div class="p-1 rounded-2xl bg-slate-200/70 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800 flex gap-1">
                @foreach ($tipeOptions as $val => $label)
                    <a href="{{ route('transactions.index', array_merge($baseQuery, $val ? ['type' => $val] : [])) }}"
                       class="flex-1 py-2 text-center text-xs font-semibold rounded-xl transition-all {{ request('type', '') == $val ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <!-- Kategori Horizontal Chips -->
            <div class="flex gap-2 overflow-x-auto pb-2 scroll-smooth no-scrollbar">
                @php $catBaseQuery = request()->except('kategori'); @endphp
                <a href="{{ route('transactions.index', $catBaseQuery) }}"
                   class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold border transition-all flex items-center gap-1.5 cursor-pointer {{ !request('kategori') ? 'bg-blue-600 border-blue-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900/80 border-slate-200/80 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:border-slate-300 dark:hover:border-slate-700' }}">
                    <i class="bi bi-grid"></i> Semua Kategori
                </a>
                @foreach ($categories as $cat)
                    @php $meta = categoryMeta($cat); @endphp
                    <a href="{{ route('transactions.index', array_merge($catBaseQuery, ['kategori' => $cat])) }}"
                       class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold border transition-all flex items-center gap-1.5 cursor-pointer {{ request('kategori') == $cat ? 'bg-blue-600 border-blue-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900/80 border-slate-200/80 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:border-slate-300 dark:hover:border-slate-700' }}">
                        <i class="bi {{ $meta['icon'] }}"></i> {{ $cat }}
                    </a>
                @endforeach
            </div>

            <!-- Toolbar Pencarian & Toggle Filter -->
            <div class="flex justify-between items-center pt-1">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                    Menampilkan {{ $transactions->total() }} Transaksi
                </span>
                <button type="button" @click="showFilter = !showFilter"
                        class="btn-ghost-action text-xs px-3 py-1.5 rounded-xl border border-slate-200/80 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i class="bi bi-sliders"></i>
                    <span>Filter & Cari</span>
                </button>
            </div>

            <!-- Collapsible Filter Form (Alpine.js) -->
            <div x-show="showFilter" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="p-4 sm:p-5 rounded-3xl bg-white/85 dark:bg-slate-900/70 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800 shadow-md">
                <form action="{{ route('transactions.index') }}" method="GET" class="space-y-3">
                    @foreach (request()->except(['q','bulan','sort']) as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-6">
                            <label class="form-label">Kata Kunci</label>
                            <input type="text" name="q" class="form-control" placeholder="Cari judul / catatan..." value="{{ request('q') }}">
                        </div>
                        <div class="sm:col-span-6">
                            <label class="form-label">Bulan Transaksi</label>
                            <input type="month" name="bulan" class="form-control" value="{{ request('bulan') }}">
                        </div>
                        <div class="sm:col-span-8">
                            <label class="form-label">Urutan</label>
                            <select name="sort" class="form-select">
                                <option value="date_desc" {{ request('sort', 'date_desc') == 'date_desc' ? 'selected' : '' }}>Tanggal Terbaru</option>
                                <option value="date_asc" {{ request('sort', 'date_asc') == 'date_asc' ? 'selected' : '' }}>Tanggal Terlama</option>
                                <option value="amount_desc" {{ request('sort', 'amount_desc') == 'amount_desc' ? 'selected' : '' }}>Nominal Terbesar</option>
                                <option value="amount_asc" {{ request('sort', 'amount_asc') == 'amount_asc' ? 'selected' : '' }}>Nominal Terkecil</option>
                            </select>
                        </div>
                        <div class="sm:col-span-4 flex items-end">
                            <button type="submit" class="btn-primary-action w-full justify-center">
                                Terapkan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 4. DAFTAR TRANSAKSI --}}
        <div class="space-y-2.5">
            @forelse ($transactions as $trx)
                @php $meta = categoryMeta($trx->category); @endphp
                <div class="group p-3.5 sm:p-4 rounded-2xl bg-white/80 dark:bg-slate-900/60 hover:bg-white dark:hover:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800/80 hover:border-slate-300 dark:hover:border-slate-700/80 shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-3.5 cursor-pointer"
                     data-edit-target="#editModal{{ $trx->id }}">
                    
                    <!-- Icon / Struk Image -->
                    @if ($trx->image)
                        <img src="{{ $trx->image_url }}" 
                             class="w-11 h-11 rounded-2xl object-cover shrink-0 border border-slate-200 dark:border-slate-700 cursor-pointer hover:opacity-80 transition-opacity" 
                             alt="bukti"
                             data-bs-toggle="modal" data-bs-target="#previewModal{{ $trx->id }}" title="Lihat Bukti Foto">
                    @else
                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-lg shrink-0 cat-{{ $meta['color'] }}">
                            <i class="bi {{ $meta['icon'] }}"></i>
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-sm text-slate-900 dark:text-white truncate">
                            {{ $trx->title }}
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            <span class="badge bg-secondary-subtle text-[10px] font-semibold">{{ ucfirst($trx->category) }}</span>
                            <span>&bull;</span>
                            <span>{{ $trx->date->translatedFormat('d M Y') }}</span>
                            @if ($trx->description)
                                <span class="hidden sm:inline">&bull;</span>
                                <span class="truncate hidden sm:inline max-w-[140px]">{{ $trx->description }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Nominal Amount -->
                    <div class="text-right">
                        <div class="font-bold text-sm tabular-nums {{ $trx->type == 'pemasukan' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-900 dark:text-slate-100' }}">
                            {{ $trx->type == 'pemasukan' ? '+' : '-' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                        </div>
                    </div>

                    <!-- Actions Dropdown (Alpine.js) -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click.stop="open = !open" 
                                class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer" 
                                aria-label="Menu Aksi">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>

                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-1 w-44 rounded-2xl bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800 shadow-xl p-1.5 z-30"
                             style="display: none;">
                            <button type="button" 
                                    data-bs-toggle="modal" data-bs-target="#editModal{{ $trx->id }}"
                                    @click="open = false"
                                    class="w-full text-left flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                                <i class="bi bi-pencil text-blue-500"></i> Edit Transaksi
                            </button>
                            @if ($trx->image)
                                <button type="button" 
                                        data-bs-toggle="modal" data-bs-target="#previewModal{{ $trx->id }}"
                                        @click="open = false"
                                        class="w-full text-left flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                                    <i class="bi bi-image text-slate-400"></i> Lihat Bukti
                                </button>
                            @endif
                            <div class="h-px bg-slate-100 dark:bg-slate-800 my-1"></div>
                            <button type="button" 
                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-action="{{ route('transactions.destroy', $trx) }}"
                                    data-title="{{ $trx->title }}"
                                    @click="open = false"
                                    class="btn-delete-trigger w-full text-left flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors cursor-pointer">
                                <i class="bi bi-trash text-sm"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Modal Preview Gambar --}}
                @if ($trx->image)
                    <div class="modal fade" id="previewModal{{ $trx->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-header border-0 pb-0">
                                <h6 class="modal-title font-semibold">Bukti Transaksi — {{ $trx->title }}</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center p-4">
                                <img src="{{ $trx->image_url }}" class="rounded-2xl shadow-md max-h-[70vh] mx-auto object-contain" alt="bukti transaksi">
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Modal Edit Transaksi --}}
                <div class="modal fade sheet-modal" id="editModal{{ $trx->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="sheet-handle"></div>
                        <form action="{{ route('transactions.update', $trx) }}" method="POST" enctype="multipart/form-data">
                            @csrf @method('PUT')
                            <div class="modal-header border-0 pb-2">
                                <h6 class="modal-title font-semibold">Edit Transaksi</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body space-y-3 pt-0">
                                <div>
                                    <label class="form-label">Tipe Transaksi</label>
                                    <div class="p-1 rounded-xl bg-slate-200/70 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800 flex gap-1">
                                        <label class="flex-1 py-1.5 text-center text-xs font-semibold rounded-lg cursor-pointer transition-all {{ $trx->type == 'pengeluaran' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500' }}">
                                            <input type="radio" name="type" value="pengeluaran" class="hidden" {{ $trx->type == 'pengeluaran' ? 'checked' : '' }} onchange="toggleModalRadio(this)">
                                            Pengeluaran
                                        </label>
                                        <label class="flex-1 py-1.5 text-center text-xs font-semibold rounded-lg cursor-pointer transition-all {{ $trx->type == 'pemasukan' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500' }}">
                                            <input type="radio" name="type" value="pemasukan" class="hidden" {{ $trx->type == 'pemasukan' ? 'checked' : '' }} onchange="toggleModalRadio(this)">
                                            Pemasukan
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label class="form-label">Judul Transaksi</label>
                                    <input type="text" name="title" class="form-control" value="{{ $trx->title }}" required>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                    <div class="sm:col-span-7">
                                        <label class="form-label">Jumlah (Rp)</label>
                                        <input type="number" step="0.01" name="amount" id="editModalAmount{{ $trx->id }}" data-currency-input="editModalCurrencyHelper{{ $trx->id }}" class="form-control tabular-nums font-bold" value="{{ $trx->amount }}" required>
                                        <div id="editModalCurrencyHelper{{ $trx->id }}" class="hidden text-[11px] mt-1.5 p-2 rounded-xl bg-blue-500/10 border border-blue-500/20 text-slate-600 dark:text-slate-300"></div>
                                    </div>
                                    <div class="sm:col-span-5">
                                        <label class="form-label">Kategori</label>
                                        <input type="text" name="category" list="category-list" class="form-control" value="{{ $trx->category }}" required>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                    <div class="sm:col-span-6">
                                        <label class="form-label">Tanggal</label>
                                        <input type="date" name="date" class="form-control" value="{{ $trx->date->format('Y-m-d') }}" required>
                                    </div>
                                    <div class="sm:col-span-6">
                                        <label class="form-label">Ganti Bukti (opsional)</label>
                                        <input type="file" name="image" class="form-control" accept="image/*">
                                    </div>
                                </div>

                                <div>
                                    <label class="form-label">Catatan</label>
                                    <textarea name="description" class="form-control" rows="2">{{ $trx->description }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-2">
                                <button type="submit" class="btn-primary-action w-full justify-center">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 sm:p-12 text-center rounded-3xl bg-white/80 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-3">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <div class="font-bold text-base text-slate-900 dark:text-white mb-1">
                        Belum Ada Transaksi
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                        Tidak ada transaksi yang cocok dengan filter saat ini.
                    </p>
                    <button type="button" 
                            data-bs-toggle="modal" data-bs-target="#addModal"
                            class="btn-primary-action text-xs mx-auto">
                        <i class="bi bi-plus-lg"></i> Catat Transaksi Baru
                    </button>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center pt-2">
            {{ $transactions->links() }}
        </div>
    </div>

    {{-- ===================== RIGHT / SIDEBAR COLUMN ===================== --}}
    <div class="lg:col-span-5 space-y-6">

        {{-- 1. WIDGET BUDGET BULAN INI --}}
        <div class="p-5 sm:p-6 rounded-3xl bg-white/85 dark:bg-slate-900/70 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800/80 shadow-xl shadow-slate-900/5 dark:shadow-black/40" id="budget-section">
            <div class="flex items-center justify-between mb-4">
                <h6 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-pie-chart text-blue-500"></i>
                    Budget Bulan Ini
                </h6>
                <button type="button" data-bs-toggle="modal" data-bs-target="#budgetModal"
                        class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1 cursor-pointer">
                    <i class="bi bi-gear-fill text-xs"></i> Atur
                </button>
            </div>

            @if ($budgets->isNotEmpty())
                <div class="mb-4 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span>Total Terpakai: <strong class="tabular-nums text-slate-900 dark:text-white">Rp {{ number_format($totalBudgetSpent, 0, ',', '.') }}</strong></span>
                    <span class="badge {{ $budgetOverallPercent > 100 ? 'bg-danger-subtle text-rose-600 dark:text-rose-400' : 'bg-primary-subtle text-blue-600 dark:text-blue-400' }} font-mono">
                        {{ $budgetOverallPercent }}%
                    </span>
                </div>

                <div class="space-y-3">
                    @foreach ($budgets as $budget)
                        @php
                            $meta = categoryMeta($budget->category);
                            $barClass = $budget->is_over ? 'bg-rose-500' : ($budget->percent >= 80 ? 'bg-amber-500' : 'bg-emerald-500');
                        @endphp
                        <div class="p-3.5 rounded-2xl bg-slate-100/70 dark:bg-slate-800/40 border border-slate-200/70 dark:border-slate-800">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm shrink-0 cat-{{ $meta['color'] }}">
                                        <i class="bi {{ $meta['icon'] }}"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-xs text-slate-900 dark:text-white">{{ $budget->category }}</div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 tabular-nums">
                                            Rp {{ number_format($budget->spent, 0, ',', '.') }} / {{ number_format($budget->amount, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="badge {{ $budget->is_over ? 'bg-danger-subtle text-rose-600 dark:text-rose-400' : ($budget->percent >= 80 ? 'bg-amber-500/20 text-amber-500' : 'bg-secondary-subtle text-slate-700 dark:text-slate-300') }} font-mono text-[11px]">
                                        {{ $budget->percent }}%
                                    </span>
                                    <form action="{{ route('budgets.destroy', $budget) }}" method="POST" onsubmit="return confirm('Hapus budget kategori {{ $budget->category }}?')">
                                        @csrf @method('DELETE')
                                        <button class="w-6 h-6 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors cursor-pointer" title="Hapus">
                                            <i class="bi bi-x text-base"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="h-2 rounded-full bg-slate-200 dark:bg-slate-700/60 overflow-hidden mt-2.5">
                                <div class="h-full rounded-full transition-all duration-500 {{ $barClass }}" style="width: {{ min($budget->percent, 100) }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 text-slate-400">
                    <i class="bi bi-bullseye mb-2 block text-3xl opacity-50"></i>
                    <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 mb-1">
                        Belum Ada Budget Ditentukan
                    </div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mb-4">
                        Atur batas anggaran per kategori agar keuangan tetap terkendali.
                    </div>
                    <button type="button" 
                            data-bs-toggle="modal" data-bs-target="#budgetModal"
                            class="btn-ghost-action text-xs mx-auto">
                        <i class="bi bi-plus-lg"></i> Buat Anggaran Sekarang
                    </button>
                </div>
            @endif
        </div>

        {{-- 2. GRAFIK LAPORAN --}}
        <div id="laporan-section" class="space-y-6">
            
            <!-- Category Breakdown Doughnut -->
            <div class="p-5 sm:p-6 rounded-3xl bg-white/85 dark:bg-slate-900/70 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800/80 shadow-xl shadow-slate-900/5 dark:shadow-black/40">
                <div class="flex items-center justify-between mb-4">
                    <h6 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-pie-chart-fill text-indigo-500"></i>
                        Pengeluaran per Kategori
                    </h6>
                    <span class="text-[11px] text-slate-400">Bulan Berjalan</span>
                </div>
                <div class="relative h-[220px]">
                    <canvas id="chartCategory"></canvas>
                </div>
            </div>

            <!-- Trend Line Chart -->
            <div class="p-5 sm:p-6 rounded-3xl bg-white/85 dark:bg-slate-900/70 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800/80 shadow-xl shadow-slate-900/5 dark:shadow-black/40">
                <div class="flex items-center justify-between mb-4">
                    <h6 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-graph-up text-emerald-500"></i>
                        Tren Arus Kas 6 Bulan
                    </h6>
                    <span class="text-[11px] text-slate-400">Masuk vs Keluar</span>
                </div>
                <div class="relative h-[200px]">
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
        <div class="sheet-handle"></div>
        <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-header border-0 pb-2">
                <h6 class="modal-title font-semibold">Catat Transaksi Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body space-y-3 pt-0">

                {{-- Tipe Selector Segmented --}}
                <div>
                    <label class="form-label">Tipe Transaksi</label>
                    <div class="p-1 rounded-xl bg-slate-200/70 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800 flex gap-1">
                        <label class="flex-1 py-1.5 text-center text-xs font-semibold rounded-lg cursor-pointer transition-all bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm">
                            <input type="radio" name="type" id="typeExpense" value="pengeluaran" class="hidden" checked onchange="toggleModalRadio(this)">
                            Pengeluaran
                        </label>
                        <label class="flex-1 py-1.5 text-center text-xs font-semibold rounded-lg cursor-pointer transition-all text-slate-500 hover:text-slate-800 dark:hover:text-slate-200">
                            <input type="radio" name="type" id="typeIncome" value="pemasukan" class="hidden" onchange="toggleModalRadio(this)">
                            Pemasukan
                        </label>
                    </div>
                </div>

                <div>
                    <label class="form-label">Judul Transaksi</label>
                    <input type="text" name="title" class="form-control" placeholder="Contoh: Makan siang, Gaji, dsb." required value="{{ old('title') }}">
                    @error('title') <div class="text-rose-500 text-xs mt-1">{{ $message }}</div> @enderror
                </div>

                <!-- Amount & Category with Quick Chips -->
                <div>
                    <div class="flex items-center justify-between mb-1.5 flex-wrap gap-1">
                        <label class="form-label mb-0">Nominal & Kategori</label>
                        <div class="flex items-center gap-1 overflow-x-auto no-scrollbar py-0.5">
                            <button type="button" data-quick-amount="10000" data-target-input="#addModalAmount"
                                    class="px-2 py-0.5 rounded-lg bg-slate-200/80 dark:bg-slate-800 text-[10px] font-semibold text-slate-700 dark:text-slate-300 hover:bg-blue-600 hover:text-white transition-colors cursor-pointer">+10rb</button>
                            <button type="button" data-quick-amount="20000" data-target-input="#addModalAmount"
                                    class="px-2 py-0.5 rounded-lg bg-slate-200/80 dark:bg-slate-800 text-[10px] font-semibold text-slate-700 dark:text-slate-300 hover:bg-blue-600 hover:text-white transition-colors cursor-pointer">+20rb</button>
                            <button type="button" data-quick-amount="50000" data-target-input="#addModalAmount"
                                    class="px-2 py-0.5 rounded-lg bg-slate-200/80 dark:bg-slate-800 text-[10px] font-semibold text-slate-700 dark:text-slate-300 hover:bg-blue-600 hover:text-white transition-colors cursor-pointer">+50rb</button>
                            <button type="button" data-quick-amount="100000" data-target-input="#addModalAmount"
                                    class="px-2 py-0.5 rounded-lg bg-slate-200/80 dark:bg-slate-800 text-[10px] font-semibold text-slate-700 dark:text-slate-300 hover:bg-blue-600 hover:text-white transition-colors cursor-pointer">+100rb</button>
                            <button type="button" data-quick-amount="500000" data-target-input="#addModalAmount"
                                    class="px-2 py-0.5 rounded-lg bg-slate-200/80 dark:bg-slate-800 text-[10px] font-semibold text-slate-700 dark:text-slate-300 hover:bg-blue-600 hover:text-white transition-colors cursor-pointer">+500rb</button>
                            <button type="button" data-quick-amount="0" data-target-input="#addModalAmount"
                                    class="px-2 py-0.5 rounded-lg bg-rose-500/10 text-rose-500 text-[10px] font-semibold hover:bg-rose-500 hover:text-white transition-colors cursor-pointer">Reset</button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-7">
                            <input type="number" step="0.01" min="0" name="amount" id="addModalAmount" data-currency-input="addModalCurrencyHelper"
                                   class="form-control tabular-nums font-bold" placeholder="0" required value="{{ old('amount') }}">
                            <div id="addModalCurrencyHelper" class="hidden text-[11px] mt-1.5 p-2 rounded-xl bg-blue-500/10 border border-blue-500/20 text-slate-600 dark:text-slate-300"></div>
                            @error('amount') <div class="text-rose-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="sm:col-span-5">
                            <input type="text" name="category" list="category-list" class="form-control" placeholder="Pilih kategori" required value="{{ old('category') }}">
                            @error('category') <div class="text-rose-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Date with Quick Shortcuts -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <div class="sm:col-span-6">
                        <div class="flex items-center justify-between mb-1">
                            <label class="form-label mb-0">Tanggal</label>
                            <div class="flex items-center gap-1">
                                <button type="button" data-quick-date="today" data-target-input="#addModalDate"
                                        class="px-2 py-0.5 rounded-lg bg-slate-200/80 dark:bg-slate-800 text-[10px] font-semibold text-slate-700 dark:text-slate-300 hover:bg-blue-600 hover:text-white transition-colors cursor-pointer">Hari Ini</button>
                                <button type="button" data-quick-date="yesterday" data-target-input="#addModalDate"
                                        class="px-2 py-0.5 rounded-lg bg-slate-200/80 dark:bg-slate-800 text-[10px] font-semibold text-slate-700 dark:text-slate-300 hover:bg-blue-600 hover:text-white transition-colors cursor-pointer">Kemarin</button>
                            </div>
                        </div>
                        <input type="date" name="date" id="addModalDate" class="form-control" required value="{{ old('date', now()->format('Y-m-d')) }}">
                        @error('date') <div class="text-rose-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="sm:col-span-6">
                        <label class="form-label">Bukti Gambar (opsional)</label>
                        <input type="file" name="image" class="form-control text-xs" accept="image/*">
                    </div>
                </div>

                <div>
                    <label class="form-label">Catatan Tambahan (opsional)</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Catatan opsional...">{{ old('description') }}</textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-2">
                <button type="submit" class="btn-primary-action w-full justify-center">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===================== MODAL ATUR BUDGET ===================== --}}
<div class="modal fade sheet-modal" id="budgetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="sheet-handle"></div>
        <form action="{{ route('budgets.store') }}" method="POST">
            @csrf
            <div class="modal-header border-0 pb-2">
                <h6 class="modal-title font-semibold">Atur Batas Anggaran Kategori</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body space-y-3 pt-0">
                <div>
                    <label class="form-label">Kategori Pengeluaran</label>
                    <input type="text" name="category" list="category-list" class="form-control" required placeholder="Contoh: Makan, Jajan, Bensin">
                </div>
                <div>
                    <label class="form-label">Batas Anggaran (Rp)</label>
                    <input type="number" step="0.01" min="0" name="amount" id="budgetModalAmount" data-currency-input="budgetModalCurrencyHelper" class="form-control tabular-nums font-bold" required placeholder="1500000">
                    <div id="budgetModalCurrencyHelper" class="hidden text-[11px] mt-1.5 p-2 rounded-xl bg-blue-500/10 border border-blue-500/20 text-slate-600 dark:text-slate-300"></div>
                </div>
                <div>
                    <label class="form-label">Bulan Anggaran</label>
                    <input type="month" name="month" class="form-control" value="{{ now()->format('Y-m') }}" required>
                </div>
            </div>
            <div class="modal-footer border-0 pt-2">
                <button type="submit" class="btn-primary-action w-full justify-center">
                    Simpan Batas Anggaran
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===================== MODAL HAPUS TRANSAKSI ===================== --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-header border-0 pb-1">
            <h6 class="modal-title text-rose-600 dark:text-rose-400 font-semibold flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi Hapus
            </h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-sm text-slate-600 dark:text-slate-300">
            Yakin ingin menghapus transaksi <strong id="deleteModalTitle" class="text-slate-900 dark:text-white"></strong>? Tindakan ini tidak dapat dibatalkan.
        </div>
        <div class="modal-footer border-0 pt-2">
            <button type="button" class="btn-ghost-action text-xs" data-bs-dismiss="modal">Batal</button>
            <form id="deleteForm" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition-colors cursor-pointer">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    function toggleModalRadio(radio) {
        const container = radio.closest('div');
        container.querySelectorAll('label').forEach(label => {
            label.className = 'flex-1 py-1.5 text-center text-xs font-semibold rounded-lg cursor-pointer transition-all text-slate-500 hover:text-slate-800 dark:hover:text-slate-200';
        });
        radio.parentElement.className = 'flex-1 py-1.5 text-center text-xs font-semibold rounded-lg cursor-pointer transition-all bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm';
    }

    // ===== Command Bar Quick Input =====
    (function () {
        const quickText   = document.getElementById('quickText');
        const quickBtn    = document.getElementById('quickParseBtn');
        const quickIcon   = document.getElementById('quickParseIcon');
        const quickPreview = document.getElementById('quickPreview');
        const csrfToken   = document.querySelector('meta[name="csrf-token"]').content;
        const addForm     = document.querySelector('#addModal form');

        function formatRupiah(n) {
            return 'Rp ' + Number(n).toLocaleString('id-ID');
        }

        async function doQuickParse() {
            const text = quickText.value.trim();
            if (!text) { quickText.focus(); return; }

            quickBtn.disabled = true;
            quickIcon.className = 'inline-block w-3.5 h-3.5 border-2 border-current border-t-transparent rounded-full animate-spin';

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
                toggleModalRadio(targetRadio);

                // Update info di preview bar
                document.getElementById('qpTitle').innerText = data.title;
                document.getElementById('qpCategory').innerText = data.category;
                document.getElementById('qpAmount').innerText = (isIncome ? '+' : '-') + formatRupiah(data.amount);
                document.getElementById('qpDate').innerText = data.date;
                quickPreview.classList.remove('hidden');

                // Buka modal secara halus dengan SmoothModal
                const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('addModal'));
                if (modal) modal.show();

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
        if (e.target.closest('[x-data]') || e.target.closest('.txn-thumb') || e.target.closest('.modal')) {
            return;
        }
        const card = e.target.closest('[data-edit-target]');
        if (card) {
            const target = card.getAttribute('data-edit-target');
            const modalEl = document.querySelector(target);
            if (modalEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                if (modal) modal.show();
            }
        }
    });

    // ===== Chart.js Configuration & Dynamic Theme Sync =====
    (function() {
        const isDark = () => document.documentElement.getAttribute('data-theme') === 'dark';
        const getColors = () => ({
            text: isDark() ? '#94a3b8' : '#64748b',
            grid: isDark() ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.06)',
            border: isDark() ? '#0f172a' : '#ffffff',
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
