<div class="d-flex gap-2 align-items-center">

    <button type="button" class="btn btn-soft-primary btn-sm" data-crud-id="{{ $data->id }}"
        title="{{ __('messages.show') }} " data-bs-toggle="tooltip"> <i class="fa-solid fa-eye"></i></button>


    <a href="{{ route('backend.reviews.destroy', $data->id) }}" id="delete-{{ $module_name }}-{{ $data->id }}"
        class="btn btn-soft-danger btn-sm" data-type="ajax" data-method="DELETE" data-token="{{ csrf_token() }}"
        data-bs-toggle="tooltip" title="{{ __('messages.delete') }}"
        data-confirm="{{ __('messages.are_you_sure?', ['module' => $module_name, 'name' => $data->user->full_name]) }}">
        <i class="fa-solid fa-trash"></i></a>

</div>
