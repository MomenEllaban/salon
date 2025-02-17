<div class="text-end d-flex gap-2 align-items-center">
    @can('edit_'.$module_name)
        <button type="button" class="btn btn-soft-primary btn-sm" data-crud-id="{{$data->id}}" title="{{ __('messages.edit') }} " data-bs-toggle="tooltip"> <i class="fa-solid fa-pen-clip"></i></button>
    @endcan
    <a href="{{route("backend.$module_name.show", $data->id)}}" class="btn btn-soft-success btn-sm" data-bs-toggle="tooltip" title="{{__('labels.backend.show')}}"><i class="fas fa-eye"></i></a>
    @can('delete_'.$module_name)
        <a href="{{route("backend.$module_name.destroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn btn-soft-danger btn-sm" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.delete')}}" data-confirm="{{ __('messages.are_you_sure?') }}"> <i class="fa-solid fa-trash"></i></a>
    @endcan
</div>
