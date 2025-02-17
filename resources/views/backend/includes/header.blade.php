<?php
$notifications_count = optional(auth()->user())->unreadNotifications->count();
?>
<nav class="pr-hide nav navbar navbar-expand-xl navbar-light iq-navbar header-hover-menu left-border {{ !empty(getCustomizationSetting('navbar_show')) ? getCustomizationSetting('navbar_show') : '' }} {{ getCustomizationSetting('header_navbar') }}">
    <div class="container-fluid navbar-inner">
        <a href="{{route('backend.dashboard')}}" class="navbar-brand">
            <div class="logo-main">
                <div class="logo-mini d-none">
                    <img  src="{{asset(setting('mini_logo'))}}" height="30" alt="{{ app_name() }}">
                </div>
                <div class="logo-normal">
                    <img  src="{{asset(setting('logo'))}}" height="30" alt="{{ app_name() }}">
                    {{-- <h4 class="logo-title d-none d-sm-block">{{app_name()}}</h4> --}}
                </div>
            </div>
        </a>
        <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
            <i class="icon d-flex">
               <svg width="20px" viewBox="0 0 24 24">
                  <path fill="currentColor" d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z" />
               </svg>
            </i>
        </div>
        @hasPermission('menu_builder_header')
        <div class="d-flex align-items-center justify-content-between product-offcanvas">
            @php
              $hmenu = new \App\Http\Middleware\GenerateMenus();
              $hmenu = $hmenu->handle('hmenu', 'horizontal', 'H_ARRAY_MENU');
            @endphp
            <div class="offcanvas offcanvas-end shadow-none iq-product-menu-responsive" tabindex="-1"
                id="offcanvasBottom">
                <div class="offcanvas-body">
                    <ul class="iq-nav-menu list-unstyled">
                        @include(('vendor.laravel-menu.custom-menu-items'), ['items' => $hmenu->roots()])
                    </ul>
                </div>
            </div>
         </div>
         @endhasPermission
         <div class="d-flex align-items-center">
            <button id="navbar-toggle" class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
               <span class="navbar-toggler-icon">
               <span class="navbar-toggler-bar bar1 mt-1"></span>
               <span class="navbar-toggler-bar bar2"></span>
               <span class="navbar-toggler-bar bar3"></span>
               </span>
            </button>
         </div>
         <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="mb-2 navbar-nav ms-auto align-items-center navbar-list mb-lg-0">
              <li class="nav-item dropdown me-0 me-xl-3">
                <div class="d-flex align-items-center mr-2 iq-font-style" role="group" aria-label="First group">
                    <input type="radio" class="btn-check" name="theme_font_size" value="theme-fs-sm" id="font-size-sm" checked>
                    <label for="font-size-sm" class="btn btn-border border-0 btn-icon btn-sm" data-bs-toggle="tooltip"
                      title="Font size 14px" data-bs-placement="bottom">
                      <span class="mb-0 h6" style="color: inherit !important;">A</span>
                    </label>
                    <input type="radio" class="btn-check" name="theme_font_size" value="theme-fs-md" id="font-size-md">
                    <label for="font-size-md" class="btn btn-border border-0 btn-icon" data-bs-toggle="tooltip"
                      title="Font size 16px" data-bs-placement="bottom">
                      <span class="mb-0 h4" style="color: inherit !important;">A</span>
                    </label>
                    <input type="radio" class="btn-check" name="theme_font_size" value="theme-fs-lg" id="font-size-lg">
                    <label for="font-size-lg" class="btn btn-border border-0 btn-icon" data-bs-toggle="tooltip"
                      title="Font size 18px" data-bs-placement="bottom">
                      <span class="mb-0 h2" style="color: inherit !important;">A</span>
                    </label>
                </div>
              </li>

              @if(isset($is_single_branch) && !$is_single_branch)
                <li class="nav-item dropdown me-0 me-xl-1 pe-3 border-end iq-dropdown">
                  <a href="javascript:void(0)" class="nav-link p-0" data-bs-toggle="dropdown">
                    @if(isset($selected_branch))
                      <div class="iq-sub-card">
                        <div class="d-flex align-items-center">
                          <span class="iq-media-group">
                              <div class="icon iq-icon-box-3 rounded-pill">{{substr($selected_branch->name, 0, 1)}}</div>
                          </span>
                          <div class="ms-3 flex-grow-1">
                              <h6 class="mb-0 ">{{$selected_branch->name}}</h6>
                          </div>
                        </div>
                      </div>
                    @else
                      <div class="iq-sub-card">
                        <div class="d-flex align-items-center">
                          <span class="iq-media-group">
                              <div class="icon iq-icon-box-3 rounded-pill">{{substr('A', 0, 1)}}</div>
                          </span>
                          <div class="ms-3 flex-grow-1">
                              <h6 class="mb-0 ">{{ __('branch.all_branches') }}</h6>
                          </div>
                        </div>
                      </div>
                    @endif
                  </a>
                  @role('admin')
                  <ul class="p-0 sub-drop dropdown-menu dropdown-menu-end iq-sub-drop">
                    <div class="m-0 shadow-none card">
                      <div class="py-3 card-header d-flex justify-content-between border-bottom">
                        <div class="header-title">
                          <h5 class="mb-0">{{ __('messages.branches') }}</h5>
                        </div>
                      </div>
                      <div class="p-0 card-body max-17 scroll-thin">
                        <form action="{{ route('backend.reset-branch') }}" method="post">
                          @csrf
                          <div class="iq-sub-card {{ !isset($selected_branch) ? 'bg-soft-primary' : '' }} iq-branch-dropdown border-bottom">
                            <div class="d-flex align-items-center">
                              <span class="iq-media-group">
                                  <div class="icon iq-icon-box-3 rounded-pill">{{substr('A',0,1)}}</div>
                              </span>
                              <div class="ms-3 flex-grow-1">
                                  <h6 class="mb-0 ">All Branches</h6>
                              </div>
                              <button type="submit" class="btn btn-primary btn-sm rounded">
                                <span class="btn-inner">
                                  Apply
                                </span>
                            </button>
                            </div>
                          </div>
                        </form>
                        @isset($auth_user_branches)
                          @foreach ($auth_user_branches as $key => $branch)
                            <form action="{{route('backend.set-current-branch', $branch->id)}}" method="post">
                              @csrf
                              <div class="iq-sub-card {{ isset($selected_branch) && $branch->id == $selected_branch->id ? 'bg-soft-primary' : '' }} iq-branch-dropdown border-bottom">
                                <div class="d-flex align-items-center">
                                  <span class="iq-media-group">
                                      <div class="icon iq-icon-box-3 rounded-pill">{{substr($branch->name,0,1)}}</div>
                                  </span>
                                  <div class="ms-3 flex-grow-1">
                                      <h6 class="mb-0 ">{{$branch->name}}</h6>
                                  </div>
                                  <button type="submit" class="btn btn-primary btn-sm rounded">
                                    <span class="btn-inner">
                                      Apply
                                    </span>
                                </button>
                                </div>
                              </div>
                            </form>
                          @endforeach
                        @endisset
                      </div>
                    </div>
                  </ul>
                  @endrole
                </li>
                @endif
                  <li class="nav-item theme-scheme-dropdown dropdown iq-dropdown">
                      <a href="javascript:void(0)" class="nav-link d-flex align-items-center change-mode" data-change-mode="{{ (auth()->user()->user_setting['theme_scheme'] ?? 'light') ==  'dark' ? 'light' : 'dark' }}" id="mode-drop" style="color: inherit !important;">
                        <i class="fa-solid fa-sun mode-icons light-mode"></i>
                        <i class="fa-solid fa-moon mode-icons dark-mode"></i>
                      </a>
                  </li>
                  <li class="nav-item dropdown">
                      <a class="nav-link d-flex align-items-center" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" style="color: inherit !important;">
                          <i class="fa-solid fa-globe me-1"></i>{{strtoupper(App::getLocale())}}
                      </a>
                      <div class="dropdown-menu dropdown-menu-end">
                          <div class="dropdown-header bg-soft-primary py-2 rounded">
                              <div class="fw-semibold">{{ __('messages.change_language') }}</div>
                          </div>
                          @foreach(config('app.available_locales') as $locale => $title)
                          <a class="dropdown-item" href="{{route("language.switch", $locale)}}">
                              {{ $title }}
                          </a>
                          @endforeach
                      </div>
                  </li>
                  <li class="nav-item dropdown iq-dropdown">
                    <a class="nav-link btn btn-primary btn-icon btn-sm rounded-pill btn-action" data-bs-toggle="dropdown" href="#">
                        <div class="iq-sub-card">
                          <div class="d-flex align-items-center notification_list">
                            <span class="btn-inner">
                              <i class="fa-solid fa-bell"></i>
                            </span>
                            @if($notifications_count>0)<span class="notification-alert">{{$notifications_count}}</span>@endif
                          </div>
                        </div>
                    </a>
                    <ul class="p-0 sub-drop dropdown-menu dropdown-menu-end">
                      <div class="m-0 shadow-none card notification_data"></div>
                    </ul>
                  </li>

                  <li class="nav-item dropdown">
                      <a class="nav-link py-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                          <div class="avatar avatar-md">
                              <img class="avatar avatar-40 img-fluid rounded-pill" src="{{ asset(user_avatar()) }}" alt="{{ auth()->user()->name ?? default_user_name() }}" loading="lazy">
                          </div>
                      </a>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <div class="dropdown-header bg-soft-primary py-2 rounded">
                          <div class="d-flex gap-2">
                              <img class="avatar avatar-40 img-fluid rounded-pill" src="{{ asset(user_avatar()) }}"/>
                              <div class="d-flex flex-column align-items-start">
                                  <h6 class="m-0 text-primary">{{ Auth::user()->full_name ?? default_user_name() }}</h6>
                                  <small class="text-muted">{{ Auth::user()->email ?? 'abc@email.com' }}</small>
                              </div>
                          </div>
                        </div>
                        <li>
                          <a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ route('backend.my-profile') }}">{{ __('messages.myprofile') }}</a>
                        </li>
                        @role('admin')
                        <li>
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ route('backend.settings') }}">
                                @lang('settings.title') <i class="fa-solid fa-gear"></i>
                            </a>
                        </li>
                        <hr class="dropdown-divider" />
                        @endrole
                          <li>
                              <a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                  @lang('messages.logout')<i class="fa-solid fa-right-from-bracket"></i>
                              </a>
                          </li>
                          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> @csrf </form>
                      </ul>
                  </li>
            </ul>
         </div>
    </div>
</nav>

@push('after-scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('.change-mode').on('click', function() {
          const value = $(this).data('change-mode')
          $(this).data('change-mode' , value == 'dark' ? 'light' : 'dark')
          fetch("{{ route('backend.setUserSetting') }}", {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify({ settings: {theme_scheme: value} })
            })
            .then((res) => res.json())
            .then((data) => {
            })
            if(value !== 'dark') {
              $('body').removeClass('dark');
            } else {
              $('body').addClass('dark');
            }
        });

        $('input[name="theme_font_size"]').on('change', function() {
          const font = $('[name="theme_font_size"]').map(function(){
            return $(this).attr('value')
          }).get();
          $('html').removeClass(font).addClass($(this).val());
        });
        $('.notification_list').on('click',function(){
            notificationList();
        });
    });



     function notification_count(){

      var url = "{{ route('notification.counts') }}";
        $.ajax({
            type: 'get',
            url: url,
           success: function(res){

            console.log(res);


            }
        });


     }

    function notificationList(type=''){
        var url = "{{ route('notification.list') }}";
        $.ajax({
            type: 'get',
            url: url,
            data: {'type':type},
            success: function(res){

                $('.notification_data').html(res.data);
                getNotificationCounts();
                if(res.type == "markas_read"){
                    notificationList();
                }
                $('.notify_count').removeClass('notification_tag').text('');
            }
        });
      }
</script>
@endpush
