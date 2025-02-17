<?php

namespace Modules\Promotion\Http\Controllers\Backend\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Promotion\Models\Promotion;
use Modules\Promotion\Models\Coupon;
use Modules\Promotion\Transformers\PromotionResource;
use Carbon\Carbon;

class PromotionsController extends Controller
{
    public function getCouponsList(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $today_date = date('Y-m-d');
        $coupons = Promotion::with('coupon')
            ->where('status', 1)
            ->whereHas('coupon', function ($query) use ($today_date) {
                $query->where('is_expired', 0)
                    ->where('end_date_time', '>=', $today_date)
                    ->where('start_date_time', '<=', $today_date);
            });

        $coupons = $coupons->paginate($perPage);
        $couponsCollection = PromotionResource::collection($coupons);

        return response()->json([
            'status' => true,
            'data' => $couponsCollection,
            'message' => __('promotion.coupons_list'),
        ], 200);
    }

    public function getPromotionAndCouponDetails(Request $request)
    {
        $coupon_code = $request->coupon_code;

        if (!empty($coupon_code)) {
            $coupon = Promotion::with('coupon')
                ->where('status', 1)
                ->whereHas('coupon', function ($query) use ($coupon_code) {
                    $query->where('coupon_code', $coupon_code);
                })->first();

            if ($coupon) {
                return response()->json([
                    'status' => true,
                    'data' => new PromotionResource($coupon),
                    'message' => __('promotion.promotion_and_coupons_details'),
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => __('promotion.coupon_not_found'),
                ], 200);
            }
        }
    }
}
