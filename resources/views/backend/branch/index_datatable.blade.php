@extends('backend.layouts.app')

@section('title')
{{ __($module_action) }} {{ __($module_title) }}
@endsection

@push('after-styles')
@endpush

@section('content')
<div class="card">
    <div class="card-body">
        <x-backend.section-header>
            <div>
                @if(auth()->user()->can('edit_branch') || auth()->user()->can('delete_branch'))
                <x-backend.quick-action url="{{route('backend.branch.bulk_action')}}">
                    <div class="">
                        <select name="action_type" class="form-control select2 col-12" id="quick-action-type"
                            style="width:100%">
                            <option value="">{{ __('messages.no_action') }}</option>
                            @can('edit_branch')
                            <option value="change-status">{{ __('messages.status') }}</option>
                            @endcan
                            @can('delete_branch')
                            <option value="delete">{{ __('messages.delete') }}</option>
                            @endcan
                        </select>
                    </div>
                    <div class="select-status d-none quick-action-field" id="change-status-action">
                            <select name="status" class="form-control select2" id="status" style="width:100%">
                                <option value="1" selected>{{ __('messages.active') }}</option>
                                <option value="0">{{ __('messages.inactive') }}</option>
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
                            <option value="">{{__('messages.all')}}</option>
                            <option value="0" {{$filter['status'] == '0' ? "selected" : ''}}>
                                {{ __('messages.inactive') }}</option>
                            <option value="1" {{$filter['status'] == '1' ? "selected" : ''}}>{{ __('messages.active') }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="addon-wrapping"><i
                            class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..." aria-label="Search"
                        aria-describedby="addon-wrapping">
                </div>

                @hasPermission('add_branch')
                <x-buttons.offcanvas target='#form-offcanvas' title="">
                    {{ __('messages.new') }}
                </x-buttons.offcanvas>
                @endhasPermission
            </x-slot>
        </x-backend.section-header>
        <table id="datatable" class="table table-striped border table-responsive">
        </table>
        <div data-render="app">
            <branch-form-offcanvas default-image="{{default_feature_image()}}" create-title="{{ __('messages.new') }} {{ __('branch.singular_title') }}"
                edit-title="{{ __('messages.edit') }} {{ __('branch.singular_title') }}" select-data="{{json_encode($select_data)}}"
                :customefield="{{ json_encode($customefield) }}">
            </branch-form-offcanvas>
            <branch-gallery-offcanvas></branch-gallery-offcanvas>
            <assign-branch-employee-offcanvas></assign-branch-employee-offcanvas>
        </div>
    </div>
</div>

<!-- <div data-render="app">
    <branch-form-offcanvas
        create-title="{{ __('messages.create') }}  {{ __('New') }} {{ __($module_title) }}"
        edit-title="{{ __('messages.edit') }} {{ __($module_title) }}"
        select-data="{{json_encode($select_data)}}"
        :customefield="{{ json_encode($customefield) }}">
    </branch-form-offcanvas>
    <branch-gallery-offcanvas></branch-gallery-offcanvas>
</div> -->
@endsection

@push('after-styles')
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
<script src="{{ mix('js/vue.min.js') }}"></script>
<script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>

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
    // {
    //     data: 'image',
    //     name: 'image',
    //     title: "{{ __('branch.lbl_image') }}",
    //     orderable: false,
    //     width: '0%'
    // },
    {
        data: 'name',
        name: 'name',
        title: "{{ __('branch.lbl_name') }}",
        width: '15%',
    },
    {
        data: 'contact_number',
        name: 'contact_number',
        title: "{{ __('branch.lbl_contact_number') }}",
        width: '15%',
    },
    {
        data: 'manager_id',
        name: 'manager_id',
        title: "{{ __('branch.lbl_manager_name') }}",
        width: '15%',
    },
    {
        data: 'address.city',
        name: 'address.city',
        title: "{{ __('branch.lbl_city') }}",
        width: '15%',
    },
    {
        data: 'address.postal_code',
        name: 'address.postal_code',
        title: "{{ __('branch.lbl_postal_code') }}",
        width: '10%',
    },
    {
        data: 'assign',
        name: 'assign',
        title: "{{ __('messages.assign_staff') }}",
        orderable: false,
        searchable: false
    },
    {
        data: 'branch_for',
        name: 'branch_for',
        title: "{{ __('branch.lbl_branch_for') }}",
        width: '12%'
    },
    {
        data: 'status',
        name: 'status',
        orderable: true,
        searchable: true,
        title: "{{ __('branch.lbl_status') }}",
        width: '5%',
    },
    {
        data: 'updated_at',
        name: 'updated_at',
        title: "{{ __('branch.lbl_update_at') }}",
        orderable: true,
        visible: false,
    },

]

const actionColumn = [{
        data: 'action',
        name: 'action',
        orderable: false,
        searchable: false,
        title: "{{ __('branch.lbl_action') }}",
        width: '5%'
    }]

    // Check permissions
    const hasEditPermission = @json(auth()->user()->can('edit_branch'));
    const hasDeletePermission = @json(auth()->user()->can('delete_branch'));

    // Add the action column only if the user has edit or delete permission
    let finalColumns = [...columns];
    if (hasEditPermission || hasDeletePermission) {
        finalColumns = [...finalColumns, ...actionColumn];
    }

    const customFieldColumns = JSON.parse(@json($columns))

    finalColumns = [
        ...finalColumns,
        ...customFieldColumns
    ]

    document.addEventListener('DOMContentLoaded', (event) => {
        initDatatable({
            url: '{{ route("backend.$module_name.index_data") }}',
            finalColumns,
            orderColumn: [
                [9, "desc"]
            ],
        })
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
