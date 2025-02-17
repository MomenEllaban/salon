<div class="d-flex gap-2 align-items-center">

    @hasPermission('delete_review')
        <a href="{{ route('backend.employees.destroy_review', $data->id) }}"
            id="delete-{{ $module_name }}-{{ $data->id }}" class="btn btn-soft-danger btn-sm" data-type="ajax"
            data-method="DELETE" data-token="{{ csrf_token() }}" data-bs-toggle="tooltip" title="{{ __('messages.delete') }}"
            data-confirm="{{ __('messages.are_you_sure?', ['module' => __('employee.review'), 'name' => $data->user->full_name]) }}">

            <i class="fa-solid fa-trash"></i></a>
    @endhasPermission
</div>
