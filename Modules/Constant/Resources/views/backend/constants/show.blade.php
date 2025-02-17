@extends('backend.layouts.app')

@section('title') {{ __($module_action) }} {{ __($module_title) }} @endsection



@section('content')
<div class="card">
    <div class="card-body">

        <x-backend.section-header>
            <i class="{{ $module_icon }}"></i> {{ $module_title }} <small class="text-muted">{{ __($module_action) }}</small>

            <x-slot name="subtitle">
                @lang(":module_name Management Dashboard", ['module_name'=>Str::title($module_name)])
            </x-slot>
            <x-slot name="toolbar">
                <a href="{{ route("backend.$module_name.index") }}" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="{{ __($module_name) }} List"><i class="fas fa-list"></i> List</a>
                @can('edit_'.$module_name)
                    <button type="button" class="btn btn-primary btn-icon btn-sm" data-bs-toggle="offcanvas" data-bs-target="#form-offcanvas" data-crud-id="{{$$module_name_singular->id}}" title="{{ __('messages.edit') }} " data-bs-toggle="tooltip"> <i class="fa-solid fa-pen-clip"></i></button>
                @endcan
                <x-backend.buttons.return-back />
            </x-slot>
        </x-backend.section-header>

        <hr>

        <div class="row mt-4">
            <div class="col-12">

                @include('backend.includes.show')

            </div>
        </div>
    </div>

    <div class="card-footer">
        <div class="row">
            <div class="col">
                <small class="float-end text-muted">
                    Updated: {{$$module_name_singular->updated_at->diffForHumans()}},
                    Created at: {{$$module_name_singular->created_at->isoFormat('LLLL')}}
                </small>
            </div>
        </div>
    </div>
</div>

<div data-render="app">
    <form-offcanvas create-title="{{ __('messages.create') }} {{__('messages.new')}} {{ __($module_title) }}"
        edit-title="{{ __('messages.edit') }} {{ __($module_title) }}"></form-offcanvas>
</div>
@endsection

@push('after-scripts')
    <script src="{{ mix('modules/constant/script.js') }}"></script>
@endpush
