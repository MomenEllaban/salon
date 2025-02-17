<?php

namespace Modules\Product\Http\Controllers\Backend;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Product\Models\Review;
use Modules\Product\Models\ReviewGallery;
use Yajra\DataTables\DataTables;

class ReviewController extends Controller
{
    public function __construct()
    {
        // Page Title
        $this->module_title = 'product.reviews';
        // module name
        $this->module_name = 'reviews';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    public function index(Request $request)
    {
        $module_action = 'List';

        return view('product::backend.review.index_datatable', compact('module_action'));
    }

    public function index_data(Request $request)
    {
        $query = Review::query();

        $filter = $request->filter;

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$data->id.'"  name="datatable_ids[]" value="'.$data->id.'" onclick="dataTableRowCheck('.$data->id.')">';
            })
            ->addColumn('action', function ($data) {
                return view('product::backend.review.action_column', compact('data'));
            })
            ->addColumn('feature_image', function ($data) {
                return "<img src='".optional($data->product)->feature_image."' class='avatar avatar-50 rounded-pill'>";
            })
            ->editColumn('name', function ($data) {
                return optional($data->product)->name;
            })
            ->filterColumn('name', function ($query, $keyword) {
                if (! empty($keyword)) {
                    $query->whereHas('product', function ($q) use ($keyword) {
                        $q->where('name', 'like', '%'.$keyword.'%');
                    });
                }
            })
            ->orderColumn('name', function ($query, $order) {
                $query->select('product_review.*')
                    ->leftJoin('products', 'product_review.product_id', '=', 'products.id')
                    ->orderBy('products.name', $order);
            }, 1)
            ->editColumn('user_name', function ($data) {
                return optional($data->user)->full_name;
            })
            ->filterColumn('user_name', function ($query, $keyword) {
                if (! empty($keyword)) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where('first_name', 'like', '%'.$keyword.'%');
                        $q->orWhere('last_name', 'like', '%'.$keyword.'%');
                    });
                }
            })
            ->orderColumn('user_name', function ($query, $order) {
                $query->select('product_review.*')
                    ->leftJoin('users', 'product_review.user_id', '=', 'users.id')
                    ->orderBy('users.first_name', $order)
                    ->orderBy('users.last_name', $order);
            }, 1)
            ->editColumn('updated_at', function ($data) {
                $module_name = $this->module_name;

                $diff = Carbon::now()->diffInHours($data->updated_at);

                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
            ->rawColumns(['action', 'check', 'feature_image'])
            ->orderColumns(['id'], '-:column $1')
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create()
    {
        return view('product::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('product::show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('product::edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if (env('IS_DEMO')) {
            return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
        }
        $review_id = $id;

        $review = Review::where('id', $review_id)->first();

        if (! $review) {
            $message = __('product.review_not_found');

            return response()->json(['message' => $message, 'status' => true], 200);
        }

        $images = ReviewGallery::where('review_id', $review_id)->get();

        foreach ($images as $key => $value) {
            $value->clearMediaCollection('gallery_images');
            $value->delete();
        }

        ReviewGallery::where('review_id', $review_id)->delete();

        $review->likes()->delete();
        $review->delete();

        $message = __('product.review_removed');

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');
        // dd($actionType, $ids, $request->status);
        switch ($actionType) {
            case 'change-status':
                $customer = Review::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_customer_update');
                break;

            case 'delete':
                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                Review::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_customer_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.bulk_update')]);
    }
}
