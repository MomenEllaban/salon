@extends('backend.layouts.app')

@section('title') {{ __($module_action) }} {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <x-backend.section-header>
                <div class="d-flex flex-wrap gap-3">
                    <x-backend.quick-action url="{{ route('backend.logistic-zones.bulk_action') }}">
                        <div class="">
                            <select name="action_type" class="form-control select2 col-12" id="quick-action-type"
                                style="width:100%">
                                <option value="">{{ __('messages.no_action') }}</option>
                                {{-- <option value="change-status">{{ __('messages.status') }}</option> --}}
                                <option value="delete">{{ __('messages.delete') }}</option>
                            </select>
                        </div>
                        <div class="select-status d-none quick-action-field" id="change-status-action">
                            <select name="status" class="form-control select2" id="status" style="width:100%">
                                <option value="1" selected>{{ __('messages.active') }}</option>
                                <option value="0">{{ __('messages.inactive') }}</option>
                            </select>
                        </div>
                    </x-backend.quick-action>
                </div>
                <x-slot name="toolbar">

                    <div>
                        <div class="datatable-filter">
                            {{-- <select name="column_status" id="column_status" class="select2 form-control"
                                data-filter="select" style="width: 100%">
                                <option value="">{{__('messages.all')}}</option>
                                <option value="0" {{ $filter['status'] == '0' ? 'selected' : '' }}>
                                    {{ __('messages.inactive') }}</option>
                                <option value="1" {{ $filter['status'] == '1' ? 'selected' : '' }}>
                                    {{ __('messages.active') }}</option>
                            </select> --}}
                        </div>
                    </div>

                    <div class="input-group flex-nowrap">
                        <span class="input-group-text" id="addon-wrapping"><i
                                class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..." aria-label="Search"
                            aria-describedby="addon-wrapping">
                    </div>
                    @hasPermission('add_service')
                        <x-buttons.offcanvas target='#form-offcanvas' title="">
                        {{ __('messages.new') }}</x-buttons.offcanvas>
                    @endhasPermission
                </x-slot>
            </x-backend.section-header>
            <table id="datatable" class="table table-striped border table-responsive">
            </table>
        </div>
    </div>
    <div data-render="app">
        <logistic-zone-offcanvas create-title="{{ __('messages.new') }} {{ __('logistic_zone.singular_title') }}"
            edit-title="{{ __('messages.edit') }} {{ __('logistic_zone.singular_title') }}">
        </logistic-zone-offcanvas>
    </div>
    <x-backend.advance-filter>
        <x-slot name="title">
            <h4>{{ __('service.lbl_advanced_filter') }}</h4>
        </x-slot>
        <button type="reset" class="btn btn-danger" id="reset-filter">Reset</button>
    </x-backend.advance-filter>
@endsection

@push ('after-styles')
<link rel="stylesheet" href='{{ mix("modules/logistic/style.css") }}'>

<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push ('after-scripts')
<script src='{{ mix("modules/logistic/script.js") }}'></script>

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
                data: 'name',
                name: 'name',
                title: "{{ __('logistic_zone.lbl_name') }}",
                searchable: true,
            },
            {
                data: 'logistic_id',
                name: 'logistic_id',
                title: "{{ __('logistics.singular_title') }}",
                searchable: true,
            },
            {
                data: 'city_id',
                name: 'city_id',
                title: "{{ __('logistic_zone.cities') }}",
                searchable: false,
            },
            {
                data: 'standard_delivery_charge',
                name: 'standard_delivery_charge',
                title: "{{ __('logistic_zone.standard_delivery_charge') }}",
                searchable: false,
            },
            {
                data: 'standard_delivery_time',
                name: 'standard_delivery_time',
                title: "{{ __('logistic_zone.standard_delivery_time') }}",
                searchable: false,
            },
            {
            data: 'updated_at',
            name: 'updated_at',
            title: "{{ __('tax.lbl_updated') }}",
            width: '5%',
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
                orderColumn: [[ 6, "desc" ]],
                advanceFilter: () => {
                    return {
                    }
                }
            });
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
