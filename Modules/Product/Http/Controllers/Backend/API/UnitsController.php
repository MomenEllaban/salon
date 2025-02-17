<?php

namespace Modules\Product\Http\Controllers\Backend\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Product\Models\Unit;
use Modules\Product\Transformers\UnitResource;

class UnitsController extends Controller
{
    public function product_unit(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Get the number of items per page from the request (default: 10)
        $branchId = $request->input('branch_id');
        $unit = Unit::where('status', 1);
        // ->whereHas('branches', function ($query) use ($branchId) {
        //     $query->where('branch_id', $branchId);
        // });

        $unit = $unit->paginate($perPage);
        // $unit = $unit->paginate($perPage)->appends('branch_id', $branchId);
        $unitCollection = unitResource::collection($unit);

        if ($request->has('unit_id') && $request->unit_id != '') {
            $unit = $unit->where('id', $request->unit_id)->first();

            $unitCollection = new unitResource($unit);
        }

        return response()->json([
            'status' => true,
            'data' => $unitCollection,
            'message' => __('product.unit_list'),
        ], 200);
    }
}
