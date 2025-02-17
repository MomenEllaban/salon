@if ($data->orderGroup->type == 'booking')
    <span class="badge bg-soft-danger rounded-pill text-capitalize">
         Booking 
    </span>
@else
    <span class="badge bg-soft-primary rounded-pill text-capitalize">
       Online
    </span>
@endif