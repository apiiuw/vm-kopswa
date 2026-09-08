@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-4">
        
        {{-- Total & Info Text --}}
        <div>
            <p class="text-xs text-slate-600 font-medium">
                Menampilkan 
                @if ($paginator->firstItem())
                    <span class="font-bold text-black">{{ $paginator->firstItem() }}</span>
                    sampai 
                    <span class="font-bold text-black">{{ $paginator->lastItem() }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
                dari total 
                <span class="font-bold text-[#015471]">{{ $paginator->total() }}</span> 
                unit armada
            </p>
        </div>

        {{-- Pagination Buttons --}}
        <div class="flex items-center gap-1.5">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-slate-300 border border-slate-200 cursor-not-allowed bg-slate-50" aria-disabled="true">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-[#015471] border border-slate-200 bg-white hover:bg-[#e6f0f3] hover:border-[#015471]/30 transition shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- Three Dots Separator --}}
                @if (is_string($element))
                    <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-slate-400">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-xs font-black text-white bg-[#FF7A3D] shadow-sm">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-xs font-bold text-slate-700 border border-slate-200 bg-white hover:border-[#015471] hover:text-[#015471] hover:bg-[#e6f0f3]/50 transition shadow-2xs">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-[#015471] border border-slate-200 bg-white hover:bg-[#e6f0f3] hover:border-[#015471]/30 transition shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-slate-300 border border-slate-200 cursor-not-allowed bg-slate-50" aria-disabled="true">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </span>
            @endif
        </div>

    </nav>
@endif
