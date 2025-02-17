<?php

namespace App\DataTables;

use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\Constant\Models\Constant;
use Modules\CustomField\Models\CustomField;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param  QueryBuilder  $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $branch_for_list = Constant::getTypeDataKeyValue('BRANCH_SERVICE_GENDER');
        $datatable = (new EloquentDataTable($query))
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row "  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" onclick="dataTableRowCheck('.$row->id.')">';
            })
            ->addColumn('action', function ($data) {
                $module_name = 'branch';

                return view('backend.branch.action_column', compact('module_name', 'data'));
            })
            ->filterColumn('address.city', function ($query, $keyword) {
                if (! empty($keyword)) {
                    $query->whereHas('address', function ($q) use ($keyword) {
                        $q->where('city', 'like', '%'.$keyword.'%');
                    });
                }
            })
            ->filterColumn('address.postal_code', function ($query, $keyword) {
                if (! empty($keyword)) {
                    $query->whereHas('address', function ($q) use ($keyword) {
                        $q->where('postal_code', 'like', '%'.$keyword.'%');
                    });
                }
            })
            ->filterColumn('manager_id', function ($query, $keyword) {
                if (! empty($keyword)) {
                    $query->whereHas('employee', function ($q) use ($keyword) {
                        $q->where('first_name', 'like', '%'.$keyword.'%');
                        $q->orWhere('last_name', 'like', '%'.$keyword.'%');
                    });
                }
            })
            ->filterColumn('branch_for', function ($query, $keyword) {
                if (! empty($keyword)) {
                    $query->where('branch_for', 'like', $keyword.'%');
                }
            })
            ->editColumn('status', function ($row) {
                $checked = '';
                if ($row->status) {
                    $checked = 'checked="checked"';
                }

                return '
              <div class="form-check form-switch  ">
                  <input type="checkbox" data-url="'.route('backend.branch.update_status', $row->id).'" data-token="'.csrf_token().'" class="switch-status-change form-check-input"  id="datatable-row-'.$row->id.'"  name="status" value="'.$row->id.'" '.$checked.'>
              </div>
              ';
            })
            ->addColumn('image', function ($data) {
                return '<img src='.$data->feature_image." class='avatar avatar-50 rounded-pill'>";
            })
            ->editColumn('address.city', function ($data) {
                return $data->address->city ?? '';
            })
            ->editColumn('address.postal_code', function ($data) {
                return $data->address->postal_code ?? '';
            })
            ->editColumn('manager_id', function ($data) {
                return $data->employee->full_name ?? '-';
            })
            ->editColumn('branch_for', function ($data) use ($branch_for_list) {
                return view('backend.branch.select_column', compact('data', 'branch_for_list'));
            })
            ->addColumn('assign', function ($data) {
                return "<b>$data->branch_employee_count</b> <button type='button' data-assign-module='$data->id' data-assign-target='#staff-assign-form' data-assign-event='staff_assign' class='btn btn-primary btn-sm rounded btn-icon'><i class='fa-solid fa-plus'></i></button>";
            })
            ->editColumn('updated_at', function ($data) {
                $diff = Carbon::now()->diffInHours($data->updated_at);

                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
            ->orderColumns(['id'], '-:column $1')
            ->setRowId('id');

        // Custom Fields For export
        $customFieldColumns = CustomField::customFieldData($datatable, Branch::CUSTOM_FIELD_MODEL, null);

        return $datatable->rawColumns(array_merge(['action', 'status', 'branch_for', 'check', 'image', 'assign'], $customFieldColumns));
    }

    /**
     * Get query source of dataTable.
     *
     * @param  \App\Models\User  $model
     */
    public function query(Branch $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('user-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload'),
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            ['data' => 'check', 'name' => 'check', 'title' => '', 'exportable' => false, 'orderable' => false, 'searchable' => false, 'printable' => false],
            ['data' => 'image', 'name' => 'image', 'title' => __('branch.lbl_image'), 'orderable' => false, 'printable' => false, 'exportable' => false],
            ['data' => 'name', 'name' => 'name', 'title' => __('branch.lbl_name'), 'width' => '15%'],
            ['data' => 'contact_number', 'name' => 'contact_number', 'title' => __('branch.lbl_contact_number'), 'width' => '15%'],
            ['data' => 'manager_id', 'name' => 'manager_id', 'title' => __('branch.lbl_manager_name'), 'width' => '15%'],
            ['data' => 'address.city', 'name' => 'address.city', 'title' => __('branch.lbl_city'), 'width' => '15%'],
            ['data' => 'address.postal_code', 'name' => 'address.postal_code', 'title' => __('branch.lbl_postal_code'), 'width' => '10%'],
            ['data' => 'assign', 'name' => 'assign', 'title' => 'Assign', 'orderable' => false, 'searchable' => false, 'printable' => false],
            ['data' => 'branch_for', 'name' => 'branch_for', 'title' => __('branch.lbl_branch_for'), 'width' => '12%', 'printable' => false],
            ['data' => 'status', 'name' => 'status', 'title' => __('branch.lbl_status'), 'width' => '5%', 'printable' => false],
        ];
    }

    /**
     * Get filename for export.
     */
    protected function filename(): string
    {
        return 'User_'.date('YmdHis');
    }
}
