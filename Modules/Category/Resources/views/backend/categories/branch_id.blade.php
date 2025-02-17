<div class="d-flex gap-3 align-items-center">
  <img src="{{ $data->feature_image ?? default_user_avatar() }}" alt="avatar" class="avatar avatar-40 rounded-pill">
  <div>
      <h6 class="m-0">
          <a href="{{ $link }}">{{ $data->name ?? default_user_name() }}</a>
      </h6>
  </div>
</div>