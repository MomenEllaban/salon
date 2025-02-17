@extends('backend.layouts.app')

@section('title')
    {{ __($module_action) }} {{ __($module_title) }}
@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/earning/style.css') }}">
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <x-backend.section-header>
                <x-slot name="toolbar">
                    
                    <div class="input-group flex-nowrap">
                      <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
                      <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..." aria-label="Search" aria-describedby="addon-wrapping">
                    </div>
                     
                  </x-slot>
                </x-backend.section-header>
            <table id="datatable" class="table border table-responsive">
            </table>
        </div>
    </div>
    <div data-render="app">
        <earning-form-offcanvas create-title="{{ __('messages.create') }} {{ __('messages.new') }} {{ __($module_title) }}"
            edit-title="{{ __('messages.create') }} {{ __('messages.create') }} {{ __('Staff Payout') }} "></earning-form-offcanvas>
    </div>
@endsection

@push('after-styles')
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <!-- DataTables Core and Extensions -->
    <script src="{{ mix('modules/earning/script.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', (event) => {
            window.renderedDataTable = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                responsive: true,
              
                dom: '<"row align-items-center"><"table-responsive my-3" rt><"row align-items-center" <"col-md-6" l><"col-md-6" p>><"clear">',
                ajax: {
                    "type"   : "GET",
                    "url"    : '{{ route("backend.$module_name.index_data") }}',
                    "data"   : function( d ) {
                    d.search = {
                        value: $('.dt-search').val()
                    };
                    d.filter = {
                        column_status: $('#column_status').val()
                    }
                    },
                },
                columns: [
                    { data: 'first_name',name: 'first_name', title: "{{ __('messages.name') }}" ,  orderable: true },
                    { data: 'total_booking', name: 'total_booking', title: "{{ __('earning.lbl_tot_booking') }}",  orderable: false, searchable: false },
                    { data: 'total_service_amount', name: 'total_service_amount', title: "{{ __('earning.lbl_total_earning') }}",  orderable: true , searchable: false},
                    { data: 'total_commission_earn', name: 'total_commission_earn', title: "{{ __('earning.lbl_total_commission') }}", orderable: true, searchable: false},
                    { data: 'total_tips_earn', name: 'total_tips_earn', title: "{{ __('earning.lbl_total_tip') }}", orderable: true, searchable: false},
                    { data: 'total_pay', name: 'total_pay', title: "{{ __('earning.lbl_staff_earning') }}", orderable: false, searchable: false },
                    { data: 'action', name: 'action', title: "{{ __('earning.lbl_action') }}", orderable: false, searchable: false }
                ]
            });
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
    </script>
@endpush
