@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex items-center justify-between w-full p-2 rounded-2xl bg-white/85 dark:bg-slate-900/70 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl text-slate-400 dark:text-slate-600 bg-slate-100/60 dark:bg-slate-800/40 cursor-not-allowed select-none">
                <i class="bi bi-chevron-left text-xs"></i>
                <span>Sebelumnya</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 shadow-xs active:scale-95 transition-all">
                <i class="bi bi-chevron-left text-xs"></i>
                <span>Sebelumnya</span>
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 shadow-xs active:scale-95 transition-all">
                <span>Selanjutnya</span>
                <i class="bi bi-chevron-right text-xs"></i>
            </a>
        @else
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl text-slate-400 dark:text-slate-600 bg-slate-100/60 dark:bg-slate-800/40 cursor-not-allowed select-none">
                <span>Selanjutnya</span>
                <i class="bi bi-chevron-right text-xs"></i>
            </span>
        @endif
    </nav>
@endif
