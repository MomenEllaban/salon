<?php

namespace Modules\Product\Http\Controllers\Backend\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Transformers\ProductCategoryResource;
use Modules\Product\Transformers\ProductResource;

class DashboardController extends Controller
{
    public function productDashboard(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $category = ProductCategory::with('media')
            ->where('status', 1)
            ->paginate($perPage)
            ->forPage(1, 6);

        $productQuery = Product::where('status', 1)->with('media', 'categories', 'brand', 'unit', 'product_variations');

        $featuredProduct = $productQuery->where('is_featured', 1)->inRandomOrder()->paginate($perPage)->forPage(1, 6);

        $bestsellerProduct = $productQuery->orderBy('total_sale_count', 'desc')->paginate($perPage)->forPage(1, 6);

        $discountProduct = $productQuery->where('discount_type', 'percent')->orderBy('discount_value', 'desc')->paginate($perPage)->forPage(1, 6);

        $responseData = [

            'category' => ProductCategoryResource::collection($category)->toArray(request()),
            'featured_product' => ProductResource::collection($featuredProduct)->toArray(request()),
            'bestseller_product' => ProductResource::collection($bestsellerProduct)->toArray(request()),
            'discount_product' => ProductResource::collection($discountProduct)->toArray(request()),

        ];

        return response()->json([
            'status' => true,
            'data' => $responseData,
            'message' => __('messages.dashboard_detail'),
        ], 200);
    }

    public function searchList(Request $request)
    {
        $query = $request->input('query');
        $results = [];

        // Search in Branches
        $product = Product::where('name', 'like', "%{$query}%")->get();
        $results['branches'] = $branches;

        return response()->json($results);
    }
}
