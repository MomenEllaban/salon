<?php

namespace Modules\Promotion\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Modules\Promotion\Models\Coupon;
use Modules\Promotion\Models\Promotion;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Modules\Promotion\Http\Requests\PromotionRequest;

class PromotionsController extends Controller
{
    // use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'promotion.coupon_title';
        // module name
        $this->module_name = 'promotions';

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
     * @return Response
     */
    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];
        $today = Carbon::now()->toDateString();

        $module_action = 'List';
        $columns = CustomFieldGroup::columnJsonValues(new Promotion());
        $customefield = CustomField::exportCustomFields(new Promotion());

        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => ' Name',
            ],
            [
                'value' => 'value',
                'text' => ' value',
            ],
            [
                'value' => 'promo_end_date',
                'text' => ' end date',
            ],

        ];

        $export_url = route('backend.promotions.export');

        return view('promotion::backend.promotions.index_datatable', compact('module_action', 'filter', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url'));
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

        $query_data = Promotion::where('name', 'LIKE', "%$term%")->orWhere('slug', 'LIKE', "%$term%")->limit(7)->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'text' => $row->name . ' (Slug: ' . $row->slug . ')',
            ];
        }

        return response()->json($data);
    }

    public function index_data(Request $request)
    {
        $module_name = $this->module_name;
        $query = Promotion::query()->with(['coupon']);

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }

        return Datatables::of($query)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" onclick="dataTableRowCheck(' . $data->id . ')">';
            })
            ->addColumn('action', function ($data) {
                return view('promotion::backend.promotions.action_column', compact('data'));
            })
            ->editColumn('description', function ($data) {
                $maxLength = 50; // Set your desired maximum length
                return Str::limit($data->description, $maxLength);
            })
            ->orderColumn('description', function ($query, $order) {
                $query->orderBy('description', $order);
            })
            ->addColumn('is_expired', function ($data) {
                return $data->coupon->every(fn($coupon) => $coupon->is_expired) ? 'Yes' : 'No';

            })
            ->editColumn('coupon_type', function ($data) {
                return $data->coupon->first()->coupon_type;
            })
            ->filterColumn('coupon_type', function ($query, $keyword) {
                $query->whereHas('coupon', function ($query) use ($keyword) {
                    $query->where('coupon_type', 'like', "%{$keyword}%");
                });
            })
            ->orderColumn('coupon_type', function ($query, $order) {
                $query->select('promotions.*') // Select columns from promotions table
                    ->leftJoin('promotions_coupon as coupon', 'promotions.id', '=', 'coupon.promotion_id') // Join with promotions_coupon table
                    ->orderBy('coupon.coupon_type', $order); // Order by coupon_type
            }, 2)
            ->editColumn('coupon_price', function ($data) {

                if ($data->coupon->first()->discount_type === 'fixed') {
                    $value = \Currency::format($data->coupon->first()->discount_amount ?? 0);
                    return $value;
                }
                if ($data->coupon->first()->discount_type === 'percent') {
                    $value = $data->coupon->first()->discount_percentage . '%';

                    return $value;
                }
            })
            ->orderColumn('coupon_price', function ($query, $order) {
                $query->select('promotions.*')
                    ->leftJoin('promotions_coupon as coupon', 'promotions.id', '=', 'coupon.promotion_id')
                    ->orderByRaw("CASE
                        WHEN coupon.discount_type = 'fixed' THEN coupon.discount_amount
                        WHEN coupon.discount_type = 'percent' THEN coupon.discount_percentage
                        END $order"); // Conditional ordering based on discount_type
            }, 3)
            ->editColumn('use_limit', function ($data) {
                return $data->coupon->first()->use_limit;
            })
            ->orderColumn('use_limit', function ($query, $order) {
                $query->select('promotions.*')
                    ->leftJoin('promotions_coupon as coupon', 'promotions.id', '=', 'coupon.promotion_id')
                    ->orderBy('coupon.use_limit', $order); // Order by use_limit
            }, 4)
            ->editColumn('status', function ($data) {
                // return $data->getStatusLabelAttribute();
                $checked = '';
                if ($data->status) {
                    $checked = 'checked="checked"';
                }

                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.promotions.update_status', $data->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $data->id . '"  name="status" value="' . $data->id . '" ' . $checked . '>
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
            ->rawColumns(['action', 'status', 'check'])
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
                $promotion = Promotion::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_promotion_update');
                break;

            case 'delete':
                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }

                Promotion::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_promotion_update');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.bulk_update')]);
    }

    public function update_status(Request $request, $id)
    {
        $promotion = Promotion::find($id); // Using find() to directly get the model instance
        if ($promotion) {
            $promotion->update(['status' => $request->status]);
        }
        if ($request->status == 1) {
            $coupon = Coupon::where('promotion_id', $id)->first();
            if ($coupon) {
                $coupon->update(['is_expired' => 0]);
            }
        }

        return response()->json(['status' => true, 'message' => 'Status Updated']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(PromotionRequest $request)
    {

        $data = $request->except('feature_image');
        $data = $request->all();

        $promotion = Promotion::create($request->all());

        $couponData = $data;
        $couponData['promotion_id'] = $promotion->id;

        if ($request->coupon_type == 'custom') {
            $couponData['coupon_type'] = $request->coupon_type;
            $couponData['coupon_code'] = $request->coupon_code;
            $this->createCoupon($couponData);
        } else {
            for ($i = 1; $i <= $request->number_of_coupon ?? 1; $i++) {
                $couponData['coupon_type'] = $request->coupon_type;
                $couponData['coupon_code'] = strtoupper(randomString(8));
                $this->createCoupon($couponData);
            }
        }
        storeMediaFile($promotion, $request->file('feature_image'));
        $message = 'New Promotion Added';

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    protected function createCoupon($data)
    {
        return Coupon::create($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Promotion::with('coupon')->findOrFail($id);
        $data['feature_image'] = $data->feature_image;
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
        // dd($request->all());
        $query = Promotion::findOrFail($id);
        $data = $request->except('feature_image');
        $query->update($data);

        if ($request->hasFile('feature_image')) {
            storeMediaFile($query, $request->file('feature_image'));
        }
        if ($request->feature_image == null) {
            $query->clearMediaCollection('feature_image');
        }
        $coupon = Coupon::where('promotion_id', $id)->first();

        $couponData = [
            'discount_type' => $request->discount_type,
        ];

        if ($coupon->used_by == null) {
            $couponData = [
                'start_date_time' => $request->start_date_time,
                'end_date_time' => $request->end_date_time,
                'use_limit' => $request->use_limit,
                'discount_type' => $request->discount_type,
            ];
        }

        if ($request->discount_type == 'percent') {
            $couponData['discount_amount'] = 0;
            $couponData['discount_percentage'] = $request->discount_percentage;
        } else {
            $couponData['discount_amount'] = $request->discount_amount;
            $couponData['discount_percentage'] = 0;
        }
        if ($request->status == 1) {
            $couponData['is_expired'] = 0;
        }
        Coupon::where('promotion_id', $id)->update($couponData);
        $message = 'Promotions Updated Successfully';

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
        $data = Promotion::findOrFail($id);

        $coupon = Coupon::where('promotion_id', $id);
        $coupon->delete();

        $data->delete();

        $message = 'Promotions Deleted Successfully';

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function couponValidate(Request $request)
    {
        $now = now();
        $coupon = Coupon::where('coupon_code', $request->coupon_code)
            ->where('end_date_time', '>=', $now)
            ->where('is_expired', '!=', '1')
            ->whereHas('promotion', function ($query) {
                $query->where('status', '!=', '0');
            })
            ->first();

        if (!$coupon) {

            $message = 'coupon not valid';

            return ['valid' => false, 'message' => $message, 'status' => false];
        }

        $servicePrice = $request->service_price;

        $discountValue = 0;
        if ($coupon->discount_type == 'fixed') {
            $discountValue = $coupon->discount_amount;
        } elseif ($coupon->discount_type == 'percentage') {
            $discountValue = ($coupon->discount_percentage / 100) * $servicePrice;
        }

        // Ensure discount doesn't exceed the service price
        if ($discountValue > $servicePrice) {
            return response()->json(['valid' => false, 'message' => 'Discount exceeds the service price', 'status' => false], 400);
        }
        $data = [
            'coupon_code' => $coupon->coupon_code,
            'discount_type' => $coupon->discount_type,
            'discount_value' => $coupon->discount_amount,
            'discount_percentage' => $coupon->discount_percentage,
        ];
        $message = 'coupon valid';

        return response()->json(['message' => $message, 'data' => $data, 'status' => true, 'valid' => true], 200);
    }

    public function couponsview(Request $request)
    {
        $promotion_id = $request->id ? $request->id : abort(404);
        $promotion = Promotion::find($promotion_id);

        // if(!isset($promotion)) {
        //     abort(404);
        // }
        $module_action = 'List';
        $columns = CustomFieldGroup::columnJsonValues(new Promotion());
        $customefield = CustomField::exportCustomFields(new Promotion());

        $export_import = true;

        $export_columns = [
            [
                'value' => 'coupon_code' . ',' . $promotion_id,
                'text' => 'Coupon code',
            ],
            [
                'value' => 'value' . ',' . $promotion_id,
                'text' => 'Value',
            ],
            [
                'value' => 'use_limit' . ',' . $promotion_id,
                'text' => 'Use limit',
            ],
            [
                'value' => 'used_by' . ',' . $promotion_id,
                'text' => 'User',
            ],
            [
                'value' => 'is_expired' . ',' . $promotion_id,
                'text' => 'Expired',
            ],
        ];

        $export_url = route('backend.coupons.export', $promotion_id);

        return view('promotion::backend.promotions.coupon_datatable', compact('module_action', 'export_import', 'export_columns', 'export_url', 'promotion_id', 'promotion'));
    }

    public function coupon_data(Request $request, $id)
    {
        $module_name = $this->module_name;

        // $query = Coupon::with('userCouponRedeem')->where('promotion_id', $id);
        $query = Coupon::with('userRedeems')->where('promotion_id', $id);


        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }

        return Datatables::of($query)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" onclick="dataTableRowCheck(' . $data->id . ')">';
            })
            ->addColumn('action', function ($data) {
                return view('promotion::backend.promotions.action_column', compact('data'));
            })
            ->editColumn('value', function ($data) {

                if ($data->discount_type === 'fixed') {
                    $value = \Currency::format($data->discount_amount ?? 0);
                    return $value;
                }
                if ($data->discount_type === 'percent') {
                    $value = $data->discount_percentage . '%';

                    return $value;
                }
            })
            ->editColumn('used_by', function ($data) {
                $userNames = $data->userRedeems->pluck('full_name');
                $displayedNames = $userNames->take(2)->implode(', ');
                if ($userNames->count() > 2) {
                    $displayedNames .= ', ...';
                }

                return $displayedNames ?: " ";
            })
            ->editColumn('is_expired', function ($data) {

                return $data->is_expired === 1 ? 'Yes' : 'No';
            })
            ->rawColumns(['action', 'status', 'check'])
            ->orderColumns(['id'], '-:column $1')
            ->make(true);
    }

    public function couponExport(Request $request, $id)
    {
        $this->exportClass = '\App\Exports\couponsExport';

        return $this->export($request);
    }
    public function unique_coupon(Request $request)
    {
        $couponCode = implode('', $request->all());
        $isUnique = !Coupon::where('coupon_code', $couponCode)->exists();

        return response()->json(['isUnique' => $isUnique]);
        ;
    }
}
