
{{-- Footer Section Start --}}
<footer class="footer pr-hide sticky {{ getCustomizationSetting('footer_style') }}">
  <div class="footer-body">
      <div class="left-panel">
          <a href="{{ route('backend.home') }}">{{setting('app_name')}}</a>.
          {{ setting('copyright_text') }}
      </div>
      <div class="center-panel">
          {!! setting('footer_text') !!}
      </div>
      <div class="end-panel">
        {!! setting('ui_text') !!}
      </div>
  </div>
</footer>
