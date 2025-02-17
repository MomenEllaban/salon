@if ($data->delivery_status == 'delivered')
    <span class="badge bg-soft-primary rounded-pill text-capitalize">
        {{ $data->delivery_status }}
    </span>
@elseif($data->delivery_status == 'cancelled')
    <span class="badge bg-soft-danger rounded-pill text-capitalize">
        {{ Str::title(Str::replace('_', ' ', $data->delivery_status)) }}
    </span>
@else
    <span class="badge bg-soft-info rounded-pill text-capitalize">
        {{ Str::title(Str::replace('_', ' ', $data->delivery_status))  }}
    </span>
@endif
