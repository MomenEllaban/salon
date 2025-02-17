<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
  <div class="offcanvas-header border-bottom">
    @if(isset($title))
      {{ $title }}
    @endif
    <button type="button" class="btn-close mb-1" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    {{ $slot }}
  </div>
  <div class="offcanvas-body">
    @if(isset($footer))
      {{$footer}}
    @endif
  </div>
</div>
