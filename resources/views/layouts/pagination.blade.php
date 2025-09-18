 @if(!empty($data) && $data->total() > 10)
 <div class="common-pagination d-flex align-items-center justify-content-end">
     <div class="pagination-item ">
         <ul class="pagination mb-0">
             {{ $data->links('layouts.pagination.default') }}
         </ul>
     </div>
 </div>
 @endif
