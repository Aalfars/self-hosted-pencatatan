@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="w-full flex items-center justify-between">
        {{-- ==================== MOBILE VIEW (sm:hidden) ==================== --}}
        <div class="flex sm:hidden w-full items-center justify-between gap-2 p-2 rounded-2xl bg-white/85 dark:bg-slate-900/70 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1 px-3 py-2 text-xs font-semibold rounded-xl text-slate-400 dark:text-slate-600 bg-slate-100/60 dark:bg-slate-800/40 cursor-not-allowed select-none">
                    <i class="bi bi-chevron-left text-xs"></i>
                    <span>Prev</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-1 px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 shadow-xs active:scale-95 transition-all">
                    <i class="bi bi-chevron-left text-xs"></i>
                    <span>Prev</span>
                </a>
            @endif

            {{-- Current Page Status Indicator --}}
            <div class="text-xs font-semibold text-slate-600 dark:text-slate-400 tabular-nums text-center">
                <span>Hal. <strong class="text-slate-900 dark:text-white">{{ $paginator->currentPage() }}</strong> / {{ $paginator->lastPage() }}</span>
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-1 px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 shadow-xs active:scale-95 transition-all">
                    <span>Next</span>
                    <i class="bi bi-chevron-right text-xs"></i>
                </a>
            @else
                <span class="inline-flex items-center gap-1 px-3 py-2 text-xs font-semibold rounded-xl text-slate-400 dark:text-slate-600 bg-slate-100/60 dark:bg-slate-800/40 cursor-not-allowed select-none">
                    <span>Next</span>
                    <i class="bi bi-chevron-right text-xs"></i>
                </span>
            @endif
        </div>

        {{-- ==================== TABLET & DESKTOP VIEW (hidden sm:flex) ==================== --}}
        <div class="hidden sm:flex sm:items-center sm:justify-between w-full p-2.5 sm:p-3 rounded-2xl bg-white/85 dark:bg-slate-900/70 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            {{-- Results Summary --}}
            <div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                    Menampilkan
                    <span class="font-bold text-slate-900 dark:text-white tabular-nums">{{ $paginator->firstItem() }}</span>
                    sampai
                    <span class="font-bold text-slate-900 dark:text-white tabular-nums">{{ $paginator->lastItem() }}</span>
                    dari
                    <span class="font-bold text-slate-900 dark:text-white tabular-nums">{{ $paginator->total() }}</span>
                    transaksi
                </p>
            </div>

            {{-- Pagination Navigation Buttons --}}
            <div class="flex items-center gap-1">
                {{-- Previous Arrow --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="Halaman Sebelumnya" class="w-8 h-8 rounded-xl flex items-center justify-center text-xs text-slate-300 dark:text-slate-600 cursor-not-allowed select-none">
                        <i class="bi bi-chevron-left"></i>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman Sebelumnya" class="w-8 h-8 rounded-xl flex items-center justify-center text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-transparent hover:border-slate-200 dark:hover:border-slate-700 transition-colors">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                @endif

                {{-- Numbered Page Links --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span aria-disabled="true" class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-semibold text-slate-400 dark:text-slate-500">
                            {{ $element }}
                        </span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-bold bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/25 tabular-nums">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-transparent hover:border-slate-200 dark:hover:border-slate-700 transition-colors tabular-nums" aria-label="Ke Halaman {{ $page }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Arrow --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman Selanjutnya" class="w-8 h-8 rounded-xl flex items-center justify-center text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-transparent hover:border-slate-200 dark:hover:border-slate-700 transition-colors">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="Halaman Selanjutnya" class="w-8 h-8 rounded-xl flex items-center justify-center text-xs text-slate-300 dark:text-slate-600 cursor-not-allowed select-none">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
