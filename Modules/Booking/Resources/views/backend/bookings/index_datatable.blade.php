@extends('backend.layouts.app')

@section('title')
    {{ __($module_action) }} {{ __($module_title) }}
@endsection
@section('banner-button')
    <a href="{{ route("backend.$module_name.index") }}" class="btn btn-soft-dark"><i
            class="fa-solid fa-calendar-days me-2"></i>{{ __('messages.calender_view') }}</a>
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <x-backend.section-header>
                <div class="d-flex flex-wrap gap-3">
                    @if (auth()->user()->can('edit_booking') || auth()->user()->can('delete_booking'))
                        <x-backend.quick-action url="{{ route('backend.bookings.bulk_action') }}">
                            <div class="">
                                <select name="action_type" class="form-control select2 col-12" id="quick-action-type"
                                    style="width:100%">
                                    <option value="">{{ __('messages.no_action') }}</option>
                                    @can('edit_booking')
                                        <option value="change-status">{{ __('messages.status') }}</option>
                                    @endcan
                                    @can('delete_booking')
                                        <option value="delete">{{ __('messages.delete') }}</option>
                                    @endcan
                                </select>
                            </div>
                            <div class="select-status d-none quick-action-field" id="change-status-action">
                                <select name="status" class="form-control select2" id="status" style="width:100px">
                                    @foreach ($booking_status as $key => $value)
                                        @if ($value->name !== 'completed')
                                            <option value="{{ $value->name }}"
                                                {{ $filter['status'] == $value->name ? 'selected' : '' }}>
                                                {{ $value->value }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </x-backend.quick-action>
                    @endif
                    <div>
                        <button type="button" class="btn btn-secondary" data-modal="export">
                            <i class="fa-solid fa-download"></i> {{ __('messages.export') }}
                        </button>
                        {{--          <button type="button" class="btn btn-secondary" data-modal="import"> --}}
                        {{--            <i class="fa-solid fa-upload"></i> Import --}}
                        {{--          </button> --}}
                    </div>
                </div>
                <x-slot name="toolbar">
                    <div>
                        <div class="datatable-filter">
                            <select name="column_status" id="column_status" class="select2 form-control p-10"
                                data-filter="select" style="width: 100%">
                                <option value="">{{ __('messages.all_status') }}</option>
                                @foreach ($booking_status as $key => $value)
                                    <option value="{{ $value->name }}"
                                        {{ $filter['status'] == $value->name ? 'selected' : '' }}>
                                        {{ $value->value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="input-group flex-nowrap">
                        <span class="input-group-text" id="addon-wrapping"><i
                                class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..."
                            aria-label="Search" aria-describedby="addon-wrapping">
                    </div>
                    <button class="btn btn-outline-primary btn-group" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"><i
                            class="fa-solid fa-filter"></i> {{ __('messages.advance_filter') }}</button>
                </x-slot>
            </x-backend.section-header>
        </div>
        <div class="card-body" id="booking-datatable">
            <table id="datatable" class="table table-striped border table-responsive">
            </table>
        </div>
        <x-backend.advance-filter>
            <x-slot name="title">
                <h4> {{ __('booking.lbl_advanced_filter') }}</h4>
            </x-slot>
            <form action="javascript:void(0)" class="datatable-filter">
                <div class="form-group">
                    <label for="form-label"> {{ __('booking.lbl_booking_date') }}</label>
                    <input type="text" name="booking_date" id="booking_date"
                        placeholder="{{ __('booking.booking_date') }}" class="booking-date-range form-control" readonly />
                </div>
                <div class="form-group">
                    <label for="form-label"> {{ __('booking.lbl_customer_name') }} </label>
                    <select name="filter_user_id" id="column_user_id" data-placeholder="{{ __('booking.customer_name') }}"
                        name="column_user_id" data-filter="select" class="select2 form-control"
                        data-ajax--url="{{ route('backend.get_search_data', ['type' => 'customers']) }}"
                        data-ajax--cache="true">
                    </select>
                </div>
                <div class="form-group">
                    <label for="form-label"> {{ __('booking.lbl_staff_name') }} </label>
                    <select name="filter_employee_id" id="column_employee_id"
                        data-placeholder="{{ __('booking.staff_name') }}" name="column_employee_id" data-filter="select"
                        class="select2 form-control"
                        data-ajax--url="{{ route('backend.get_search_data', ['type' => 'employees']) }}"
                        data-ajax--cache="true">
                    </select>
                </div>
                <div class="form-group">
                    <label for="form-label"> {{ __('booking.lbl_services') }} </label>
                    <select name="filter_service_id" id="column_service_id"
                        data-placeholder="{{ __('booking.select_service') }}" name="column_service_id[]"
                        data-filter="select" class="select2 form-control" multiple
                        data-ajax--url="{{ route('backend.get_search_data', ['type' => 'services']) }}"
                        data-ajax--cache="true">
                    </select>
                </div>
                <button type="reset" class="btn btn-danger" id="reset-filter">{{ __('messages.reset') }}</button>
            </form>
        </x-backend.advance-filter>
    </div>
@endsection

@push('after-styles')
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <script src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
    <script src="{{ mix('modules/booking/script.js') }}"></script>
    <script src="{{ asset('js/form-modal/index.js') }}" defer></script>
    <!-- DataTables Core and Extensions -->

    <script type="text/javascript">
        const range_flatpicker = document.querySelectorAll('.booking-date-range')
        Array.from(range_flatpicker, (elem) => {
            if (typeof flatpickr !== typeof undefined) {
                flatpickr(elem, {
                    mode: "range",
                    dateFormat: "Y-m-d",
                })
            }
        })
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
                data: 'id',
                name: 'id',
                title: "ID",
                orderable: true,
                visible: true,
            },
            {
                data: 'start_date_time',
                name: 'start_date_time',
                title: "{{ __('booking.lbl_date') }}",
                orderable: true,
            },
            {
                data: 'user_id',
                name: 'user_id',
                title: "{{ __('booking.lbl_customer_name') }}"
            },
            {
                data: 'service_amount',
                name: 'service_amount',
                title: "{{ __('booking.lbl_amount') }}",
                orderable: true,
                searchable: false,
                // render: function(data, type, row) {

                //     return currencyFormat(data);

                // }
            },
            {
                data: 'service_duration',
                name: 'service_duration',
                title: "{{ __('booking.lbl_duration') }}",
                orderable: true,
                searchable: false,
            },
            {
                data: 'employee_id',
                name: 'employee_id',
                title: "{{ __('booking.lbl_staff_name') }}"
            },
            {
                data: 'services',
                name: 'services',
                title: "{{ __('booking.lbl_services') }}",
                orderable: false,
                searchable: true,
                width: '10%'
            },
            {
                data: 'packages',
                name: 'packages',
                title: "{{ __('booking.lbl_packages') }}",
                orderable: false,
                searchable: false,
                width: '10%'
            },
            {
                data: 'updated_at',
                name: 'updated_at',
                title: "{{ __('booking.lbl_update_at') }}",
                orderable: true,
                visible:false,
            },
            {
                data: 'status',
                name: 'status',
                orderable: true,
                searchable: true,
                title: "{{ __('booking.lbl_status') }}",
                width: '10%',
            },
            {
                data: 'payment_status',
                name: 'payment_status',
                orderable: false,
                searchable: false,
                title: "{{ __('booking.lbl_payment_status') }}",
                width: '10%',
            },
        ]

        const actionColumn = [{
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{ __('booking.lbl_action') }}",
            width: '10%'
        }]

        let finalColumns = [
            ...columns,
            ...actionColumn
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.$module_name.index_data") }}',
                finalColumns,
                orderColumn: [
                    [9, "desc"]
                ],
                advanceFilter: () => {
                    return {
                        booking_date: $('#booking_date').val(),
                        user_id: $('#column_user_id').val(),
                        emploee_id: $('#column_employee_id').val(),
                        service_id: $('#column_service_id').val(),
                    }
                }
            })
        })
        const offcanvasElem = document.querySelector('#offcanvasExample')
        offcanvasElem.addEventListener('shown.bs.offcanvas', function() {
            $('form.datatable-filter .select2').select2({
                dropdownParent: $('#offcanvasExample')
            });
        })

        $('#reset-filter').on('click', function(e) {
            $('#column_status').val('')
            $('#booking_date').val('')
            $('#column_user_id').val('')
            $('#column_employee_id').val('')
            $('#column_service_id').val('')
            $('form.datatable-filter .select2').empty()
            $('form.datatable-filter .select2').select2()

            const range_flatpickers = document.querySelectorAll('.booking-date-range');
            Array.from(range_flatpickers, (elem) => {
                const flatpickrInstance = elem._flatpickr;
                if (flatpickrInstance) {
                    flatpickrInstance.clear();
                }
            });

            window.renderedDataTable.ajax.reload(null, false)
        })

        $('#booking_date').on('change', function() {
            window.renderedDataTable.ajax.reload(null, false)
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
    </script>
@endpush
