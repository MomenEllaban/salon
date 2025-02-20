@extends('backend.layouts.app')

@section('title')
    {{ __($module_action) }} {{ __($module_title) }}
@endsection
@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/tax/style.css') }}">
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <x-backend.section-header>

                <div>
                    @if (auth()->user()->can('edit_tax') || auth()->user()->can('delete_tax'))
                        <x-backend.quick-action url="{{ route('backend.expense.bulk_action') }}">
                            <div class="">
                                <select name="action_type" class="form-control select2 col-12" id="quick-action-type"
                                    style="width:100%">
                                    <option value="">{{ __('messages.no_action') }}</option>
                                    @can('edit_tax')
                                        <option value="change-status">{{ __('messages.status') }}</option>
                                    @endcan
                                    @can('delete_tax')
                                        <option value="delete">{{ __('messages.delete') }}</option>
                                    @endcan
                                </select>
                            </div>
                            <div class="select-status d-none quick-action-field" id="change-status-action">
                                <select name="status" class="form-control select2 p-2" id="status" style="width:100%">
                                    <option value="1">{{ __('messages.paid') }}</option>
                                    <option value="0">{{ __('messages.unpaid') }}</option>
                                </select>
                            </div>
                        </x-backend.quick-action>
                    @endif
                </div>
                <x-slot name="toolbar">
                    <div>
                        <div class="datatable-filter">
                            <select name="column_status" id="column_status" class="select2 form-control p-10"
                                data-filter="select" style="width: 100%">
                                <option value="">{{ __('messages.all') }}</option>
                                <option value="0" {{ $filter['status'] == '0' ? 'selected' : '' }}>
                                    {{ __('messages.unpaid') }}</option>
                                <option value="1" {{ $filter['status'] == '1' ? 'selected' : '' }}>
                                    {{ __('messages.paid') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="input-group flex-nowrap">
                        <span class="input-group-text" id="addon-wrapping"><i
                                class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..."
                            aria-label="Search" aria-describedby="addon-wrapping">
                    </div>
                    @hasPermission('add_tax')
                        <button type="button" class="btn btn-primary" data-crud-id="{{ 0 }}" title=""><i
                                class="fas fa-plus-circle me-2"></i>{{ __('messages.new') }}</button>
                    @endhasPermission
                </x-slot>
            </x-backend.section-header>
            <table id="datatable" class="table border table-responsive">
            </table>
        </div>

        <div data-render="app">
            <expense-form-offcanvas default-image="{{product_feature_image()}}"  create-title="{{ __('messages.new') }} {{ __($module_title) }}"
                edit-title="{{ __('messages.edit') }} {{ __($module_title) }}">
            </expense-form-offcanvas>
        </div>

    </div>
    <expense-form-offcanvas default-image="{{product_feature_image()}}"  create-title="{{ __('messages.new') }} {{ __($module_title) }}"
        edit-title="{{ __('messages.edit') }} {{ __($module_title) }}">
    </expense-form-offcanvas>
    </div>
@endsection

@push('after-styles')
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <!-- DataTables Core and Extensions -->
    <script src="{{ mix('modules/expense/script.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

    <script type="text/javascript">
        const finalColumns = [{
                name: 'check',
                data: 'check',
                title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                width: '0%',
                exportable: false,
                orderable: false,
                searchable: false,
            },
            {
                data: 'amount',
                name: 'title',
                title: "{{ __('expense.amount') }}",
                width: '15%',
            },
            {
                data: 'branch',
                name: 'branch',
                title: "{{ __('expense.branch') }}",
                width: '15%'
            },
            {
                data: 'reference_number',
                name: 'reference_number',
                title: "{{ __('expense.reference_number') }}",
                width: '15%',
            },
            {
                data: 'date',
                name: 'date',
                title: "{{ __('expense.date') }}",
                width: '15%',
            },
            {
                data: 'note',
                name: 'note',
                title: "{{ __('expense.note') }}",
                width: '20%',
            },
            {
                data: 'category',
                name: 'category',
                title: "{{ __('expense.category') }}",
                width: '10%',
            },
            {
                data: 'subcategory',
                name: 'subcategory',
                title: "{{ __('expense.subcategory') }}",
                width: '10%',
            },
            {
                data: 'status',
                name: 'status',
                title: "{{ __('expense.lbl_status') }}",
                width: '5%',
            },
            {
                data: 'action',
                name: 'action',
                title: "{{ __('expense.lbl_action') }}",
                orderable: false,
                searchable: false,
                width: '5%',
            },
            {
                data: 'updated_at',
                name: 'updated_at',
                title: "{{ __('expense.lbl_updated') }}",
                width: '5%',
                visible: false,
            },
        ]
        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.$module_name.index_data") }}',
                finalColumns,
                orderColumn: [
                    [5, "desc"]
                ],
            })
        })


        const formOffcanvas = document.getElementById('form-offcanvas')

        const instance = bootstrap.Offcanvas.getOrCreateInstance(formOffcanvas)

        $(document).on('click', '[data-crud-id]', function() {
            setEditID($(this).attr('data-crud-id'), $(this).attr('data-parent-id'))
        })

        function setEditID(id, parent_id) {
            if (id !== '' || parent_id !== '') {
                const idEvent = new CustomEvent('crud_change_id', {
                    detail: {
                        form_id: id,
                        parent_id: parent_id
                    }
                })
                document.dispatchEvent(idEvent)
            } else {
                removeEditID()
            }
            instance.show()
        }

        function removeEditID() {
            const idEvent = new CustomEvent('crud_change_id', {
                detail: {
                    form_id: 0,
                    parent_id: null
                }
            })
            document.dispatchEvent(idEvent)
        }

        formOffcanvas?.addEventListener('hidden.bs.offcanvas', event => {
            removeEditID()
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
