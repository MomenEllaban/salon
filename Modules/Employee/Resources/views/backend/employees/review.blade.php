@extends('backend.layouts.app', ['isNoUISlider' => true])

@section('title')
{{ $module_title }}
@endsection



@push('after-styles')
<link rel="stylesheet" href="{{ mix('modules/service/style.css') }}">
@endpush

@section('content')
<div class="card">
  <div class="card-body">
    <x-backend.section-header>

      <div class="d-flex flex-wrap gap-3">
        <x-backend.quick-action url="{{ route('backend.employees.bulk_action_review') }}">
          <div class="">
            <select name="action_type" class="form-control select2 col-12" id="quick-action-type" style="width:100%">
              <option value="">{{ __('messages.no_action') }}</option>
              @can('delete_review')
              <option value="delete">{{ __('messages.delete') }}</option>
              @endcan
            </select>
          </div>
        </x-backend.quick-action>
        <div>
          <button type="button" class="btn btn-secondary" data-modal="export">
            <i class="fa-solid fa-download"></i> {{ __('messages.export') }}
          </button>
{{--          <button type="button" class="btn btn-secondary" data-modal="import">--}}
{{--            <i class="fa-solid fa-upload"></i> Import--}}
{{--          </button>--}}
        </div>
      </div>
      <x-slot name="toolbar">
        <div>
        </div>
        <div class="input-group flex-nowrap">
          <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
          <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..." aria-label="Search" aria-describedby="addon-wrapping">
        </div>
      </x-slot>
    </x-backend.section-header>
    <table id="datatable" class="table table-striped border table-responsive">
    </table>
  </div>
</div>
<x-backend.advance-filter>
  <x-slot name="title">
    <h4>Advanced Filter</h4>
  </x-slot>
</x-backend.advance-filter>
</div>
@endsection

@push('after-styles')
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
<script src="{{ asset('js/form-modal/index.js') }}" defer></script>
<!-- DataTables Core and Extensions -->
<script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

<script type="text/javascript" defer>
  const range_flatpicker = document.querySelectorAll('.booking-date-range')
  Array.from(range_flatpicker, (elem) => {
    if (typeof flatpickr !== typeof undefined) {
      flatpickr(elem, {
        mode: "range",
        dateFormat: "d-m-Y",
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
    //  {
    //   data: 'image',
    //   name: 'image',
    //   title: "{{ __('employee.lbl_image') }}",
    //   orderable: false,
    //   width: '0%'
    // }, 
    {
      data: 'user_id',
      name: 'user_id',
      title: "{{ __('customer.singular_title') }}",
      width: '10%'
    },
    {
      data: 'employee_id',
      name: 'employee_id',
      title: "{{ __('employee.singular_title') }}",
      width: '10%'
    },
    {
      data: 'review_msg',
      name: 'review_msg',
      title: "{{ __('employee.lbl_message') }}",
      width: '10%'
    },
    {
      data: 'rating',
      name: 'rating',
      title: "{{ __('employee.lbl_rating') }}",
      width: '5%'
    },
    {
      data: 'updated_at',
      name: 'updated_at',
      title: "{{ __('employee.lbl_updated') }}",
      width: '5%'
    }

  ]

  const actionColumn = [{
    data: 'action',
    name: 'action',
    orderable: false,
    searchable: false,
    title: "{{ __('employee.lbl_action') }}",
    width: '5%'
  }]


  let finalColumns = [
    ...columns,
    ...actionColumn
  ]


  document.addEventListener('DOMContentLoaded', (event) => {
    initDatatable({
      url: '{{ route("backend.employees.review_data") }}',
      finalColumns,
      orderColumn: [[ 5, "desc" ]],
      advanceFilter: () => {
        return {
          booking_date: $('#booking_date').val(),

        }
      }
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
  })
</script>
@endpush
