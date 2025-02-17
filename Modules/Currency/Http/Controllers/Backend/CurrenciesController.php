<?php

namespace Modules\Currency\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Currency\Models\Currency;

class CurrenciesController extends Controller
{
    use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'Currencies';

        // module name
        $this->module_name = 'currencies';

        // directory path of the module
        $this->module_path = 'currency::backend';

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

        return view('currency::backend.currencies.index_datatable', compact('module_action'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        $term = trim($request->q);

        $query_data = Currency::where(function ($q) {
            if (! empty($term)) {
                $q->orWhere('currency_name', 'LIKE', "%$term%");
            }
        })->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'currency_name' => $row->currency_name,
                'currency_symbol' => $row->currency_symbol,
                'currency_code' => $row->currency_code,
            ];
        }

        return response()->json($data);
    }

    public function index_data()
    {
        $data = Currency::get();

        return response()->json(['data' => $data, 'status' => true, 'message' => __('messages.currency_list')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $module_action = 'Create';

        return view('currency::backend.currencies.create', compact('module_action'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        if ($request->is_primary) {
            Currency::where('is_primary', '=', 1)->update(['is_primary' => 0]);
        }
        $data = Currency::create($request->all());

        $message = __('messages.currency_created');

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

        $data = Currency::findOrFail($id);

        return view('currency::backend.currencies.show', compact('module_action', "$data"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Currency::findOrFail($id);

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        if ($request->is_primary) {
            Currency::where('is_primary', '=', 1)->update(['is_primary' => 0]);
        }
        $data = Currency::findOrFail($id);
        $data->update($request->all());

        $message = __('messages.currency_updated');

        if (request()->wantsJson()) {
            return response()->json(['message' => $message, 'status' => true], 200);
        } else {
            flash("<i class='fas fa-check'></i> $message")->success()->important();

            return redirect()->route('backend.currencies.show', $data->id);
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
        $data = Currency::findOrFail($id);

        $data->delete();

        $message = __('messages.currency_deleted');

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

        $data = Currency::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate();

        return view('currency::backend.currencies.trash', compact("$data", 'module_name_singular', 'module_action'));
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

        $data = Currency::withTrashed()->find($id);
        $data->restore();

        $message = __('messages.currency_data');

        return response()->json(['message' => $message, 'status' => true], 200);
    }
}
