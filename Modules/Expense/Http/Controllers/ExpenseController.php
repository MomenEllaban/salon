<?php

namespace Modules\Expense\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Expense\Models\Expense;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class ExpenseController extends Controller
{

    public function __construct()
    {
        // Page Title
        $this->module_title = 'expense.title';

        // module name
        $this->module_name = 'expense';

        // directory path of the module
        $this->module_path = 'expense::backend';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => 'fa-regular fa-sun',
            'module_name' => $this->module_name,
            'module_path' => $this->module_path,
        ]);
        // $this->middleware(['permission:view_expense'])->only('index');
        // $this->middleware(['permission:edit_expense'])->only('edit', 'update');
        // $this->middleware(['permission:add_expense'])->only('store');
        // $this->middleware(['permission:delete_expense'])->only('destroy');
        $this->middleware(['permission:view_tax'])->only('index');
        $this->middleware(['permission:edit_tax'])->only('edit', 'update');
        $this->middleware(['permission:add_tax'])->only('store');
        $this->middleware(['permission:delete_tax'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $module_action = 'List';
        $filter = [
            'status' => $request->status,
        ];
        return view('expense::backend.expense.index_datatable', compact('module_action', 'filter'));

    }

        /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        $term = trim($request->q);

        if (empty($term)) {
            return response()->json([]);
        }

        $query_data = Expense::where('name', 'LIKE', "%$term%")->orWhere('slug', 'LIKE', "%$term%")->limit(7)->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'text' => $row->name.' (Slug: '.$row->slug.')',
            ];
        }

        return response()->json($data);
    }

    public function update_status(Request $request, Expense $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('branch.status_update')]);
    }

    public function index_data(DataTables $datatable, Request $request)
    {
        $query = Expense::query()->with(['expenseCategory', 'expenseSubCategory', 'branch']);

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }

        return $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row "  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" onclick="dataTableRowCheck('.$row->id.')">';
            })
            ->addColumn('action', function ($data) {
                return view('expense::backend.expense.action_column', compact('data'));
            })
            ->editColumn('status', function ($row) {
                $checked = '';
                if ($row->status) {
                    $checked = 'checked="checked"';
                }

                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="'.route('backend.expense.update_status', $row->id).'" data-token="'.csrf_token().'" class="switch-status-change form-check-input"  id="datatable-row-'.$row->id.'"  name="status" value="'.$row->id.'" '.$checked.'>
                    </div>
                ';
            })
            ->editColumn('amount', function ($data) {
                $value = \Currency::format($data->amount ?? 0);

                return $value;
            })
            ->editColumn('reference_number', function ($data) {
                return $data->reference_number;
            })
            ->editColumn('date', function ($data) {
                return $data->date;
            })
            ->editColumn('note', function ($data) {
                return $data->note;
            })
            ->editColumn('category', function ($data) {
                return $data->expenseCategory->name;
            })
            ->editColumn('subcategory', function ($data) {
                return $data->expenseSubCategory?->name;
            })
            ->editColumn('branch', function ($data) {
                return $data->branch->name;
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
            ->rawColumns(['action', 'status', 'check', 'type'])
            ->orderColumns(['id'], '-:column $1')
            ->make(true);
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        // dd($actionType, $ids, $request->status);
        switch ($actionType) {
            case 'change-status':
                $branches = Expense::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_tax_update');//note: to be changed in translation
                break;

            case 'delete':
                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                Expense::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_tax_delete');//note: to be changed in translation
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $query = Expense::create($data);

        $message = __('messages.create_form', ['form' => __($this->module_title)]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $module_action = 'Edit';

        $data = Expense::findOrFail($id);

        return response()->json(['data' => $data, 'status' => true]);    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = Expense::findOrFail($id);

        $data->update($request->all());

        $message = __('messages.update_form', ['form' => __($this->module_title)]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (env('IS_DEMO')) {
            return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
        }

        $data = Expense::findOrFail($id);

        $data->delete();

        $message = __('messages.delete_form', ['form' => __($this->module_title)]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }
}
