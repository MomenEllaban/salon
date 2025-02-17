<?php

namespace Modules\Product\Http\Controllers\Backend\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductGallery;
use Modules\Product\Transformers\ProductDetailResource;
use Modules\Product\Transformers\ProductResource;

class ProductsController extends Controller
{
    public function ProductList(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $productQuery = Product::where('status', 1)->with('media', 'categories', 'brand', 'unit', 'product_variations', 'product_review');

        if ($request->has('category_id') && $request->category_id != '') {
            $category_id = $request->category_id;

            $productQuery->whereHas('categories', function ($query) use ($category_id) {
                $query->where('category_id', $category_id);
            });
        }

        if ($request->has('search') && $request->search != '') {
            $productQuery = $productQuery->where('name', 'like', "%{$request->search}%")->inRandomOrder();
        }

        if ($request->has('is_featured') && $request->is_featured != '') {
            $is_featured = $request->is_featured;

            $productQuery = $productQuery->where('is_featured', 1)->inRandomOrder();
        }

        if ($request->has('best_seller') && $request->best_seller != '') {
            $productQuery = $productQuery->orderBy('total_sale_count', 'desc');
        }

        if ($request->has('best_discount') && $request->best_discount != '') {
            $productQuery = $productQuery->where('discount_type', 'percent')->orderBy('discount_value', 'desc');
        }

        $productQuery = $productQuery->paginate($perPage);

        $productCollection = ProductResource::collection($productQuery);

        return response()->json([
            'status' => true,
            'data' => $productCollection,
            'message' => __('product.product_list'),
        ], 200);
    }

    public function product_detail(Request $request)
    {
        $id = $request->id;

        $productdetails = Product::where('id', $id)->with('media', 'categories', 'brand', 'unit', 'product_variations', 'gallery', 'product_review')->first();

        if ($productdetails == null) {
            $message = __('product.product_not_found');

            return response()->json([
                'status' => false,
                'message' => $message,
            ], 200);
        }

        $productDetailCollection = new ProductDetailResource($productdetails);

        $categoryIds = $productdetails->categories->pluck('id')->toArray();
        $relatedProducts = Product::whereHas('categories', function ($query) use ($categoryIds) {
            $query->whereIn('product_categories.id', $categoryIds);
        })
            ->where('id', '!=', $id)
            ->with('media', 'categories', 'brand', 'unit', 'product_variations')
            ->get();

        $relatedproductCollection = ProductResource::collection($relatedProducts);

        return response()->json([
            'status' => true,
            'data' => $productDetailCollection,
            'related-product' => $relatedproductCollection,
            'message' => __('product.product_detail'),
        ], 200);
    }

    public function ProductGallery(Request $request)
    {
        $productId = $request->input('product_id');

        // Retrieve service-wise gallery
        if ($productId) {
            $product = Product::find($productId);

            if (! $product) {
                return response()->json([
                    'status' => false,
                    'message' => __('product.product_not_found'),
                ], 404);
            }

            $data = ProductGallery::where('product_id', $productId)->get();

            $gallery = ['gallery' => $data, 'product' => $product];

            return response()->json([
                'status' => true,
                'data' => $gallery,
                'message' => __('product.product_gal_retrived'),
            ], 200);
        }

        // Retrieve all gallery
        $allData = ProductGallery::all();

        return response()->json([
            'status' => true,
            'data' => $allData,
            'message' => __('product.product_gallery'),
        ], 200);
    }
}
