<div class="d-flex gap-3 align-items-center">
  <img src="{{ $data->feature_image ?? default_user_avatar() }}" alt="avatar" class="avatar avatar-40 rounded-pill">
  <div>
    <p class="m-0">{{ $data->name ?? default_user_name() }}</p>
    @if(isset($email))
    <small>{{ $email  }}</small>
    @endif
  </div>
</div>