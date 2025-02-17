<select name="branch_for" class="select2 change-select" data-token="{{csrf_token()}}" data-url="{{route('backend.branch.update_select', ['id' => $data->id, 'action_type' => 'update-branch-for'])}}" style="width: 100%; position: relative !important;">
  @foreach ($branch_for_list as $key => $value )
    <option value="{{$key}}" {{$data->branch_for == $key ? 'selected' : ''}}>{{$value}}</option>
  @endforeach
</select>
