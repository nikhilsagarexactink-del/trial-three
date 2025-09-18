@if ($paginator->hasPages())
<div class="pagination_section" id="paginationList">
    <div class="d-sm-flex align-items-center">
        <!-- <div class="page_counter">
            <p class="mb-0">Page <span class="ml-1 left">{{$paginator->currentPage()}}</span><span class="mx-1">of</span><span class="right">{{$paginator->lastPage()}}</span></p>
        </div> -->
        <div class="pagination_right ml-sm-auto">
            <nav aria-label="Page navigation example">
                <ul class="pagination mb-0">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                    <li class="page-item disabled"><a class="page-link" href="javascript:void(0);">{{__('Previous')}}</a></li>
                    @else
                    <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">{{__('Previous')}}</a></li>
                    @endif

                    @if($paginator->currentPage() > 2)
                    <li class="page-item"><a class="page-link" href="{{ $paginator->url(1) }}">1</a></li>
                    @endif
                    @if($paginator->currentPage() > 3)
                    <li class="page-item disabled"><a class="page-link" href="javascript:void(0);">...</a></li>
                    @endif

                    @foreach(range(1, $paginator->lastPage()) as $i)
                    @if($i >= $paginator->currentPage() - 1 && $i <= $paginator->currentPage() + 1)
                    @if ($i == $paginator->currentPage())
                    <li class="page-item active disabled"><a class="page-link active" href="javascript:void(0);">{{ $i }}</a></li>
                    @else
                    <li class="page-item"><a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a></li>
                    @endif
                    @endif
                    @endforeach
                    
                    @if($paginator->currentPage() < $paginator->lastPage() - 2)
                    <li class="page-item disabled"><a class="page-link" href="javascript:void(0);">...</a></li>
                    @endif
                    @if($paginator->currentPage() < $paginator->lastPage() - 1)
                    <li class="page-item"><a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a></li>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">{{__('Next')}}</a></li>
                    @else
                    <li class="page-item disabled"><a class="page-link" href="javascript:void(0);">{{__('Next')}}</a></li>
                    @endif
                </ul>
            </nav>
        </div>
        
    </div>
</div>
@endif
