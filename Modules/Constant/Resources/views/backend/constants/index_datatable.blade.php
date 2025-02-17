@extends('backend.layouts.app')

@section('title')
    {{ __($module_action) }} {{ __($module_title) }}
@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/constant/style.css') }}">
@endpush
@section('content')
    <div class="card">
        <div class="card-body">
            <x-backend.section-header>
                <div>
                  @if(auth()->user()->can("edit_$module_name") || auth()->user()->can("delete_$module_name"))
                  <x-backend.quick-action url="{{route('backend.constants.bulk_action')}}">
                    <div class="">
                      <select name="action_type" class="form-control select2 col-12" id="quick-action-type" style="width:100%">
                          <option value="">{{ __('messages.no_action') }}</option>
                          @can("edit_$module_name")
                          <option value="change-status">{{ __('messages.status') }}</option>
                          @endcan
                          @can("delete_$module_name")
                          <option value="delete">{{ __('messages.delete') }}</option>
                          @endcan
                      </select>
                    </div>
                    <div class="select-status d-none quick-action-field" id="change-status-action">
                        <select name="status" class="form-control select2" id="status" style="width:100%">
                          <option value="1">{{ __('messages.active') }}</option>
                          <option value="0">{{ __('messages.inactive') }}</option>
                        </select>
                    </div>
                  </x-backend.quick-action>
                  @endif
                </div>
                <x-slot name="toolbar">
                  <div>
                    <div class="datatable-filter">
                      <select name="column_status" id="column_status" class="select2 form-control" data-filter="select" style="width: 100%">
                        <option value="">{{__('messages.all')}}</option>
                        <option value="0" {{$filter['status'] == '0' ? "selected" : ''}}>{{ __('messages.inactive') }}</option>
                        <option value="1" {{$filter['status'] == '1' ? "selected" : ''}}>{{ __('messages.active') }}</option>
                      </select>
                    </div>
                  </div>
                  <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..." aria-label="Search" aria-describedby="addon-wrapping">
                  </div>
                  @can('add_' . $module_name)
                    <x-buttons.offcanvas target='#form-offcanvas'
                        title="{{ __('messages.create') }} {{ __($module_title) }}">{{ __('messages.create') }}
                        {{ __($module_title) }}</x-buttons.offcanvas>
                  @endcan
                  <button class="btn btn-outline-primary btn-group" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"><i class="fa-solid fa-filter"></i>  {{__('messages.advance_filter')}}</button>
                </x-slot>
              </x-backend.section-header>
            <table id="datatable" class="table table-striped border table-responsive">
            </table>
        </div>
    </div>

    <div data-render="app">
        <form-offcanvas create-title="{{ __('messages.create') }} {{ __($module_title) }}"
            edit-title="{{ __('messages.edit') }} {{ __($module_title) }}"></form-offcanvas>
    </div>
@endsection

@push('after-styles')
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <script src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
    <script src="{{ mix('modules/constant/script.js') }}"></script>
    <script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
    <!-- DataTables Core and Extensions -->

    <script type="text/javascript">

        const columns = [
            {
                name: 'check',
                data: 'check',
                title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                width: '0%',
                exportable: false,
                orderable: false,
                searchable: false,
            },
            { data: 'id', name: 'id', title: 'Id' },
            { data: 'type', name: 'type', title: "{{ __('constant.lbl_type') }}" },
            { data: 'name', name: 'name', title: "{{ __('constant.lbl_name') }}" },
            { data: 'value', name: 'value', title: "{{ __('constant.lbl_value') }}" },
            { data: 'status', name: 'status', orderable: false, searchable: true, title: 'Status' },
            { data: 'updated_at', name: 'updated_at',  title: 'Updated At' },
        ]

        const actionColumn = [
            { data: 'action', name: 'action', orderable: false, searchable: false, title: 'Action' }
        ]

        const customFieldColumns = JSON.parse(@json($columns))

        let finalColumns = [
            ...columns,
            ...customFieldColumns,
            ...actionColumn
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.$module_name.index_data") }}',
                finalColumns,
            })
        })

        function resetQuickAction () {
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

      $('#quick-action-type').change(function () {
        resetQuickAction()
      });
    </script>
@endpush
