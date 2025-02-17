<?php

namespace Modules\Slider\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Constant\Models\Constant;
use Modules\CustomField\Models\CustomFieldGroup;
use Modules\Slider\Http\Requests\SliderRequest;
use Modules\Slider\Models\Slider;
use Yajra\DataTables\DataTables;

class SlidersController extends Controller
{
    // use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'slider.title';

        // module name
        $this->module_name = 'app_banners';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => 'fa-regular fa-sun',
            'module_name' => $this->module_name,
        ]);
        $this->middleware(['permission:view_app_banner'])->only('index');
        $this->middleware(['permission:edit_app_banner'])->only('edit', 'update');
        $this->middleware(['permission:add_app_banner'])->only('store');
        $this->middleware(['permission:delete_app_banner'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $module_action = 'List';

        $filter = [
            'status' => $request->status,
        ];
        $columns = CustomFieldGroup::columnJsonValues(new Slider());
        $module_action = 'List';

        return view('slider::backend.sliders.index_datatable', compact('module_action', 'filter', 'columns'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        $query_data = Constant::where('type', 'SLIDER_TYPES')->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->value,
                'name' => $row->name,
            ];
        }

        return response()->json($data);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $module_name = $this->module_name;
        $query = Slider::query();
        $filter = $request->filter;
        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row "  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" onclick="dataTableRowCheck('.$row->id.')">';
            })
            ->addColumn('action', function ($data) use ($module_name) {
                return view('slider::backend.sliders.action_column', compact('module_name', 'data'));
            })
            ->editColumn('link_id', function ($data) {
                return $data->module->name ?? '-';
            })
            ->editColumn('link', function ($data) {
                return $data->link ?? '-';
            })
            ->editColumn('status', function ($row) {
                $checked = '';
                if ($row->status) {
                    $checked = 'checked="checked"';
                }

                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="'.route('backend.app_banners.update_status', $row->id).'" data-token="'.csrf_token().'" class="switch-status-change form-check-input"  id="datatable-row-'.$row->id.'"  name="status" value="'.$row->id.'" '.$checked.'>
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

            ->orderColumns(['id'], '-:column $1');

        return $datatable->rawColumns(array_merge(['action', 'status', 'check']))
            ->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(SliderRequest $request)
    {
        $data = $request->except('feature_image');
        $query = Slider::create($data);

        if ($request->hasFile('feature_image')) {
            storeMediaFile($query, $request->file('feature_image'));
        }

        $message = __('messages.create_form', ['form' => __($this->module_title)]);

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

        $data = Slider::findOrFail($id);
dd($data);
        return view('slider::backend.sliders.show', compact('module_action', "$data"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $module_action = 'Edit';

        $data = Slider::findOrFail($id);

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(SliderRequest $request, $id)
    {
        $query = Slider::findOrFail($id);
        $data = $request->except('feature_image');

        $query->update($data);

        $message = Str::singular($this->module_title).' Updated Successfully';

        if ($request->hasFile('feature_image')) {
            storeMediaFile($query, $request->file('feature_image'));
        }

        $message = __('messages.update_form', ['form' => __($this->module_title)]);

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

        $data = Slider::findOrFail($id);

        $data->delete();

        $message = __('messages.delete_form', ['form' => __('slider.singular_title')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function restore($id)
    {
        $module_action = 'Restore';

        $data = Slider::withTrashed()->find($id);
        $data->restore();

        return redirect('app/app-banners');
    }

    public function update_status(Request $request, Slider $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('branch.status_update')]);
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $services = Slider::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_slider_update');
                break;

            case 'delete':

                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                Slider::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_slider_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.bulk_update')]);
    }

    public function uniqueSliders(Request $request)
    {
        $sliderName = $request->input('name'); // The name field is 'name', not 'service'
        $sliderId = $request->input('id'); // The unique check will be based on 'id'

        $isUnique = true;
        if (!$sliderId) {
            $isUnique = Slider::where('name', $sliderName)
                ->doesntExist();
        }

        return response()->json(['isUnique' => $isUnique]);
    }
}
