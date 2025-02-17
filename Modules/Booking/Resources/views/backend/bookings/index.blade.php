@extends('backend.layouts.app')

@section('title') {{ __($module_action) }} {{ __($module_title) }} @endsection

@push('after-styles')
    {{-- <link rel="stylesheet" href='{{ mix("modules/booking/style.css") }}'> --}}

@endpush
@section('banner-button')
@hasPermission('booking_booking_tableview')
<a href="{{route("backend.$module_name.datatable_view")}}" class="btn btn-dark"><i class="fa-solid fa-table"></i> {{ __('messages.datatable_view') }}</a>
@endhasPermission
@endsection
@section('content')
<div class="card">
    <div class="card-body">
      <div data-render="app">
        <calendar-view
          slot-duration="{{ setting('slot_duration') }}"
          status="{{ json_encode($statusList) }}"
          :branch-id="{{ $selected_branch->id ?? 1 }}"
          date={{$date}}
          ></calendar-view>
      </div>
    </div>
</div>

@endsection

@push ('after-scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="{{ mix("modules/booking/script.js") }}"></script>

@endpush
