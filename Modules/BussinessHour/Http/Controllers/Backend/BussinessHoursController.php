<?php

namespace Modules\BussinessHour\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\BussinessHour\Models\BussinessHour;
use Yajra\DataTables\DataTables;

class BussinessHoursController extends Controller
{
    // use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'BussinessHours';

        // module name
        $this->module_name = 'bussinesshours';

        // directory path of the module
        $this->module_path = 'bussinesshour::backend';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => 'fa-regular fa-sun',
            'module_name' => $this->module_name,
            'module_path' => $this->module_path,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $module_action = 'List';

        return view('bussinesshour::backend.bussinesshours.index_datatable', compact('module_action'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        $branch_id = $request->branch_id;

        $data = BussinessHour::where('branch_id', $branch_id)->get();

        return response()->json(['data' => $data, 'status' => true]);
    }

    public function index_data()
    {
        $query = BussinessHour::query();

        return Datatables::of($query)
            ->addColumn('action', function ($data) {
                return view('bussinesshour::backend.bussinesshours.action_column', compact('data'));
            })
            ->editColumn('status', function ($data) {
                return $data->getStatusLabelAttribute();
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
            ->rawColumns(['action', 'status'])
            ->orderColumns(['id'], '-:column $1')
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $branch_id = $data['branch_id'];

        $weekdays = $data['weekdays'];

        foreach ($weekdays as $key => $value) {
            $value['branch_id'] = $branch_id;
            BussinessHour::updateOrCreate(['branch_id' => $branch_id, 'id' => $value['id'] ?? -1], $value);
        }

        $data = BussinessHour::where('branch_id', $branch_id)->get();

        $message = __('messages.buissnesshour_added');

        return response()->json(['message' => $message, 'data' => $data,  'status' => true], 200);
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

        $data = BussinessHour::findOrFail($id);

        return view('bussinesshour::backend.bussinesshours.show', compact('module_action', "$data"));
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

        $data = BussinessHour::findOrFail($id);

        if (request()->wantsJson()) {
            return response()->json(['data' => $$module_name_singular, 'status' => true]);
        } else {
            return view('bussinesshour::backend.bussinesshours.edit', compact('module_action', "$data"));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = BussinessHour::findOrFail($id);

        $data->update($request->all());

        $message = __('messages.buissnesshour_updated');

        if (request()->wantsJson()) {
            return response()->json(['message' => $message, 'status' => true], 200);
        } else {
            flash("<i class='fas fa-check'></i> $message")->success()->important();

            return redirect()->route('backend.bussinesshours.show', $data->id);
        }
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
        $data = BussinessHour::findOrFail($id);

        $data->delete();

        $message = __('messages.buissnesshour_deleted');

        if (request()->wantsJson()) {
            return response()->json(['message' => $message, 'status' => true], 200);
        } else {
            flash('<i class="fas fa-check"></i> '.label_case($this->module_name).' Deleted Successfully!')->success()->important();

            return redirect("app//notification/$this->module_name");
        }
    }

    /**
     * List of trashed ertries
     * works if the softdelete is enabled.
     *
     * @return Response
     */
    public function trashed()
    {
        $module_name = $this->module_name;

        $module_name_singular = Str::singular($module_name);

        $module_action = 'Trash List';

        $data = BussinessHour::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate();

        return view('bussinesshour::backend.bussinesshours.trash', compact("$data", 'module_name_singular', 'module_action'));
    }

    /**
     * Restore a soft deleted entry.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function restore($id)
    {
        $module_action = 'Restore';

        $data = BussinessHour::withTrashed()->find($id);
        $data->restore();

        $message = __('messages.buissnesshour_data');

        return response()->json(['message' => $message, 'status' => true], 200);
    }
}
