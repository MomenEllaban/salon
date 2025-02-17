<div class="text-end d-flex gap-2 align-items-center">
    {{-- @can('edit_' . $module_name)
        <button type="button" class="btn btn-soft-primary btn-sm" data-crud-id="{{$data->id}}" title="{{ __('messages.edit') }}" data-bs-toggle="tooltip"> <i class="fa-solid fa-pen-clip"></i></button>
    @endcan --}}
    @if ($data->status == 'completed')
        <a href="{{ route('backend.bookings.invoice', ['id' => $data->id]) }}"
            class="btn btn-sm btn-icon btn-soft-warning" data-bs-toggle="tooltip" data-bs-placement="top"
            title="View Details">
            <i class="fa-solid fa-eye"></i>
        </a>
    @endif
    @hasPermission('delete_booking')
        <a href="{{ route("backend.$module_name.destroy", $data->id) }}" id="delete-{{ $module_name }}-{{ $data->id }}"
            class="btn btn-soft-danger btn-sm" data-type="ajax" data-method="DELETE" data-token="{{ csrf_token() }}"
            data-bs-toggle="tooltip" title="{{ __('messages.delete') }}"
            data-confirm="{{ __('messages.are_you_sure?', ['module' => __('booking.singular_title'), 'name' => $data->user->full_name ?? default_user_name()]) }}">
            <i class="fa-solid fa-trash"></i></a>
    @endhasPermission
</div>
