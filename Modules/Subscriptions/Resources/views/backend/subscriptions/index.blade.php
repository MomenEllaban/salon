@extends('backend.layouts.app')

@section('title')
    {{ __($module_action) }} {{ __($module_title) }}
@endsection


@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/constant/style.css') }}">
@endpush
@section('content')
    <div class="card">
        <div class="card-header">
            <x-backend.section-header>
                <i class="{{ $module_icon }}"></i> {{ $module_title }} {{ __($module_action) }}
                <x-slot name="subtitle">
                    @lang(':module_name Data For Status, List etc.', ['module_name' => Str::title($module_name)])
                </x-slot>
                <x-slot name="toolbar">
                    <!-- @can('add_' . $module_name)
                        <x-buttons.offcanvas target='#form-offcanvas'
                            title="{{ __('messages.create') }}  {{ __($module_title) }}">{{ __('messages.create') }} 
                            {{ __($module_title) }}</x-buttons.offcanvas>
                    @endcan -->

                    <!-- @can('restore_' . $module_name)
                        <div class="btn-group">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                data-bs-target="#module-dropdown-01" aria-expanded="false">
                                <i class="fas fa-cog"></i>
                            </button>
                            <ul class="dropdown-menu" id="module-dropdown-01">
                                <li>
                                    <a class="dropdown-item" href=''>
                                        <i class="fas fa-eye-slash"></i> @lang('View trash')
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endcan -->
                </x-slot>
            </x-backend.section-header>
        </div>
        <div class="card-body">
            <table id="datatable" class="table table-striped border table-responsive">
            </table>
        </div>
    </div>

    <div data-render="app">


        <plan-offcanvas
            create-title="{{ __('messages.create') }} {{ __('messages.new') }} {{ __($module_title) }}"
            edit-title="{{ __('messages.edit') }} {{ __($module_title) }}">
        </plan-offcanvas>

    </div>
@endsection

@push('after-styles')
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <script src="{{ mix('modules/subscriptions/script.js') }}"></script>
    <script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>

    <!-- DataTables Core and Extensions -->
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

    <script type="text/javascript" defer>
    document.addEventListener('DOMContentLoaded', (event) => {
        window.renderedDataTable = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                responsive: true,
                dom: '<"row align-items-center"<"col-md-6" l><"col-md-6" f>><"table-responsive my-3" rt><"row align-items-center" <"col-md-6" i><"col-md-6" p>><"clear">',
                ajax: '{{ route("backend.subscriptions.index_data") }}',
                fixedHeader: true,
                drawCallback: function() {
                    if(laravel !== undefined) {
                        window.laravel.initialize();
                    }
                },

                columns: [{
                        data: 'id',
                        name: 'id',
                        title: 'ID'
                    },
                    {
                        data: 'user_id',
                        name: 'name',
                        title: 'Name'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        title: 'Plan Title'
                    },
                    {
                        data: 'type',
                        name: 'type',
                        title: 'Type'
                    },
                    {
                        data: 'plan_type',
                        name: 'plan_type',
                        title: 'Plan Type'
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        title: 'Amount'
                    },

                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        title: 'Status'
                    },

                ]
            });
    })
    </script>
@endpush
