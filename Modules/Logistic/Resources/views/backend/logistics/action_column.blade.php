

<div class="d-flex gap-2 align-items-center">
  <!-- <button type='button' data-assign-module="{{ $data->id }}" data-assign-target='#Employee_change_password' data-assign-event='employee_assign' class='btn btn-soft-info btn-sm rounded text-nowrap' data-bs-toggle="tooltip" title="Change Password"><i class="fas fa-key"></i></button> -->

          <button type="button" class="btn btn-soft-primary btn-sm" data-crud-id="{{$data->id}}" title="{{ __('messages.edit') }} " data-bs-toggle="tooltip"> <i class="fa-solid fa-pen-clip"></i></button>

          <a href="{{route("backend.logistics.destroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn btn-soft-danger btn-sm" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.delete')}}" data-confirm="{{ __('messages.are_you_sure?') }}"> <i class="fa-solid fa-trash"></i></a>

  </div>



