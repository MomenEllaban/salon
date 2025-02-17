<?php

namespace Modules\Product\Http\Controllers\Backend;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Modules\Product\Http\Requests\ProductVariationsRequest;
use Modules\Product\Models\Variations;
use Modules\Product\Models\VariationValue;
use Yajra\DataTables\DataTables;

class VariationsController extends Controller
{
    // use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'variations.title';
        // module name
        $this->module_name = 'variations';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);

        $this->middleware(['permission:view_product'])->only('index');
        $this->middleware(['permission:edit_product'])->only('edit', 'update');
        $this->middleware(['permission:add_product'])->only('store');
        $this->middleware(['permission:delete_product'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');
        // dd($actionType, $ids, $request->status);
        switch ($actionType) {
            case 'change-status':
                $customer = Variations::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_customer_update');
                break;

            case 'delete':
                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                Variations::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_customer_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.bulk_update')]);
    }

    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];

        $module_action = 'List';
        $columns = CustomFieldGroup::columnJsonValues(new Variations());
        $customefield = CustomField::exportCustomFields(new Variations());

        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => ' Name',
            ],
        ];
        $export_url = route('backend.variations.export');

        return view('product::backend.variations.index_datatable', compact('module_action', 'filter', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        $query_data = Variations::with('values')->get();

        $data = [];

        foreach ($query_data as $row) {
            $values = [];

            foreach ($row->values as $value) {
                $values[] = [
                    'id' => $value->id,
                    'name' => $value->name,
                ];
            }

            $data[] = [
                'id' => $row->id,
                'name' => $row->name,
                'values' => $values,
            ];
        }

        return response()->json($data);
    }

    public function index_data(Request $request)
    {
        $query = Variations::query();

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$data->id.'"  name="datatable_ids[]" value="'.$data->id.'" onclick="dataTableRowCheck('.$data->id.')">';
            })
            ->addColumn('action', function ($data) {
                return view('product::backend.variations.action_column', compact('data'));
            })
            ->editColumn('status', function ($data) {
                $checked = '';
                if ($data->status) {
                    $checked = 'checked="checked"';
                }

                return '
                              <div class="form-check form-switch">
                                  <input type="checkbox" data-url="'.route('backend.variations.update_status', $data->id).'" data-token="'.csrf_token().'" class="switch-status-change form-check-input"  id="datatable-row-'.$data->id.'"  name="status" value="'.$data->id.'" '.$checked.'>
                              </div>
                            ';
            })
            ->editColumn('updated_at', function ($data) {
                $module_name = $this->module_name;

                $diff = Carbon::now()->diffInHours($data->updated_at);

                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
            ->rawColumns(['action', 'status', 'check'])
            ->orderColumns(['id'], '-:column $1')
            ->make(true);
    }

    public function update_status(Request $request, Variations $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => 'Status Updated']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(ProductVariationsRequest $request)
    {
        $data = Variations::create($request->all());

        foreach ($request->values as $key => $value) {
            if (empty($value['value'])) {
                $value['value'] = $value['name'];
            }
            $data->values()->create($value);
        }

        $message = 'New Variation Added';

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Variations::with('values')->findOrFail($id);

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(ProductVariationsRequest $request, $id)
    {
        $data = Variations::findOrFail($id);

        $data->update($request->all());

        $values = collect($request->values);

        $ids = $values->pluck('id')->toArray();

        VariationValue::whereNotIn('id', $ids)->delete();

        foreach ($values as $key => $value) {
            $value['variation_id'] = $data->id;
            if (empty($value['value'])) {
                $value['value'] = $value['name'];
            }
            VariationValue::updateOrCreate(['id' => $value['id'] ?? null], $value);
        }

        $message = 'Variation Updated Successfully';

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        if (env('IS_DEMO')) {
            return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
        }
        $data = Variations::findOrFail($id);

        $data->values()->delete();

        $data->delete();

        $message = 'Variation Deleted Successfully';

        return response()->json(['message' => $message, 'status' => true], 200);
    }
}
