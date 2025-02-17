<div class="d-flex gap-2 align-items-center">
  @hasPermission('edit_notification_template')
    <a href="{{route("backend.notification-templates.edit", $data->id)}}" class="btn btn-soft-primary btn-sm" data-bs-toggle="tooltip" title="{{ __('messages.edit') }} "> <i class="fa-solid fa-pen-clip"></i></a>
  @endhasPermission
  {{-- @hasPermission('delete_notification_template')
    <a href="{{route("backend.notification-templates.destroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn btn-soft-danger btn-sm" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.delete')}}" data-confirm="{{ __('messages.are_you_sure?') }}"> <i class="fa-solid fa-trash"></i></a>
  @endhasPermission --}}

</div>
