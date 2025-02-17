@if ($data->payment_status == 'unpaid')
    <span class="badge bg-soft-danger rounded-pill text-capitalize">
        {{ $data->payment_status }}
    </span>
@else
    <span class="badge bg-soft-primary rounded-pill text-capitalize">
        {{ $data->payment_status }}
    </span>
@endif
