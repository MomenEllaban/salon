@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
          <div data-render="app">
            <form-stocks :locations='@json($locations)' :products='@json($products)'></form-stocks>
          </div>
        </div>
    </div>
@endsection

@push ('after-styles')
<link rel="stylesheet" href="{{ mix('modules/location/style.css') }}">
@endpush

@push ('after-scripts')
<script src="{{ mix('modules/location/script.js') }}"></script>
<script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
@endpush
