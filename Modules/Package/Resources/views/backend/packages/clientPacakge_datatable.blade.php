@extends('backend.layouts.app')

@section('title')
    {{ __($module_action) }} {{ __($module_title) }}
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <table id="datatable" class="table table-striped border table-responsive">
            </table>
        </div>
    </div>
    <div data-render="app">
        <client-package-form-offcanvas>
        </client-package-form-offcanvas>
    </div>
@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/package/style.css') }}">
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <script src="{{ mix('modules/package/script.js') }}"></script>
    <script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
    <script src="{{ asset('js/form-modal/index.js') }}" defer></script>

    <!-- DataTables Core and Extensions -->
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

    <script type="text/javascript" defer>
        const columns = [{
                data: 'username',
                name: 'username',
                title: "{{ __('customer.singular_title') }}"
            },
            {
                data: 'packagename',
                name: 'packagename',
                title: "{{ __('package.singular_title') }}"
            },
            {
                data: 'qty',
                name: 'qty',
                title: "{{ __('package.no_of_services') }}"
            },
            {
                data: 'package_price',
                name: 'package_price',
                title: "{{ __('package.lbl_package_price') }}"
            },
            {
                data: 'startdate',
                name: 'startdate',
                title: "{{ __('start Date') }}"
            },
            {
                data: 'expirydate',
                name: 'expirydate',
                title: "{{ __('expiry Date') }}"
            },
            {
                data: 'updated_at',
                name: 'updated_at',
                title: "{{ __('service.lbl_update_at') }}",
                orderable: true,
                visible: false,
            },

        ]


        const actionColumn = [{
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{ __('service.lbl_action') }}",
            width: '5%'
        }]

        // const customFieldColumns = JSON.parse(@json($columns))

        let finalColumns = [
            ...columns,
            // ...customFieldColumns,
            ...actionColumn
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.$module_name.clientPackageData") }}',
                finalColumns,
                orderColumn: [[ 6, "desc" ]],
                advanceFilter: () => {
                    return {}
        }
            });
        })
    </script>
@endpush
