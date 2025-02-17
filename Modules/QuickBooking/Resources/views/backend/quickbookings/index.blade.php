@extends('backend.layouts.quick-booking')

@section('title') Quick Booking @endsection

@push('after-styles')
    <link rel="stylesheet" href='{{ mix("modules/quickbooking/style.css") }}'>
@endpush

@section('content')
  <div class="container">
    <div class="row justify-content-center align-items-center vh-100">
      <div class="col">
        <quick-booking></quick-booking>
      </div>
    </div>
  </div>
@endsection

@push ('after-scripts')
<script src="{{ mix("modules/quickbooking/script.js") }}"></script>
@endpush
