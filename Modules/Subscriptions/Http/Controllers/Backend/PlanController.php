<?php

namespace Modules\Subscriptions\Http\Controllers\Backend;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Modules\Subscriptions\Http\Requests\PlanRequest;
use Modules\Subscriptions\Models\Plan;
use Modules\Subscriptions\Models\PlanLimitation;
use Modules\Subscriptions\Models\PlanLimitationMapping;
use Yajra\DataTables\DataTables;

class PlanController extends Controller
{
    public function __construct()
    {
        // Page Title
        $this->module_title = 'plan.title';

        // module name
        $this->module_name = 'plans';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $branches = Plan::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_plan_update');
                break;

            case 'delete':
                Plan::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_plan_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.bulk_update')]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index()
    {
        $module_action = 'List';
        $module_name = $this->module_name;

        $columns = CustomFieldGroup::columnJsonValues(new Plan());

        $customefield = CustomField::exportCustomFields(new Plan());

        return view('subscriptions::backend.plan.index', compact('module_action', 'module_name', 'columns', 'customefield'));
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = Plan::query();

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
            ->addColumn('action', function ($data) {
                return view('subscriptions::backend.plan.action_column', compact('data'));
            })
            ->editColumn('status', function ($row) {
                $checked = '';
                if ($row->status) {
                    $checked = 'checked="checked"';
                }

                return '
                <div class="form-check form-switch ">
                    <input type="checkbox" data-url="'.route('backend.subscription.plans.update_status', $row->id).'" data-token="'.csrf_token().'" class="switch-status-change form-check-input"  id="datatable-row-'.$row->id.'"  name="status" value="'.$row->id.'" '.$checked.'>
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
            ->orderColumns(['id'], '-:column $1');

        // Custom Fields For export
        $customFieldColumns = CustomField::customFieldData($datatable, Plan::CUSTOM_FIELD_MODEL, null);

        return $datatable->rawColumns(array_merge(['action', 'status', 'check'], $customFieldColumns))
            ->toJson();
    }

    public function index_list(Request $request)
    {
        $term = trim($request->q);

        $query_data = PlanLimitation::where('status', 1)
            ->where(function ($q) {
                if (! empty($term)) {
                    $q->orWhere('name', 'LIKE', "%$term%");
                }
            })->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'name' => $row->name,
                'limit' => $row->limit,
            ];
        }

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Renderable
     */
    public function store(PlanRequest $request)
    {
        $data = $request->all();

        if ($data['duration'] == 'undefined' || $data['duration'] == '') {
            $data['duration'] = 1;
        }

        if ($data['status'] != 1) {
            $data['status'] = 0;
        }

        $data['identifier'] = strtolower($data['name']);

        $plandata = Plan::create($data);

        $plan_id = $plandata['id'];

        $limitation_data = json_decode($data['data']);

        if (count($limitation_data) != 0 && $data['planlimitation'] === 'Limited') {
            foreach ($limitation_data as $item) {
                PlanLimitationMapping::create(['plan_id' => $plan_id,
                    'planlimitation_id' => $item->planlimitation_id,
                    'limit' => $item->limit,
                ]);
            }
        }

        // To add custom fields data
        if ($request->custom_fields_data) {
            $plandata->updateCustomFieldData(json_decode($request->custom_fields_data));
        }

        $message = __('messages.create_form', ['form' => __('plan.singular_title')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function edit($id)
    {
        $plan_data = Plan::with('planLimitation')->findOrFail($id);

        $plan_data->planLimitation = $plan_data->planLimitation->each(function ($data) {
            $data['name'] = $data->limitation_data->name;

            return $data;
        });

        if (! is_null($plan_data)) {
            $custom_field_data = $plan_data->withCustomFields();
            $plan_data['custom_field_data'] = $custom_field_data->custom_fields_data->toArray();
        }

        return response()->json(['data' => $plan_data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Renderable
     */
    public function update(PlanRequest $request, $id)
    {
        $data = Plan::findOrFail($id);

        $plan_data = $request->all();

        if ($plan_data['type'] == 2) {
            $plan_data['duration'] = 1;
        }

        $data->update($plan_data);

        // To add custom fields data
        if ($request->custom_fields_data) {
            $data->updateCustomFieldData(json_decode($request->custom_fields_data));
        }

        $limitation_data = json_decode($plan_data['data']);

        PlanLimitationMapping::where('plan_id', $id)->forceDelete();

        if (count($limitation_data) != 0 && $plan_data['planlimitation'] === 'Limited') {
            foreach ($limitation_data as $item) {
                PlanLimitationMapping::create(['plan_id' => $id,
                    'planlimitation_id' => $item->planlimitation_id,
                    'limit' => $item->limit,
                ]);
            }
        }

        $message = __('messages.update_form', ['form' => __('plan.singular_title')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $data = Plan::findOrFail($id);

        $data->delete();

        $message = __('messages.delete_form', ['form' => __('plan.singular_title')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function trashed()
    {
        $module_name_singular = Str::singular($this->module_name);

        $module_action = 'Trash List';

        $data = Plan::with('user')->onlyTrashed()->orderBy('deleted_at', 'desc')->paginate();

        return view('subscriptions::backend.plan.trash', compact('data', 'module_name_singular', 'module_action'));
    }

    public function restore($id)
    {
        $data = Plan::withTrashed()->find($id);
        $data->restore();

        $message = __('messages.plan_data');

        return redirect('app/subscription/plans');
    }

    public function update_status(Request $request, Plan $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('branch.status_update')]);
    }
}
