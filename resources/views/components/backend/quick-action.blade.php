<form action="{{$url ?? ''}}" id="quick-action-form" class="form-disabled d-flex gap-3 align-items-stretch flex-md-row flex-column">
  @csrf
  {{$slot}}
  <input type="hidden" name="message_change-is_featured" value="Are you sure you want to perform this action?">
  <input type="hidden" name="message_change-status" value="Are you sure you want to perform this action?">
  <input type="hidden" name="message_delete" value="Are you sure you want to delete it?">
  <button class="btn btn-gray" id="quick-action-apply">{{ __('messages.apply') }}</button>
</form>
