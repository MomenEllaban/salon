<div class="d-flex align-items-center">
  <div class="avatar avatar-md">
        <img class="avatar avatar-50 rounded-pill" src="{{optional($data->user)->profile_image}}" alt="avatar" />
    </div>
    <div class="ms-2">
        <h6 class="fs-sm mb-0">{{ optional($data->user)->full_name }}</h6>
        <span class="text-muted fs-sm"> {{ optional($data->user)->mobile ?? '-' }}</span>
    </div>
</div>
