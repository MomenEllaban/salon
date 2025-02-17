@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <x-backend.section-header>
                <x-slot name="toolbar">
                    <div class="flex-grow-1">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text rounded-end-0">{{ setting('inv_prefix') }}</span>
                            </div>
                            <input type="text" class="form-control order-code" placeholder="code" name="code"
                                value="{{ isset($searchCode) ? $searchCode : '' }}">
                        </div>
                    </div>
                    <div>
                        <div class="datatable-filter" style="width: 100%; display: inline-block;">
                            <select name="payment_status" id="payment_status" class="select2 form-control"
                                data-filter="select">
                                <option value="">Payment Status</option>
                                <option value="paid">Paid</option>
                                <option value="unpaid">Unpaid</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <div class="datatable-filter" style="width: 100%; display: inline-block;">
                            <select name="delivery_status" id="delivery_status" class="select2 form-control"
                                data-filter="select">
                                <option value="">Delivery Status</option>
                                <option value="order_placed">Order Placed</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="input-group flex-nowrap">
                        <span class="input-group-text" id="addon-wrapping"><i
                                class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="table_search" class="form-control dt-search"
                            placeholder="{{ __('messages.search') }}...">
                    </div>
                </x-slot>
            </x-backend.section-header>
            <table id="datatable" class="table table-striped border table-responsive">
            </table>
        </div>
    </div>
    {{-- <x-backend.advance-filter>
        <x-slot name="title">
            <h4>{{ __('service.lbl_advanced_filter') }}</h4>
        </x-slot>
        <button type="reset" class="btn btn-danger" id="reset-filter">Reset</button>
    </x-backend.advance-filter> --}}
@endsection

@push('after-styles')
    <link rel="stylesheet" href='{{ mix('modules/product/style.css') }}'>
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <script src='{{ mix('modules/product/script.js') }}'></script>
    <script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
    <script src="{{ asset('js/form-modal/index.js') }}" defer></script>
    <!-- DataTables Core and Extensions -->
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

    <script type="text/javascript" defer>
        const columns = [{
                name: 'check',
                data: 'check',
                title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                width: '0%',
                exportable: false,
                orderable: false,
                searchable: false,
            },
            {
                data: 'order_code',
                name: 'order_code',
                title: "{{ __('messages.order_code') }}",
                orderable: false,
                searchable: false,
            },
            {
                data: 'customer_name',
                name: 'customer_name',
                title: "{{ __('booking.lbl_customer_name') }}",
                orderable: false,
            },
            {
                data: 'phone',
                name: 'phone',
                title: "{{ __('branch.lbl_contact_number') }}",
                orderable: false,
            },
            {
                data: 'placed_on',
                name: 'placed_on',
                title: "{{ __('messages.placed_on') }}",
                orderable: false,
                searchable: false,
            },
            {
                data: 'items',
                name: 'items',
                title: "{{ __('messages.items') }}",
                orderable: false,
                searchable: false,
            },
            {
                data: 'payment',
                name: 'payment',
                title: "{{ __('messages.payment') }}",
                orderable: false,
                searchable: false,
            },
            {
                data: 'type',
                name: 'type',
                title: "{{ __('messages.type') }}",
                orderable: false,
                searchable: false,
            },
            {
                data: 'status',
                name: 'status',
                title: "{{ __('messages.status') }}",
                orderable: false,
                searchable: false,
            },
        //    {
        //         data: 'location',
        //         name: 'location',
        //         title: "Location",
        //         orderable: false,
        //         searchable: false,
        //     },
            {
              data: 'updated_at',
              name: 'updated_at',
              title: "{{ __('product.lbl_update_at') }}",
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

        let finalColumns = [
            ...columns,
            ...actionColumn
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.$module_name.index_data") }}',
                finalColumns,
                orderColumn: [[ 9, "desc" ]],
                advanceFilter: () => {
                    return {
                        search: $('[name="table_search"]').val(),
                        code: $('[name="code"]').val(),
                        delivery_status: $('[name="delivery_status"]').val(),
                        payment_status: $('[name="payment_status"]').val(),
                        location_id: $('[name="location_id"]').val()
                    }
                }
            });
        })

        function resetQuickAction() {
            const actionValue = $('#quick-action-type').val();
            if (actionValue != '') {
                $('#quick-action-apply').removeAttr('disabled');

                if (actionValue == 'change-status') {
                    $('.quick-action-field').addClass('d-none');
                    $('#change-status-action').removeClass('d-none');
                } else {
                    $('.quick-action-field').addClass('d-none');
                }

            } else {
                $('#quick-action-apply').attr('disabled', true);
                $('.quick-action-field').addClass('d-none');
            }
        }

        $('#quick-action-type').change(function() {
            resetQuickAction()
        });

        $(document).on('input', '.order-code', function() {
            window.renderedDataTable.ajax.reload(null, false)
        })
    </script>
@endpush
