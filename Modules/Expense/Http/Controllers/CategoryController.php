<?php

namespace Modules\Expense\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Expense\Models\Expense;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Modules\Expense\Models\ExpenseCategory;
use Modules\CustomField\Models\CustomFieldGroup;
use Modules\CustomField\Models\CustomField;

class CategoryController extends Controller
{

    public function __construct()
    {
        // Page Title
        $this->module_title = 'expense.expense_category';

        // module name
        $this->module_name = 'expenses-categories';

        // directory path of the module
        $this->module_path = 'expense::backend';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => 'fa-regular fa-sun',
            'module_name' => $this->module_name,
            'module_path' => $this->module_path,
        ]);
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
        return view('expense::backend.category.index_datatable', compact('module_action', 'filter'));

    }

        /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        $term = trim($request->q);
        $parentID = $request->parent_id;
        $query_data = ExpenseCategory::where(function ($q) use ($parentID) {
            if (! empty($term)) {
                $q->orWhere('name', 'LIKE', "%$term%");
            }
            if (isset($parentID) && $parentID != 0) {
                $q->where('parent_id', $parentID);
            } else {
                $q->whereNull('parent_id');
            }
        })
            ->where('status', 1) // Add this line to filter by status
            ->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'name' => $row->name,
            ];
        }

        return response()->json($data);
    }

    public function update_status(Request $request, ExpenseCategory $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('branch.status_update')]);
    }

    public function index_data(DataTables $datatable, Request $request)
    {
        $query = ExpenseCategory::query();

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
                return view('expense::backend.category.action_column', compact('data'));
            })
            ->editColumn('status', function ($row) {
                $checked = '';
                if ($row->status) {
                    $checked = 'checked="checked"';
                }

                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="'.route('backend.expenses-categories.update_status', $row->id).'" data-token="'.csrf_token().'" class="switch-status-change form-check-input"  id="datatable-row-'.$row->id.'"  name="status" value="'.$row->id.'" '.$checked.'>
                    </div>
                ';
            })
            ->editColumn('category_code', function ($data) {
                return $data->category_code;
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

        switch ($actionType) {
            case 'change-status':
                $branches = ExpenseCategory::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_tax_update');//note: to be changed in translation
                break;

            case 'delete':
                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                ExpenseCategory::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_tax_delete');//note: to be changed in translation
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function index_nested(Request $request)
    {
        $categories = ExpenseCategory::with('mainCategory')->whereNull('parent_id')->get();

        $filter = [
            'status' => $request->status,
        ];
        $parentID = $request->category_id ?? null;

        $module_action = 'List';

        $module_title = __('expense.sub_categories');
        $columns = CustomFieldGroup::columnJsonValues(new ExpenseCategory());
        $customefield = CustomField::exportCustomFields(new ExpenseCategory());

        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => 'Name',
            ],
            [
                'value' => 'category_name',
                'text' => 'Category Name',
            ],
            [
                'value' => 'status',
                'text' => 'Status',
            ],
        ];
        $export_url = route('backend.expenses-sub-categories.export');

        return view('expense::backend.category.index_nested_datatable', compact('parentID', 'module_action', 'filter', 'categories', 'module_title',
         'columns', 'customefield',
          'export_import', 'export_columns', 'export_url'));
    }

    public function index_nested_data(Request $request, Datatables $datatable)
    {
        $module_name = $this->module_name;
        $query = ExpenseCategory::query()
            ->select('expense_categories.*', 'mainCategory.name as mainCategory_name')
            ->leftJoin('expense_categories as mainCategory', 'mainCategory.id', '=', 'expense_categories.parent_id')
            ->whereNotNull('expense_categories.parent_id')
            ->whereNull('expense_categories.deleted_at');

        
        $filter = $request->filter;
    
        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('categories.status', $filter['column_status']);
            }
            if (isset($filter['column_category'])) {
                $query->where('categories.parent_id', $filter['column_category']);
            }
        }
    
        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row" id="datatable-row-'.$row->id.'" name="datatable_ids[]" value="'.$row->id.'" onclick="dataTableRowCheck('.$row->id.')">';
            })
            ->addColumn('action', function ($data) use ($module_name) {
                return view('category::backend.categories.sub_action_column', compact('module_name', 'data'));
            })

            ->editColumn('name', function ($data) {
                return $data->name;
            })
            ->editColumn('mainCategory.name', function ($data) {
                return $data->mainCategory->name ?? '-';
            })
            ->orderColumn('mainCategory_name', function ($query, $order) {
                $query->orderBy('mainCategory_name', $order);
            })
            ->editColumn('status', function ($row) {
                $checked = '';
                if ($row->status) {
                    $checked = 'checked="checked"';
                }
    
                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="'.route('backend.expenses-categories.update_status', $row->id).'" data-token="'.csrf_token().'" class="switch-status-change form-check-input" id="datatable-row-'.$row->id.'" name="status" value="'.$row->id.'" '.$checked.'>
                    </div>
                ';
            })
            ->editColumn('updated_at', function ($data) {
                $diff = Carbon::now()->diffInHours($data->updated_at);
    
                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
            ->editColumn('created_at', function ($data) {
                $diff = Carbon::now()->diffInHours($data->created_at);
    
                if ($diff < 25) {
                    return $data->created_at->diffForHumans();
                } else {
                    return $data->created_at->isoFormat('llll');
                }
            })
            ->orderColumns(['id'], '-:column $1');
    
        // Custom Fields For export
        $customFieldColumns = CustomField::customFieldData($datatable, ExpenseCategory::CUSTOM_FIELD_MODEL, null);
    
        return $datatable->rawColumns(array_merge(['action', 'status', 'image', 'check'], $customFieldColumns))
            ->toJson();
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $query = ExpenseCategory::create($data);

        $message = __('messages.create_form', ['form' => __($this->module_title)]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $module_action = 'Edit';

        $data = ExpenseCategory::findOrFail($id);

        return response()->json(['data' => $data, 'status' => true]);    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = ExpenseCategory::findOrFail($id);

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

        $data = ExpenseCategory::findOrFail($id);

        $data->delete();

        $message = __('messages.delete_form', ['form' => __($this->module_title)]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }
}
