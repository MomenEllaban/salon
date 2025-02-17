<div class="d-flex gap-2 align-items-center">
    @hasPermission('service_gallery')
        <button type='button' data-gallery-module="{{ $data->id }}" data-gallery-target='#service-gallery-form'
            data-gallery-event='service_gallery' class='btn btn-soft-info btn-sm rounded text-nowrap' data-bs-toggle="tooltip"
            title="{{ __('messages.gallery_for_service') }}"><i class="fa-solid fa-images"></i></button>
    @endhasPermission
    @hasPermission('edit_service')
        <button type="button" class="btn btn-soft-primary btn-sm" data-crud-id="{{ $data->id }}"
            title="{{ __('messages.edit') }} " data-bs-toggle="tooltip"> <i class="fa-solid fa-pen-clip"></i></button>
    @endhasPermission
    @hasPermission('delete_service')
        <a href="{{ route("backend.$module_name.destroy", $data->id) }}" id="delete-{{ $module_name }}-{{ $data->id }}"
            class="btn btn-soft-danger btn-sm" data-type="ajax" data-method="DELETE" data-token="{{ csrf_token() }}"
            data-bs-toggle="tooltip" title="{{ __('messages.delete') }}"
            data-confirm="{{ __('messages.are_you_sure?', ['module' => __('service.singular_title'), 'name' => $data->name]) }}">
            <i class="fa-solid fa-trash"></i></a>
    @endhasPermission
</div>
