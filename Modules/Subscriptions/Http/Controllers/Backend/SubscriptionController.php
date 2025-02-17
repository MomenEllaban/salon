<?php

namespace Modules\Subscriptions\Http\Controllers\Backend;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Routing\Controller;
use Modules\Subscriptions\Models\Subscription;
use Yajra\DataTables\DataTables;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        // Page Title
        $this->module_title = 'Subscriptions';

        // module name
        $this->module_name = 'subscriptions';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index()
    {
        $module_action = 'User List';

        return view('subscriptions::backend.subscriptions.index', compact('module_action'));
    }

    public function index_data(Datatables $datatable)
    {
        $query = Subscription::query()->with('user')->where('status', 'active');

        return $datatable->eloquent($query)
                    // ->addColumn('action', function ($data) {
                    //     return view('plan::backend.plan.action_column', compact('data'));
                    // })
            ->editColumn('user_id', function ($data) {
                return isset($data->user->name) ? $data->user->name : '-';
            })
            ->editColumn('status', function ($data) {
                if ($data->status === 'active') {
                    $data['status'] = 1;
                }

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
            ->toJson();
    }
}
