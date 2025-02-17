<?php

namespace Modules\NotificationTemplate\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Constant\Models\Constant;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Modules\NotificationTemplate\Models\NotificationTemplate;
use Yajra\DataTables\DataTables;
use Modules\NotificationTemplate\Models\NotificationTemplateContentMapping;

class NotificationTemplatesController extends Controller
{
    // use Authorizable;

    public function __construct()
    {
        $this->global_booking = false;
        // Page Title
        $this->module_title = 'notification.title_template';

        // module name
        $this->module_name = 'notificationtemplates';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => 'fa-regular fa-sun',
            'module_name' => $this->module_name,
            'global_booking' => $this->global_booking,
        ]);
        $this->middleware(['permission:view_notification_template'])->only('index');
        $this->middleware(['permission:edit_notification_template'])->only('edit', 'update');
        $this->middleware(['permission:add_notification_template'])->only('store');
        $this->middleware(['permission:delete_notification_template'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $module_action = 'List';

        $filter = [
            'status' => request()->status,
        ];

        $columns = CustomFieldGroup::columnJsonValues(new NotificationTemplate());

        return view('notificationtemplate::backend.notificationtemplates.index_datatable', compact('module_action', 'filter', 'columns'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
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
                $branches = NotificationTemplate::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_notification_update');
                break;

            case 'delete':
                NotificationTemplate::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_notification_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.bulk_update')]);
    }

    public function index_list(Request $request)
    {
        $query_data = NotificationTemplate::with('defaultNotificationTemplateMap', 'constant')->get();

        $data = [];

        $notificationKeyChannels = array_keys(config('notificationtemplate.channels'));

        $arr = [];
        // For Channel Map Or Update Channel Value
        foreach ($notificationKeyChannels as $key => $value) {
            $arr[$value] = 0;
        }

        foreach ($query_data as $key => $value) {
            $data[$key] = [
                'id' => $value->id,
                'type' => $value->type,
                'template' => $value->defaultNotificationTemplateMap->subject,
                'is_default' => false,
            ];

            if (isset($value->channels)) {
                $data[$key]['channels'] = $value->channels;
            } else {
                $data[$key]['channels'] = $arr;
            }
        }

        $notificationChannels = config('notificationtemplate.channels');

        return response()->json(['data' => $data, 'channels' => $notificationChannels, 'status' => true, 'message' => __('messages.notification_temp_list')]);
    }

    public function update_status(Request $request, NotificationTemplate $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('branch.status_update')]);
    }

    public function index_data(Datatables $datatable)
    {
        $query = NotificationTemplate::query()->with('defaultNotificationTemplateMap');

        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->addColumn('action', function ($data) {
                return view('notificationtemplate::backend.notificationtemplates.action_column', compact('data'));
            })
            ->editColumn('label', function ($row) {
                return '<a href="' . route('backend.notification-templates.edit', $row->id) . '">' . optional($row->defaultNotificationTemplateMap)->subject . '</a>';
            })
            ->editColumn('status', function ($row) {
                $checked = '';
                if ($row->status) {
                    $checked = 'checked="checked"';
                }

                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.notificationtemplates.update_status', $row->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $row->id . '"  name="status" value="' . $row->id . '" ' . $checked . '>
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
        $customFieldColumns = CustomField::customFieldData($datatable, NotificationTemplate::CUSTOM_FIELD_MODEL, null);

        return $datatable->rawColumns(array_merge(['label', 'action', 'status', 'check'], $customFieldColumns))
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $module_action = 'messages.create';

        $assets = ['textarea'];

        return view('notificationtemplate::backend.notificationtemplates.create', compact('module_action', 'assets'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $map = $request->defaultNotificationTemplateMap;
        $request->merge(['type' => $request->type]);

        $map['subject'] = $request->defaultNotificationTemplateMap['subject'];
        $map['notification_message'] = $request->defaultNotificationTemplateMap['notification_message'];
        $map['notification_link'] = $request->defaultNotificationTemplateMap['notification_link'];

        $request['to'] = isset($request->to) ? json_encode($request->to) : null;
        $request['bcc'] = isset($request->bcc) ? json_encode($request->bcc) : null;
        $request['cc'] = isset($request->cc) ? json_encode($request->cc) : null;

        $data = NotificationTemplate::create($request->all());
        $data->defaultNotificationTemplateMap()->create($map);

        $message = (__('messages.msg_added', ['name' => __('messages.mailable')]));

        return redirect()->route('backend.notification-templates.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $module_name = $this->module_name;

        $module_name_singular = Str::singular($module_name);

        $module_action = 'Show';

        $data = NotificationTemplate::findOrFail($id);

        return view('notificationtemplate::backend.notificationtemplates.show', compact('module_name_singular', 'module_action', 'data'));
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
        $data = NotificationTemplate::with('defaultNotificationTemplateMap', 'constant')->findOrFail($id);
        $buttonTypes = Constant::where('type', 'notification_param_button')
            ->where(function ($query) use ($data) {
                $query->where('sub_type', $data->type)->orWhere('sub_type', null);
            })->get();

        $assets = ['textarea'];

        return view('notificationtemplate::backend.notificationtemplates.edit', compact('module_action', 'data', 'assets', 'buttonTypes'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $userType = $request->defaultNotificationTemplateMap['user_type'];
        $type = $request->type;


        if (!$request->has('to') || is_null($request->to)) {
            return back()->with('error', __('messages.to_field_required'));
        }

        $ids = NotificationTemplate::where('type', $type)->pluck('id');

        $check = NotificationTemplateContentMapping::with('template')
            ->whereIn('template_id', $ids)
            ->where('user_type', $userType)
            ->first();

        if ($check !== null) {
            $data = NotificationTemplateContentMapping::find($check->id);
            if ($data !== null) {
                $map = $request->defaultNotificationTemplateMap;
                $data->update([
                    'subject' => $map['subject'] ?? '',
                    'template_detail' => $map['template_detail'] ?? '',
                    'notification_subject' => $map['notification_subject'] ?? '',
                    'notification_template_detail' => $map['notification_template_detail'] ?? '',
                ]);

                $data->template->update([
                    'to' => isset($request->to) ? json_encode($request->to) : null,
                    'bcc' => isset($request->bcc) ? json_encode($request->bcc) : null,
                    'cc' => isset($request->cc) ? json_encode($request->cc) : null,
                    'status' => $request->status,
                ]);

                $message = __('messages.notification_template_updated');
            } else {
                $message = __('messages.notification_template_not_found');
            }
        } else {
            $data = NotificationTemplate::updateOrCreate(['type' => $type], [
                'name' => $type,
                'description' => $request->description,
                'to' => isset($request->to) ? json_encode($request->to) : null,
                'bcc' => isset($request->bcc) ? json_encode($request->bcc) : null,
                'cc' => isset($request->cc) ? json_encode($request->cc) : null,
                'status'=>$request->status,
            ]);

            $data->defaultNotificationTemplateMap()->create(
                array_merge(
                    $request->defaultNotificationTemplateMap,
                    [
                        'notification_subject' => $request->defaultNotificationTemplateMap['notification_subject'] ?? '',
                        'notification_template_detail' => $request->defaultNotificationTemplateMap['notification_template_detail'] ?? ''
                    ]
                )
            );

            $message = __('messages.notification_template_created');
        }

        return back()->with('success', $message);
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
        $data = NotificationTemplate::findOrFail($id);
        $data->delete();

        $message = __('messages.notification_deleted');

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * List of trashed ertries
     * works if the softdelete is enabled.
     *
     * @return Response
     */
    public function trashed()
    {
        $module_name_singular = Str::singular($this->module_name);

        $module_action = 'Trash List';

        $data = NotificationTemplate::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate();

        return view('notificationtemplate::backend.notificationtemplates.trash', compact('data', 'module_name_singular', 'module_action'));
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
        $module_name = $this->module_name;
        $module_name_singular = Str::singular($module_name);
        $$module_name_singular = NotificationTemplate::withTrashed()->find($id);
        $$module_name_singular->restore();

        flash('<i class="fas fa-check"></i> ' . label_case($module_name_singular) . ' Data Restoreded Successfully!')->success()->important();

        return redirect("app/$module_name");
    }

    public function getAjaxList(Request $request)
    {
        $items = [];
        $value = $request->q;
        switch ($request->type) {
            case 'constants':
                $items = Constant::select(\DB::raw('id,name text'))
                    ->where(function ($query) use ($value) {
                        $query->where(\DB::raw('value', 'LIKE', '%' . $value . '%'));
                        $query->orWhere('value', 'LIKE', '%' . $value . '%');
                    })
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->orderBy('sequence', 'ASC')
                    ->where('type', $request->data_type);
                $items = $items->get();
                break;
            case 'constants_key':
                $items = DB::table('constants')->select(DB::raw('value id, name text'))
                    ->where(function ($query) use ($value) {
                        $query->where(DB::raw('value', 'LIKE', '%' . $value . '%'));
                        $query->orWhere('value', 'LIKE', '%' . $value . '%');
                    })
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->orderBy('sequence', 'ASC')
                    ->where('type', $request->data_type);
                $items = $items->get();
                break;
                break;
            default:
                break;
        }

        return response()->json(['status' => 'true', 'results' => $items]);
    }

    public function notificationButton(Request $request)
    {
        $buttonTypes = Constant::where('type', 'notification_param_button')
            ->where(function ($query) use ($request) {
                $query->where('sub_type', $request->type)->orWhere('sub_type', null);
            })->get();

        return view('notificationtemplate::backend.notificationtemplates.perameters-buttons', compact('buttonTypes'));
    }

    public function notificationTemplate(Request $request)
    {
        $detail = NotificationTemplateContentMapping::where(['template_id' => $request->template_id, 'mailable_id' => $request->mailable_id, 'language' => $request->language])->first();
        if (!isset($type)) {
            $detail = NotificationTemplate::find($request->template_id);
        }

        return response()->json(['data' => $detail, 'status' => true]);
    }

    public function updateChanels(Request $request)
    {
        $data = $request->except('selected_session_branch_id');
        foreach ($data as $key => $value) {
            if (isset($value['id'])) {
                $notificationTemplate = NotificationTemplate::find($value['id']);

                $notificationTemplate->channels = $value['channels'] ?? '';

                $notificationTemplate->save();
            }
        }

        $message = __('messages.bulk_notification_setting_update');

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function fetchNotificationData(Request $request)
    {
        $userType = $request->input('user_type');
        $type = $request->input('type');

        $ids = NotificationTemplate::where('type', $type)->pluck('id');

        $data = NotificationTemplateContentMapping::with('template')->whereIn('template_id', $ids)
            ->where('user_type', $userType)
            ->first();

        if ($data) {
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            return response()->json(['success' => false, 'message' => 'No data found for the selected user_type.']);
        }
    }

}
