@extends('layouts.app')

@section('title', 'Dashboard Keuangan')

@php
    if (!function_exists('categoryIcon')) {
        function categoryIcon($cat) {
            $map = [
                'Makan' => '🍜', 'Jajan' => '🍭', 'Bensin' => '⛽', 'Kewajiban' => '📄',
                'Donasi/Amal' => '🤲', 'Beli Barang' => '🛍️', 'Transportasi' => '🚌',
                'Tagihan' => '🧾', 'Kesehatan' => '💊', 'Hiburan' => '🎮',
            ];
            return $map[$cat] ?? '💸';
        }
    }
    if (!function_exists('categoryChipClass')) {
        function categoryChipClass($cat) {
            $classes = ['chip-c1', 'chip-c2', 'chip-c3', 'chip-c4', 'chip-c5'];
            return $classes[crc32($cat) % count($classes)];
        }
    }
    $saldo = $totalPemasukan - $totalPengeluaran;
@endphp

@section('content')

    {{-- ===================== HERO SALDO ===================== --}}
    <div class="hero-balance mb-3">
        <div class="hero-label">Saldo Bersih Periode Ini</div>
        <div class="hero-number">Rp {{ number_format($saldo, 0, ',', '.') }}</div>
        <div class="d-flex gap-2 mt-3">
            <div class="hero-pill hero-pill-income">
                <i class="bi bi-arrow-down-left-circle-fill"></i>
                <span>Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</span>
            </div>
            <div class="hero-pill hero-pill-expense">
                <i class="bi bi-arrow-up-right-circle-fill"></i>
                <span>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- ===================== BUDGET (SCROLL HORIZONTAL) ===================== --}}
    <div class="section-head" id="budget-section">
        <h6>Budget Bulan Ini</h6>
        <button class="link-action" data-bs-toggle="modal" data-bs-target="#budgetModal">
            <i class="bi bi-plus-lg"></i> Atur
        </button>
    </div>

    @if ($budgets->isNotEmpty())
        <div class="scroll-x mb-4">
            @foreach ($budgets as $budget)
                <div class="budget-chip-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <span class="fw-semibold small">{{ categoryIcon($budget->category) }} {{ $budget->category }}</span>
                        <form action="{{ route('budgets.destroy', $budget) }}" method="POST" onsubmit="return confirm('Hapus budget {{ $budget->category }}?')">
                            @csrf @method('DELETE')
                            <button class="btn-icon-ghost"><i class="bi bi-x"></i></button>
                        </form>
                    </div>
                    <div class="budget-track mt-2">
                        <div class="budget-fill {{ $budget->is_over ? 'over' : ($budget->percent >= 80 ? 'warn' : '') }}"
                             style="width: {{ min($budget->percent, 100) }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between small mt-1 text-muted-soft">
                        <span>Rp {{ number_format($budget->spent, 0, ',', '.') }}</span>
                        <span>{{ $budget->percent }}%</span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card-panel p-3 mb-4 text-center text-muted-soft small">
            Belum ada budget diatur bulan ini. Yuk atur biar pengeluaran lebih terkontrol.
        </div>
    @endif

    {{-- ===================== FILTER (SEGMENTED + CHIPS) ===================== --}}
    <div class="segmented mb-3">
        @php
            $tipeOptions = ['' => 'Semua', 'pemasukan' => 'Masuk', 'pengeluaran' => 'Keluar'];
            $baseQuery = request()->except('type');
        @endphp
        @foreach ($tipeOptions as $val => $label)
            <a href="{{ route('transactions.index', array_merge($baseQuery, $val ? ['type' => $val] : [])) }}"
               class="segmented-item {{ request('type', '') == $val ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="scroll-x mb-3">
        @php $catBaseQuery = request()->except('kategori'); @endphp
        <a href="{{ route('transactions.index', $catBaseQuery) }}" class="chip {{ !request('kategori') ? 'chip-active' : '' }}">Semua Kategori</a>
        @foreach ($categories as $cat)
            <a href="{{ route('transactions.index', array_merge($catBaseQuery, ['kategori' => $cat])) }}"
               class="chip {{ request('kategori') == $cat ? 'chip-active' : '' }}">{{ categoryIcon($cat) }} {{ $cat }}</a>
        @endforeach
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <button class="link-action" data-bs-toggle="collapse" data-bs-target="#advancedFilter">
            <i class="bi bi-sliders"></i> Cari & Urutkan
        </button>
        <a href="{{ route('report.export', request()->query()) }}" class="link-action">
            <i class="bi bi-download"></i> Export
        </a>
    </div>

    <div class="collapse mb-3" id="advancedFilter">
        <form action="{{ route('transactions.index') }}" method="GET" class="card-panel p-3">
            @foreach (request()->except(['q','bulan','sort']) as $key => $val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
            @endforeach
            <div class="row g-2">
                <div class="col-7">
                    <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari transaksi..." value="{{ request('q') }}">
                </div>
                <div class="col-5">
                    <input type="month" name="bulan" class="form-control form-control-sm" value="{{ request('bulan') }}">
                </div>
                <div class="col-8">
                    <select name="sort" class="form-select form-select-sm">
                        <option value="date_desc" {{ request('sort', 'date_desc') == 'date_desc' ? 'selected' : '' }}>Tanggal Terbaru</option>
                        <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Tanggal Terlama</option>
                        <option value="amount_desc" {{ request('sort') == 'amount_desc' ? 'selected' : '' }}>Jumlah Terbesar</option>
                        <option value="amount_asc" {{ request('sort') == 'amount_asc' ? 'selected' : '' }}>Jumlah Terkecil</option>
                    </select>
                </div>
                <div class="col-4 d-grid">
                    <button class="btn btn-accent btn-sm" type="submit">Terapkan</button>
                </div>
            </div>
        </form>
    </div>

    {{-- ===================== LIST TRANSAKSI ===================== --}}
    <div class="txn-list mb-4">
        @forelse ($transactions as $trx)
            <div class="txn-row" data-bs-toggle="modal" data-bs-target="#editModal{{ $trx->id }}" role="button">
                @if ($trx->image)
                    <img src="{{ $trx->image_url }}" class="txn-avatar-img" alt="bukti"
                         onclick="event.stopPropagation()" data-bs-toggle="modal" data-bs-target="#previewModal{{ $trx->id }}">
                @else
                    <div class="txn-avatar {{ categoryChipClass($trx->category) }}">{{ categoryIcon($trx->category) }}</div>
                @endif

                <div class="txn-info">
                    <div class="txn-title">{{ $trx->title }}</div>
                    <div class="txn-meta">{{ $trx->category }} &middot; {{ $trx->date->translatedFormat('d M') }}</div>
                </div>

                <div class="txn-right">
                    <div class="txn-amount {{ $trx->type == 'pemasukan' ? 'text-income' : 'text-expense' }}">
                        {{ $trx->type == 'pemasukan' ? '+' : '-' }}{{ number_format($trx->amount, 0, ',', '.') }}
                    </div>
                    <div class="dropdown" onclick="event.stopPropagation()">
                        <button class="btn-icon-ghost" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-dark">
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editModal{{ $trx->id }}">
                                <i class="bi bi-pencil me-2"></i>Edit</button></li>
                            <li><button type="button" class="dropdown-item text-danger btn-delete-trigger"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        data-action="{{ route('transactions.destroy', $trx) }}"
                                        data-title="{{ $trx->title }}">
                                <i class="bi bi-trash me-2"></i>Hapus</button></li>
                        </ul>
                    </div>
                </div>
            </div>

            @if ($trx->image)
                <div class="modal fade" id="previewModal{{ $trx->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header border-0"><h6 class="modal-title">Bukti Transaksi</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                            <div class="modal-body text-center">
                                <img src="{{ $trx->image_url }}" class="img-fluid rounded-4" alt="bukti transaksi">
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Modal Edit (bottom-sheet) --}}
            <div class="modal fade sheet-modal" id="editModal{{ $trx->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="sheet-handle"></div>
                        <form action="{{ route('transactions.update', $trx) }}" method="POST" enctype="multipart/form-data">
                            @csrf @method('PUT')
                            <div class="modal-header border-0">
                                <h6 class="modal-title">Edit Transaksi</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-2">
                                    <label class="form-label">Judul</label>
                                    <input type="text" name="title" class="form-control" value="{{ $trx->title }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Tipe</label>
                                    <select name="type" class="form-select" required>
                                        <option value="pemasukan" {{ $trx->type == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                                        <option value="pengeluaran" {{ $trx->type == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Jumlah (Rp)</label>
                                    <input type="number" step="0.01" name="amount" class="form-control" value="{{ $trx->amount }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Kategori</label>
                                    <input type="text" name="category" list="category-list" class="form-control" value="{{ $trx->category }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Catatan</label>
                                    <textarea name="description" class="form-control" rows="2">{{ $trx->description }}</textarea>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" name="date" class="form-control" value="{{ $trx->date->format('Y-m-d') }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Ganti Gambar (opsional)</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="submit" class="btn btn-accent w-100">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="card-panel p-4 text-center text-muted-soft">
                <div style="font-size:2rem;">🌱</div>
                Belum ada transaksi yang cocok. Coba ubah filter, atau tambahkan transaksi baru.
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mb-4">
        {{ $transactions->links() }}
    </div>

    {{-- ===================== GRAFIK ===================== --}}
    <div id="laporan-section">
        <div class="section-head"><h6>Laporan</h6></div>
        <div class="card-panel p-3 mb-3">
            <div class="small text-muted-soft mb-2">Pengeluaran per Kategori (Bulan Ini)</div>
            <canvas id="chartCategory" height="200"></canvas>
        </div>
        <div class="card-panel p-3 mb-4">
            <div class="small text-muted-soft mb-2">Tren 6 Bulan Terakhir</div>
            <canvas id="chartTrend" height="200"></canvas>
        </div>
    </div>

    <datalist id="category-list">
        @foreach (\App\Models\Transaction::DEFAULT_CATEGORIES as $cat)
            <option value="{{ $cat }}">
        @endforeach
    </datalist>

    {{-- ===================== MODAL TAMBAH (bottom-sheet) ===================== --}}
    <div class="modal fade sheet-modal" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="sheet-handle"></div>
                <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-0">
                        <h6 class="modal-title">Tambah Transaksi</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        {{-- ===== CATAT CEPAT ===== --}}
                        <div class="quick-add-box mb-3">
                            <label class="form-label mb-2"><i class="bi bi-stars me-1"></i>Catat Cepat</label>
                            <div class="d-flex gap-2">
                                <input type="text" id="quickText" class="form-control"
                                       placeholder="Contoh: makan coto sama ayang 39k">
                                <button type="button" id="quickParseBtn" class="btn btn-accent flex-shrink-0" style="width:48px;">
                                    <i class="bi bi-magic" id="quickParseIcon"></i>
                                </button>
                            </div>
                            <div class="quick-hint">Ketik bebas, lalu tekan Enter atau tombol ✨ — sisanya diisi otomatis di bawah.</div>

                            <div id="quickPreview" class="quick-preview d-none">
                                <div class="quick-preview-title"><i class="bi bi-check-circle-fill"></i> Terdeteksi!</div>
                                <div class="quick-preview-row"><i class="bi bi-tag"></i> Tipe: <span id="qpType"></span></div>
                                <div class="quick-preview-row"><i class="bi bi-pencil-square"></i> Keterangan: <span id="qpTitle"></span></div>
                                <div class="quick-preview-row"><i class="bi bi-cash-coin"></i> Jumlah: <span id="qpAmount"></span></div>
                                <div class="quick-preview-row"><i class="bi bi-calendar3"></i> Tanggal: <span id="qpDate"></span></div>
                                <div class="quick-preview-note">Cek dulu field di bawah sebelum simpan ya, kalau ada yang salah tinggal diedit.</div>
                            </div>
                        </div>

                        <div class="divider-or"><span>atau isi manual</span></div>

                        <div class="segmented mb-3" id="typeSegmented">
                            <input type="radio" name="type" id="typeExpense" value="pengeluaran" class="d-none" checked>
                            <label for="typeExpense" class="segmented-item seg-expense">Pengeluaran</label>
                            <input type="radio" name="type" id="typeIncome" value="pemasukan" class="d-none">
                            <label for="typeIncome" class="segmented-item seg-income">Pemasukan</label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Judul</label>
                            <input type="text" name="title" class="form-control" placeholder="Contoh: Makan siang kantor" required value="{{ old('title') }}">
                            @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah (Rp)</label>
                            <input type="number" step="0.01" min="0" name="amount" class="form-control" placeholder="0" required value="{{ old('amount') }}">
                            @error('amount') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <input type="text" name="category" list="category-list" class="form-control" placeholder="Pilih atau ketik kategori baru" required value="{{ old('category') }}">
                            @error('category') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan (opsional)</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                        </div>

                        <div class="row g-2">
                            <div class="col-7">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" class="form-control" required value="{{ old('date', now()->format('Y-m-d')) }}">
                                @error('date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-5">
                                <label class="form-label">Bukti (opsional)</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-accent w-100">Simpan Transaksi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Atur Budget --}}
    <div class="modal fade sheet-modal" id="budgetModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="sheet-handle"></div>
                <form action="{{ route('budgets.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0">
                        <h6 class="modal-title">Atur Budget Kategori</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <input type="text" name="category" list="category-list" class="form-control" required placeholder="Contoh: Makan">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Batas Anggaran (Rp)</label>
                            <input type="number" step="0.01" min="0" name="amount" class="form-control" required placeholder="1500000">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bulan</label>
                            <input type="month" name="month" class="form-control" value="{{ now()->format('Y-m') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-accent w-100">Simpan Budget</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h6 class="modal-title"><i class="bi bi-exclamation-triangle text-danger me-1"></i> Konfirmasi Hapus</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">Yakin ingin menghapus <strong id="deleteModalTitle"></strong>?</div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-sm btn-outline-light" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<style>
    .hero-balance {
        background: linear-gradient(155deg, #241d33, #1a1626);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 26px 24px;
    }
    .hero-label { color: var(--text-muted); font-size: .85rem; font-weight: 600; }
    .hero-number { font-size: 2.5rem; font-weight: 800; letter-spacing: -0.03em; margin-top: 2px; }
    .hero-pill {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 7px 13px; border-radius: 999px; font-size: .82rem; font-weight: 700;
    }
    .hero-pill-income { background: var(--income-soft); color: var(--income); }
    .hero-pill-expense { background: var(--expense-soft); color: var(--expense); }

    .section-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    .section-head h6 { margin: 0; font-weight: 800; font-size: 1rem; }
    .link-action { background: none; border: none; color: var(--accent); font-size: .82rem; font-weight: 700; padding: 0; }

    .scroll-x { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 4px; scroll-snap-type: x proximity; }
    .scroll-x::-webkit-scrollbar { display: none; }

    .chip {
        flex: 0 0 auto; scroll-snap-align: start;
        background: var(--panel); border: 1px solid var(--border); color: var(--text-muted);
        padding: 8px 16px; border-radius: 999px; font-size: .82rem; font-weight: 600; white-space: nowrap;
    }
    .chip-active { background: var(--accent-soft); border-color: var(--accent); color: var(--accent); }

    .segmented { display: flex; background: var(--panel); border: 1px solid var(--border); border-radius: 999px; padding: 4px; }
    .segmented-item { flex: 1; text-align: center; padding: 8px 0; border-radius: 999px; color: var(--text-muted); font-size: .85rem; font-weight: 700; cursor: pointer; }
    .segmented-item.active, #typeExpense:checked ~ .seg-expense, #typeIncome:checked ~ .seg-income {
        background: var(--panel-2); color: var(--text); box-shadow: inset 0 0 0 1px var(--border);
    }
    .seg-expense { order: 1; } .seg-income { order: 2; } #typeExpense { order: 0; } #typeIncome { order: 1; }

    .budget-chip-card {
        flex: 0 0 210px; scroll-snap-align: start;
        background: var(--panel); border: 1px solid var(--border); border-radius: var(--radius-md);
        padding: 14px;
    }
    .budget-track { height: 7px; border-radius: 999px; background: var(--bg-elevated); overflow: hidden; }
    .budget-fill { height: 100%; border-radius: 999px; background: var(--accent); }
    .budget-fill.warn { background: #ffd166; }
    .budget-fill.over { background: var(--expense); }
    .btn-icon-ghost { background: none; border: none; color: var(--text-faint); padding: 2px 6px; }

    .txn-list { display: flex; flex-direction: column; gap: 8px; }
    .txn-row {
        display: flex; align-items: center; gap: 12px;
        background: var(--panel); border: 1px solid var(--border); border-radius: var(--radius-md);
        padding: 12px 14px; cursor: pointer;
    }
    .txn-avatar {
        width: 44px; height: 44px; border-radius: 14px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
    }
    .txn-avatar-img { width: 44px; height: 44px; border-radius: 14px; object-fit: cover; flex-shrink: 0; cursor: pointer; }
    .chip-c1 { background: var(--accent-soft); }
    .chip-c2 { background: var(--income-soft); }
    .chip-c3 { background: var(--expense-soft); }
    .chip-c4 { background: var(--lavender-soft); }
    .chip-c5 { background: rgba(139, 191, 255, .16); }
    .txn-info { flex: 1; min-width: 0; }
    .txn-title { font-weight: 700; font-size: .92rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .txn-meta { color: var(--text-faint); font-size: .76rem; }
    .txn-right { display: flex; align-items: center; gap: 4px; }
    .txn-amount { font-weight: 700; font-size: .9rem; white-space: nowrap; }
    .text-income { color: var(--income); }
    .text-expense { color: var(--expense); }

    .dropdown-dark { background: var(--panel-2); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .dropdown-dark .dropdown-item { color: var(--text); font-size: .88rem; padding: 10px 14px; }
    .dropdown-dark .dropdown-item:hover { background: var(--bg-elevated); color: var(--text); }

    /* ===== Catat Cepat ===== */
    .quick-add-box {
        background: linear-gradient(150deg, var(--accent-soft), transparent 70%);
        border: 1px solid rgba(255,177,94,.3);
        border-radius: var(--radius-md);
        padding: 14px;
    }
    .quick-hint { color: var(--text-faint); font-size: .74rem; margin-top: 6px; }
    #quickText { background: var(--bg); }
    .quick-preview {
        margin-top: 12px; background: var(--income-soft); border: 1px solid rgba(126,232,176,.3);
        border-radius: var(--radius-sm); padding: 12px 14px;
        animation: quickPop .25s ease;
    }
    @keyframes quickPop { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }
    .quick-preview-title { color: var(--income); font-weight: 700; font-size: .88rem; margin-bottom: 6px; }
    .quick-preview-row { font-size: .82rem; color: var(--text); margin-bottom: 2px; }
    .quick-preview-row span { font-weight: 600; }
    .quick-preview-note { color: var(--text-faint); font-size: .74rem; margin-top: 6px; }

    .divider-or {
        display: flex; align-items: center; gap: 10px; margin: 16px 0;
        color: var(--text-faint); font-size: .76rem;
    }
    .divider-or::before, .divider-or::after { content: ''; flex: 1; height: 1px; background: var(--border); }

    .field-flash { animation: fieldFlash 1s ease; }
    @keyframes fieldFlash {
        0% { box-shadow: 0 0 0 3px var(--accent-soft); border-color: var(--accent); }
        100% { box-shadow: none; }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    // ===== Catat Cepat: parse teks bebas lalu isi form otomatis =====
    (function () {
        const quickText   = document.getElementById('quickText');
        const quickBtn    = document.getElementById('quickParseBtn');
        const quickIcon   = document.getElementById('quickParseIcon');
        const quickPreview = document.getElementById('quickPreview');
        const csrfToken   = document.querySelector('meta[name="csrf-token"]').content;
        const addForm     = document.querySelector('#addModal form');

        function flash(el) {
            el.classList.remove('field-flash');
            void el.offsetWidth; // restart animasi
            el.classList.add('field-flash');
        }

        function formatRupiah(n) {
            return 'Rp ' + Number(n).toLocaleString('id-ID');
        }

        function formatTanggal(isoDate) {
            const d = new Date(isoDate + 'T00:00:00');
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
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

                if (!res.ok) throw new Error('Gagal parsing');
                const data = await res.json();

                // Isi field form
                addForm.querySelector('[name="title"]').value = data.title;
                addForm.querySelector('[name="category"]').value = data.category;
                addForm.querySelector('[name="amount"]').value = data.amount;
                addForm.querySelector('[name="date"]').value = data.date;

                const typeRadio = addForm.querySelector('#type' + (data.type === 'pemasukan' ? 'Income' : 'Expense'));
                typeRadio.checked = true;

                // Highlight field yang baru terisi
                ['title', 'category', 'amount', 'date'].forEach(function (name) {
                    flash(addForm.querySelector('[name="' + name + '"]'));
                });

                // Tampilkan kartu preview
                document.getElementById('qpType').innerText = data.type === 'pemasukan' ? 'Pemasukan' : data.category;
                document.getElementById('qpTitle').innerText = data.title;
                document.getElementById('qpAmount').innerText = formatRupiah(data.amount);
                document.getElementById('qpDate').innerText = formatTanggal(data.date);
                quickPreview.classList.remove('d-none');

            } catch (e) {
                alert('Gagal memproses teks. Coba isi manual di bawah ya.');
            } finally {
                quickBtn.disabled = false;
                quickIcon.className = 'bi bi-magic';
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

    document.querySelectorAll('.btn-delete-trigger').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('deleteForm').action = this.dataset.action;
            document.getElementById('deleteModalTitle').innerText = this.dataset.title;
        });
    });

    const palette = ['#ffb15e', '#c9aeff', '#7ee8b0', '#ff8b94', '#8bbfff', '#ffd166'];

    new Chart(document.getElementById('chartCategory'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($chartCategoryLabels) !!},
            datasets: [{
                data: {!! json_encode($chartCategoryValues) !!},
                backgroundColor: palette,
                borderColor: '#1c1926',
                borderWidth: 3,
            }]
        },
        options: {
            plugins: { legend: { position: 'bottom', labels: { color: '#f2eef8', boxWidth: 10, font: { size: 11, family: 'Plus Jakarta Sans' } } } }
        }
    });

    new Chart(document.getElementById('chartTrend'), {
        type: 'line',
        data: {
            labels: {!! json_encode($trendLabels) !!},
            datasets: [
                { label: 'Pemasukan', data: {!! json_encode($trendPemasukan) !!}, borderColor: '#7ee8b0', backgroundColor: 'rgba(126,232,176,0.12)', tension: 0.35, fill: true },
                { label: 'Pengeluaran', data: {!! json_encode($trendPengeluaran) !!}, borderColor: '#ff8b94', backgroundColor: 'rgba(255,139,148,0.12)', tension: 0.35, fill: true }
            ]
        },
        options: {
            plugins: { legend: { labels: { color: '#f2eef8', font: { family: 'Plus Jakarta Sans' } } } },
            scales: {
                x: { ticks: { color: '#96909f' }, grid: { color: '#2e2a3d' } },
                y: { ticks: { color: '#96909f' }, grid: { color: '#2e2a3d' } }
            }
        }
    });
</script>
@endsection
