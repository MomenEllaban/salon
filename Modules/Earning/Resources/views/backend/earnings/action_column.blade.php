<div class="text-end d-flex gap-2 align-items-center">
    @if($data['total_pay'] > 0)
      <span  class="btn btn-soft-primary btn-sm"  data-crud-id="{{ $data->id }}" title="{{ __('messages.payment') }}" data-bs-toggle="tooltip"><i class='fas fa-money-bill'></i></span>
    @else
      <span  class="px-2">-</span>
    @endif
</div>



