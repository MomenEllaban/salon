<?php

namespace Modules\Category\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Category\Http\Requests\CategoryRequest;
use Modules\Category\Models\Category;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Yajra\DataTables\DataTables;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

class CategoriesController extends Controller
{
    // use Authorizable;

    protected string $exportClass = '\App\Exports\CategoryExport';

    public function __construct()
    {
        // Page Title
        $this->module_title = 'category.title';

        // module name
        $this->module_name = 'categories';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => 'fa-regular fa-sun',
            'module_name' => $this->module_name,
        ]);
        $this->middleware(['permission:view_category'])->only('index');
        $this->middleware(['permission:view_subcategory'])->only('index_nested');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];
        $module_name = $this->module_name;
        $module_action = 'List';
        $columns = CustomFieldGroup::columnJsonValues(new Category());
        $customefield = CustomField::exportCustomFields(new Category());

        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => 'Name',
            ],
            [
                'value' => 'status',
                'text' => 'Status',
            ],
            [
                'value' => 'Date',
                'text' => 'Date',
            ],
        ];
        $export_url = route('backend.categories.export');

        return view('category::backend.categories.index_datatable', compact('module_name', 'filter', 'module_action', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url'));
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

        $query_data = Category::where(function ($q) use ($parentID) {
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

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $branches = Category::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_category_update');
                break;

            case 'delete':
                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }

                Category::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_category_update');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.bulk_update')]);
    }

    public function update_status(Request $request, Category $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('branch.status_update')]);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $module_name = $this->module_name;
        $query = Category::query()->with('media')->whereNull('parent_id');

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }

        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" onclick="dataTableRowCheck('.$row->id.')">';
            })
            // ->editColumn('name', function ($row) use ($module_name) {
            //     return "<a href='".route('backend.'.$module_name.'.index_nested', ['category_id' => $row->id])."'>$row->name</a>";
            // })
            ->editColumn('name', function ($row) use ($module_name) {
                $link = route('backend.' . $module_name . '.index_nested', ['category_id' => $row->id]);
                $data = $row;
                $image = optional($data)->feature_image ?? default_user_avatar();
                $name = optional($data)->name ?? default_user_name();
                return view('product::backend.category.category_id', compact('image', 'link','name'));
            })
            ->addColumn('action', function ($data) use ($module_name) {
                return view('category::backend.categories.action_column', compact('module_name', 'data'));
            })
            ->addColumn('image', function ($data) {
                return "<img src='".$data->feature_image."' class='avatar avatar-50 rounded-pill'>";
            })
            ->editColumn('status', function ($row) {
                $checked = '';
                if ($row->status) {
                    $checked = 'checked="checked"';
                }

                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="'.route('backend.categories.update_status', $row->id).'" data-token="'.csrf_token().'" class="switch-status-change form-check-input"  id="datatable-row-'.$row->id.'"  name="status" value="'.$row->id.'" '.$checked.'>
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
            ->editColumn('created_at', function ($data) {
                $module_name = $this->module_name;

                $diff = Carbon::now()->diffInHours($data->created_at);

                if ($diff < 25) {
                    return $data->created_at->diffForHumans();
                } else {
                    return $data->created_at->isoFormat('llll');
                }
            })
            ->orderColumns(['id'], '-:column $1');

        // Custom Fields For export
        $customFieldColumns = CustomField::customFieldData($datatable, Category::CUSTOM_FIELD_MODEL, null);

        return $datatable->rawColumns(array_merge(['action', 'status', 'image', 'check', 'name'], $customFieldColumns))
            ->toJson();
    }

    public function index_nested(Request $request)
    {
        $categories = Category::with('mainCategory')->whereNull('parent_id')->get();

        $filter = [
            'status' => $request->status,
        ];
        $parentID = $request->category_id ?? null;

        $module_action = 'List';

        $module_title = 'category.sub_categories';
        $columns = CustomFieldGroup::columnJsonValues(new Category());
        $customefield = CustomField::exportCustomFields(new Category());

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
            [
                'value' => 'Date',
                'text' => 'Date',
            ],
        ];
        $export_url = route('backend.sub-categories.export');

        return view('category::backend.categories.index_nested_datatable', compact('parentID', 'module_action', 'filter', 'categories', 'module_title', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url'));
    }

    public function index_nested_data(Request $request, Datatables $datatable)
    {
        $module_name = $this->module_name;
        $query = Category::query()
            ->select('categories.*', 'mainCategory.name as mainCategory_name')
            ->leftJoin('categories as mainCategory', 'mainCategory.id', '=', 'categories.parent_id')
            ->whereNotNull('categories.parent_id')
            ->whereNull('categories.deleted_at');
        
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

            // ->addColumn('image', function ($data) {
            //     return '<img src='.$data->feature_image." class='avatar avatar-50 rounded-pill'>";
            // })
            ->editColumn('name', function ($data) {
                return view('backend.branch.branch_id', compact('data'));
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
                        <input type="checkbox" data-url="'.route('backend.categories.update_status', $row->id).'" data-token="'.csrf_token().'" class="switch-status-change form-check-input" id="datatable-row-'.$row->id.'" name="status" value="'.$row->id.'" '.$checked.'>
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
        $customFieldColumns = CustomField::customFieldData($datatable, Category::CUSTOM_FIELD_MODEL, null);
    
        return $datatable->rawColumns(array_merge(['action', 'status', 'image', 'check'], $customFieldColumns))
            ->toJson();
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(CategoryRequest $request)
    {
        $data = $request->except('feature_image');

        $query = Category::create($data);

        if ($request->custom_fields_data) {
            $query->updateCustomFieldData(json_decode($request->custom_fields_data));
        }

        storeMediaFile($query, $request->file('feature_image'));

        $message = __('messages.create_form', ['form' => __('category.singular_title')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $module_action = 'Show';

        $data = Category::with('mainCategory')->findOrFail($id);

        return view('category::backend.categories.show', compact('module_action', "$data"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Category::with('mainCategory')->findOrFail($id);

        if (! is_null($data)) {
            $custom_field_data = $data->withCustomFields();
            $data['custom_field_data'] = collect($custom_field_data->custom_fields_data)
                ->filter(function ($value) {
                    return $value !== null;
                })
                ->toArray();
        }

        $data['feature_image'] = $data->feature_image;
        $data['category_name'] = $data->mainCategory->name ?? null;

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(CategoryRequest $request, $id)
    {
        $query = Category::findOrFail($id);

        $data = $request->except('feature_image');

        $query->update($data);

        if ($request->custom_fields_data) {
            $query->updateCustomFieldData(json_decode($request->custom_fields_data));
        }

        if ($request->hasFile('feature_image')) {
            storeMediaFile($query, $request->file('feature_image'), 'feature_image');
        }
        if ($request->feature_image == null) {
            $query->clearMediaCollection('feature_image');
        }

        $message = __('messages.update_form', ['form' => __('category.singular_title')]);

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

        $data = Category::findOrFail($id);

        $data->delete();

        $message = __('messages.delete_form', ['form' => __('category.singular_title')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function subCategoryExport(Request $request)
    {
        $this->exportClass = '\App\Exports\SubCategoryExport';

        return $this->export($request);
    }
}
