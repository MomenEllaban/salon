<div class="card-header border-bottom p-3">
  <h5 class="mb-0">{{ __('messages.all_notifications') }} ({{ $all_unread_count }})</h5>
</div>
<div class="card-body overflow-auto card-header-border p-0 card-body-list max-17 scroll-thin">
    <div class="dropdown-menu-1 overflow-y-auto list-style-1 mb-0 notification-height">
        @if(isset($notifications) && count($notifications) > 0)
            @foreach($notifications->sortByDesc('created_at')->take(5) as $notification)
              @if($notification->data['data']['notification_group']=='booking')
                <div class="dropdown-item-1 float-none p-3 list-unstyled iq-sub-card  {{ $notification->read_at ? '':'notify-list-bg'}} ">
                  <a href="{{ route('backend.bookings.index', ['booking_id' => $notification->data['data']['id']]) }}" class="">
                    <h6>{{ $notification->data['subject']}}</h6>
                    <div class="list-item d-flex">
                        <div class="me-3 mt-1">
                            <button type="button" class="btn btn-soft-primary btn-icon rounded-pill">
                                {{ strtoupper(substr($notification->data['data']['user_name'], 0, 1)) }}
                            </button>
                        </div>

                        <div class="list-style-detail">
                            <p class="text-body mb-1">Booking received for <span class="text-primary">
                                @if(!empty($notification->data['data']['service_name']))
                                    {{ $notification->data['data']['service_name'] }}
                                @else
                                    {{ $notification->data['data']['package_name'] ?? '-' }}
                                @endif
                            </span> service by <span class="text-black">{{ ($notification->data['data']['user_name']) }}</span></p>
                            <div class="d-flex justify-content-between">
                                <p class="text-body">{{ ($notification->data['data']['booking_date']) }}</p>
                                <p class="text-body">{{ ($notification->data['data']['booking_time']) }}</p>
                            </div>
                        </div>
                    </div>
                  </a>
                </div>

                @elseif($notification->data['data']['notification_group']=='package')
                <div class="dropdown-item-1 float-none p-3 list-unstyled iq-sub-card {{ $notification->read_at ? '' : 'notify-list-bg'}}">
                    <a href="{{ route('backend.package.index') }}" class="">    
                    <h6>{{ $notification->data['subject'] }}</h6>
                        <div class="list-item d-flex">
                            <div class="me-3 mt-1">
                                <button type="button" class="btn btn-soft-primary btn-icon rounded-pill">
                                    {{ strtoupper(substr($notification->data['data']['user_name'], 0, 1)) }}
                                </button>
                            </div>
                        
                            <div class="list-style-detail">
                                <p class="text-body mb-1">Package <span class="text-primary">
                                    @if(!empty($notification->data['data']['package_name']))
                                        {{ $notification->data['data']['package_name'] }}
                                    @else
                                        {{ '-' }}
                                    @endif
                                </span> is about to expire soon for <span class="text-black">{{ $notification->data['data']['user_name'] }}</span></p>
                                <div class="d-flex justify-content-between">
                                    <p class="text-body">on {{ $notification->data['data']['package_expiry_date'] }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                 @else
                     <div class="dropdown-item-1 float-none p-3 list-unstyled iq-sub-card  {{ $notification->read_at ? '':'notify-list-bg'}} ">
                     <a href="{{ route('backend.orders.show', ['id' => $notification->data['data']['id']]) }}" class="">
                     <div class="d-flex justify-content-between">
                    <h6>{{ $notification->data['subject']}}</h6>
                    <h6>{{ ($notification->data['data']['order_code']) ?? '-' }} </h6>
                    </div>
                    <div class="list-item d-flex">
                        <div class="me-3 mt-1">
                            <button type="button" class="btn btn-soft-primary btn-icon rounded-pill">
                                {{ strtoupper(substr($notification->data['data']['user_name'], 0, 1)) }}
                            </button>
                        </div>
                         <div class="list-style-detail">
                            @if($notification->data['data']['notification_type']=='order_placed')
                            <p class="text-body mb-1">New Order received from <span class="text-black">{{ ($notification->data['data']['user_name']) }}.</span></p>
                            <div class="d-flex justify-content-between">
                                <p class="text-body">{{ ($notification->data['data']['order_date']) }}</p>
                                <p class="text-body">{{ ($notification->data['data']['order_time']) }}</p>
                            </div>

                             @elseif($notification->data['data']['notification_type']=='order_proccessing')
                            <p class="text-body mb-1">Order <span class="text-black">{{ ($notification->data['data']['order_code']) }}</span> has been Processing.</p>
                            <div class="d-flex justify-content-between">
                                <p class="text-body">{{ ($notification->data['data']['order_date']) }}</p>
                                <p class="text-body">{{ ($notification->data['data']['order_time']) }}</p>
                            </div>

                             @elseif($notification->data['data']['notification_type']=='order_delivered')
                            <p class="text-body mb-1">Order <span class="text-black">{{ ($notification->data['data']['order_code']) }} </span> has been Delivered.</p>
                            <div class="d-flex justify-content-between">
                                <p class="text-body">{{ ($notification->data['data']['order_date']) }}</p>
                                <p class="text-body">{{ ($notification->data['data']['order_time']) }}</p>
                            </div>

                              @elseif($notification->data['data']['notification_type']=='order_cancelled')
                            <p class="text-body mb-1">Order <span class="text-black">{{ ($notification->data['data']['order_code']) }} </span> has been Cancelled.</p>
                            <div class="d-flex justify-content-between">
                                <p class="text-body">{{ ($notification->data['data']['order_date']) }}</p>
                                <p class="text-body">{{ ($notification->data['data']['order_time']) }}</p>
                            </div>

                            @else


                            @endif
                        </div>

                    </div>
                  </a>
                </div>
                @endif

            @endforeach
        @else
            <li class="list-unstyled dropdown-item-1 float-none p-3">
                <div class="list-item d-flex justify-content-center align-items-center">
                    <div class="list-style-detail ml-2 mr-2">
                    <h6 class="font-weight-bold">{{ __('messages.no_notification') }}</h6>
                    <p class="mb-0"></p>
                    </div>
                </div>
            </li>
        @endif
    </div>
</div>
<div class="card-footer py-2 border-top">
  <div class="d-flex align-items-center justify-content-between">
      @if($all_unread_count > 0 )
        <a href="{{route('backend.notifications.markAllAsRead')}}" data-type="markas_read" class="text-primary mb-0 notifyList pull-right" ><span>{{__('messages.mark_all_as_read') }}</span></a>
      @endif
      @if(isset($notifications) && count($notifications) > 0)
        <a href="{{ route('backend.notifications.index') }}" class="btn btn-sm btn-primary">{{ __('messages.view_all') }}</a>
      @endif
  </div>
</div>
{{-- @if(isset($notifications) && count($notifications) > 0)
<div class="card-footer text-muted p-3 text-center ">
    <a href="{{ route('backend.notifications.index') }}" class="mb-0 btn-link btn-link-hover font-weight-bold view-all-btn">{{ __('messages.view_all') }}</a>
</div>
@endif --}}
