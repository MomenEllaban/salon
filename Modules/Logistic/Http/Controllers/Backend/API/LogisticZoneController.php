<?php

namespace Modules\Logistic\Http\Controllers\Backend\API;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Logistic\Models\LogisticZone;
use Modules\Logistic\Transformers\LogisticZoneResource;

class LogisticZoneController extends Controller
{
    public function logisticzoneList(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query_data = LogisticZone::with('cities');

        if ($request->has('address_id') && $request->address_id != '') {
            $user_address = Address::where('id', $request->address_id)->first();

            if ($user_address) {
                $query_data->whereHas('cities', function ($query) use ($user_address) {
                    $query->where('city_id', $user_address->city);
                });
            } else {
                return response()->json([
                    'status' => true,
                    'message' => __('product.user_address_not_found'),
                ], 200);
            }
        }

        $logisticZone = $query_data->paginate($perPage);

        $logisticZoneCollection = LogisticZoneResource::collection($logisticZone);

        return response()->json([
            'status' => true,
            'data' => $logisticZoneCollection,
            'message' => __('product.logistic_zone_list'),
        ], 200);
    }
}
