@extends('backend.layouts.app', ['isNoUISlider' => true])

@section('title')
    {{ __($module_action) }} {{ __($module_title) }}
@endsection


@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/service/style.css') }}">
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <x-backend.section-header>
                <div class="d-flex flex-wrap gap-3">
                    @if(auth()->user()->can('edit_service') || auth()->user()->can('delete_service'))
                    <x-backend.quick-action url="{{ route('backend.services.bulk_action') }}">
                        <div class="">
                            <select name="action_type" class="form-control select2 col-12" id="quick-action-type"
                                style="width:100%">
                                <option value="">{{ __('messages.no_action') }}</option>
                                @can('edit_service')
                                <option value="change-status">{{ __('messages.status') }}</option>
                                @endcan
                                @can('delete_service')
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
                    <div>
                      <button type="button" class="btn btn-secondary" data-modal="export">
                        <i class="fa-solid fa-download"></i> {{ __('messages.export') }}
                      </button>
                    </div>
                </div>
                <x-slot name="toolbar">

                    <div>
                        <div class="datatable-filter">
                            <select name="column_status" id="column_status" class="select2 form-control"
                                data-filter="select" style="width: 100%">
                                <option value="">{{__('messages.all')}}</option>
                                <option value="0" {{ $filter['status'] == '0' ? 'selected' : '' }}>
                                    {{ __('messages.inactive') }}</option>
                                <option value="1" {{ $filter['status'] == '1' ? 'selected' : '' }}>
                                    {{ __('messages.active') }}</option>
                            </select>
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
                    <button class="btn btn-outline-primary btn-group" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"><i
                            class="fa-solid fa-filter"></i>{{__('messages.advance_filter')}}</button>
                </x-slot>
            </x-backend.section-header>
            <table id="datatable" class="table table-striped border table-responsive">
            </table>
        </div>
    </div>
    <div data-render="app">
        <service-form-offcanvas   default-image="{{default_feature_image()}}"  create-title="{{ __('messages.new') }} {{ __('service.singular_title') }}"
            edit-title="{{ __('messages.edit') }} {{ __('service.singular_title') }}" :customefield="{{ json_encode($customefield) }}">
        </service-form-offcanvas>
        <assign-employee-form-offcanvas></assign-employee-form-offcanvas>
        <assign-branch-form-offcanvas></assign-branch-form-offcanvas>
        <gallery-form-offcanvas></gallery-form-offcanvas>
    </div>
    <x-backend.advance-filter>
        <x-slot name="title">
            <h4>{{ __('service.lbl_advanced_filter') }}</h4>
        </x-slot>
        <div class="form-group datatable-filter">
            <label class="form-label" for="column_category">{{ __('service.lbl_category') }}</label>
            <select name="column_category" id="column_category" class="form-control select2" data-filter="select">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group datatable-filter">
            <label class="form-label" for="column_subcategory">{{ __('service.lbl_sub_category') }}</label>
            <select name="column_subcategory" id="column_subcategory" class="form-control select2" data-filter="select">
                <option value="">All Sub-Categories</option>
                @foreach ($subcategories as $subcategory)
                    <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="reset" class="btn btn-danger" id="reset-filter">{{ __('messages.reset') }}</button>
        <div class="form-group custom-range">
            <div class="filter-slider slider-secondary"></div>
        </div>
    </x-backend.advance-filter>
@endsection

@push('after-styles')
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <script src="{{ mix('modules/service/script.js') }}"></script>
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
            // {
            //     data: 'image',
            //     name: 'image',
            //     title:  "{{ __('service.lbl_image') }}",
            //     orderable: false,
            //     width: '0%'
            // },
            {
                data: 'name',
                name: 'name',
                title: "{{ __('service.lbl_name') }}"
            },
            {
                data: 'default_price',
                name: 'default_price',
                title: "{{ __('service.lbl_default_price') }}"
            },
            {
                data: 'duration_min',
                name: 'duration_min',
                title: "{{ __('service.lbl_duration') }}"
            },

            {
                data: 'category_id',
                name: 'category_id',
                title: "{{ __('service.lbl_category_id') }}"
            },
            @if (!$is_single_branch)
                {
                    data: 'branches_count',
                    name: 'branches_count',
                    title: "{{ __('service.lbl_branches') }}",
                    orderable: true,
                    searchable: false,
                },
            @endif
            @if(auth()->user()->hasRole('admin'))
            {   data: 'employee_count', 
                name: 'employee_count',  
                title: "{{ __('service.lbl_staffs') }}", 
                orderable: true, 
                searchable: false,  
            },
            @endif
            {
                data: 'status',
                name: 'status',
                orderable: true,
                searchable: true,
                title: "{{ __('service.lbl_status') }}",
                width: '5%'
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

        const customFieldColumns = JSON.parse(@json($columns))

        let finalColumns = [
            ...columns,
            ...customFieldColumns,
            ...actionColumn
        ]

        // document.addEventListener('DOMContentLoaded', (event) => {
        //     initDatatable({
        //         url: '{{ route("backend.$module_name.index_data") }}',
        //         finalColumns,
        //         advanceFilter: () => {
        //             return {
        //               category_id: $('#column_category').val(), // Add category filter value
        //               sub_category_id: $('#column_subcategory').val(), // Add subcategory filter value
        //             }
        //         }
        //     })

        // })

        // $('#reset-filter').on('click', function(e) {
        //   $('#column_category').val('')
        //   $('#column_subcategory').val('')
        //   window.renderedDataTable.ajax.reload(null, false)
        // })

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.$module_name.index_data") }}',
                finalColumns,
                orderColumn: [[ 8, "desc" ]],
                advanceFilter: () => {
                    return {
                        category_id: $('#column_category').val(), // Add category filter value
                        sub_category_id: $('#column_subcategory').val(), // Add subcategory filter value
                    }
                }
            });

            // Event listener for category selection change
            $('#column_category').on('change', function() {
                var selectedCategoryId = $(this).val();
                filterSubcategories(selectedCategoryId);
            });

            // Function to filter subcategories based on the selected category
            function filterSubcategories(selectedCategoryId) {
                var $subcategorySelect = $('#column_subcategory');
                $subcategorySelect.empty();

                // Add the default option
                $subcategorySelect.append('<option value="">All Sub-Categories</option>');

                if (selectedCategoryId) {
                    var filteredSubcategories = @json($subcategories);
                    filteredSubcategories = filteredSubcategories.filter(function(subcategory) {
                        return subcategory.parent_id == selectedCategoryId;
                    });

                    filteredSubcategories.forEach(function(subcategory) {
                        $subcategorySelect.append('<option value="' + subcategory.id + '">' + subcategory
                            .name + '</option>');
                    });
                } else {
                    @foreach ($subcategories as $subcategory)
                        $subcategorySelect.append(
                            '<option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>');
                    @endforeach
                }
            }

            $('#reset-filter').on('click', function(e) {
                $('#column_category').val('');
                $('#column_subcategory').val('');
                filterSubcategories('');
                window.renderedDataTable.ajax.reload(null, false);
            });

            // Initialize subcategory options based on the initial selected category
            filterSubcategories($('#column_category').val());
        });


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
