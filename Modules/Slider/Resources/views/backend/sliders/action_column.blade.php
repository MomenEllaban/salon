<div class="d-flex gap-2 align-items-center">
    <!-- <button type='button' data-assign-module="{{ $data->id }}" data-assign-target='#Employee_change_password' data-assign-event='employee_assign' class='btn btn-soft-info btn-sm rounded text-nowrap' data-bs-toggle="tooltip" title="Change Password"><i class="fas fa-key"></i></button> -->
    @hasPermission('edit_app_banner')
        <button type="button" class="btn btn-soft-primary btn-sm" data-crud-id="{{ $data->id }}"
            title="{{ __('messages.edit') }} " data-bs-toggle="tooltip"> <i class="fa-solid fa-pen-clip"></i></button>
    @endhasPermission
    @hasPermission('delete_app_banner')
        <a href="{{ route('backend.app-banners.destroy', $data->id) }}" id="delete-{{ $module_name }}-{{ $data->id }}"
            class="btn btn-soft-danger btn-sm" data-type="ajax" data-method="DELETE" data-token="{{ csrf_token() }}"
            data-bs-toggle="tooltip" title="{{ __('messages.delete') }}"
            data-confirm="{{ __('messages.are_you_sure?', ['module' => __('slider.singular_title'), 'name' => $data->name]) }}">
            <i class="fa-solid fa-trash"></i></a>
    @endhasPermission
</div>
