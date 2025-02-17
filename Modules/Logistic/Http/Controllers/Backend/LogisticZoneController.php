<?php

namespace Modules\Logistic\Http\Controllers\Backend;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Logistic\Http\Requests\ZoneRequest;
use Modules\Logistic\Models\LogisticZone;
use Modules\Logistic\Models\LogisticZoneCity;
use Yajra\DataTables\DataTables;

class LogisticZoneController extends Controller
{
    // use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'logistic_zone.title';
        // module name
        $this->module_name = 'logistic-zones';

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
        // dd($actionType, $ids, $request->status);
        switch ($actionType) {
            case 'change-status':
                $customer = LogisticZone::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_customer_update');
                break;

            case 'delete':
                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                LogisticZone::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_customer_delete');
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
     * @return Response
     */
    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];

        $module_action = 'List';

        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => ' Name',
            ],
        ];
        $export_url = route('backend.logistic-zones.export');

        return view('logistic::backend.zone.index_datatable', compact('module_action', 'filter', 'export_import', 'export_columns', 'export_url'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        $query_data = LogisticZone::isActive()->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'text' => $row->name,
            ];
        }

        return response()->json(['data' => $data, 'status' => true]);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = LogisticZone::with('cities');

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }

        return $datatable->eloquent($query)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$data->id.'"  name="datatable_ids[]" value="'.$data->id.'" onclick="dataTableRowCheck('.$data->id.')">';
            })
            ->addColumn('action', function ($data) {
                return view('logistic::backend.zone.action_column', compact('data'));
            })
            ->editColumn('logistic_id', function ($data) {
                return $data->logistic->name ?? '-';
            })
            ->editColumn('standard_delivery_charge', function ($data) {
                return \Currency::format($data->standard_delivery_charge);
            })
            ->editColumn('standard_delivery_time', function ($data) {
                return $data->standard_delivery_time ?? '-';
            })
            ->editColumn('city_id', function ($data) {
                return view('logistic::backend.zone.city_column', compact('data'));
            })
            ->orderColumn('city_id', function ($query, $order) {
                $query->select('logistic_zones.*')
                      ->leftJoin('logistic_zone_city', 'logistic_zone_city.logistic_zone_id', '=', 'logistic_zones.id')
                      ->leftJoin('cities', 'cities.id', '=', 'logistic_zone_city.city_id')
                      ->orderBy('cities.name', $order);
            })

            ->editColumn('status', function ($data) {
                $checked = '';
                if ($data->status) {
                    $checked = 'checked="checked"';
                }

                return '
                            <div class="form-check form-switch">
                                <input type="checkbox" data-url="'.route('backend.logistic-zones.update_status', $data->id).'" data-token="'.csrf_token().'" class="switch-status-change form-check-input"  id="datatable-row-'.$data->id.'"  name="status" value="'.$data->id.'" '.$checked.'>
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
            ->toJson();
    }

    public function update_status(Request $request, LogisticZone $id)
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
    public function store(ZoneRequest $request)
    {
        $data = $request->all();

        $data['standard_delivery_charge'] = $request->standard_delivery_charge ? $request->standard_delivery_charge : 0.00;
        $data['standard_delivery_time'] = $request->standard_delivery_time ? $request->standard_delivery_time : '1 Day';
        $logisticZone = LogisticZone::create($data);

        foreach ($request->city_id as $city_id) {
            $logisticZoneCity = new LogisticZoneCity;
            $logisticZoneCity->logistic_id = $logisticZone->logistic_id;
            $logisticZoneCity->logistic_zone_id = $logisticZone->id;
            $logisticZoneCity->city_id = $city_id;
            $logisticZoneCity->save();
        }

        $message = 'Shipping Zone added successfully!';

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
        $data = LogisticZone::findOrFail($id);

        $data->city_id = $data->cities->pluck('id')->toArray();

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(ZoneRequest $request, $id)
    {
        $data = LogisticZone::findOrFail($id);

        $data->update($request->all());

        LogisticZoneCity::where('logistic_zone_id', $data->id)->delete();

        foreach ($request->city_id as $city_id) {
            $logisticZoneCity = new LogisticZoneCity;
            $logisticZoneCity->logistic_id = $data->logistic_id;
            $logisticZoneCity->logistic_zone_id = $data->id;
            $logisticZoneCity->city_id = $city_id;
            $logisticZoneCity->save();
        }

        $message = 'Shipping Zone updated successfully!';

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
        $data = LogisticZone::findOrFail($id);

        $data->delete();

        $message = 'Logistic Zones Deleted Successfully';

        return response()->json(['message' => $message, 'status' => true], 200);
    }
}
