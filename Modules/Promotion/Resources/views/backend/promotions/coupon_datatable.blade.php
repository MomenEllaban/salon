@extends('backend.layouts.app')

@section('title')
    {{ __($module_action) }} {{ __('promotion.coupon_title') }}
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <x-backend.section-header>
                <div class="d-flex flex-wrap gap-3">
                    <div>
                        <button type="button" class="btn btn-secondary" data-modal="export">
                            <i class="fa-solid fa-download"></i> {{ __('messages.export') }}
                        </button>
                    </div>
                </div>
                <x-slot name="toolbar">
                    <div>
                        <a href="{{ route('backend.promotions.index') }}"
                        class="float-right btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i>
                        {{ __('messages.back') }}</a>

                    </div>
                </x-slot>
            </x-backend.section-header>
            <table id="datatable" class="table table-striped border table-responsive">
            </table>
        </div>
    </div>
    <div data-render="app">
        <form-offcanvas create-title="{{ __('messages.create') }} {{ __($module_title) }}"
            edit-title="{{ __('messages.edit') }} {{ __($module_title) }}">
        </form-offcanvas>
    </div>
@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/promotion/style.css') }}">

    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <script src="{{ mix('modules/promotion/script.js') }}"></script>
    <script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
    <script src="{{ asset('js/form-modal/index.js') }}" defer></script>

    <!-- DataTables Core and Extensions -->
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

    <script type="text/javascript" defer>
        const columns = [{
                data: 'coupon_code',
                name: 'coupon_code',
                title: "{{ __('promotion.coupon_code') }}",
            },
            {
                data: 'value',
                name: 'value',
                title: "{{ __('promotion.value') }}"
            },
            {
                data: 'use_limit',
                name: 'use_limit',
                title: "{{ __('promotion.use_limit') }}"
            },
            {
                data: 'used_by',
                name: 'used_by',
                title: "{{ __('promotion.user') }}"
            },
            {
                data: 'is_expired',
                name: 'is_expired',
                orderable: true,
                searchable: true,
                title: "{{ __('promotion.lbl_expired') }}",
                width: '5%',

            },

            {
                data: 'updated_at',
                name: 'updated_at',
                title: "{{ __('promotion.lbl_update_at') }}",
                orderable: true,
                visible: false,
            },

        ]



        let finalColumns = [
            ...columns,
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.$module_name.coupon_data", $promotion_id) }}',
                finalColumns,
                advanceFilter: () => {
                    return {}
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
    </script>
@endpush
