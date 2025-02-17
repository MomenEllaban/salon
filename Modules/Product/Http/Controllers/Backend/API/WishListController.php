<?php

namespace Modules\Product\Http\Controllers\Backend\API;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Product\Models\Product;
use Modules\Product\Models\WishList;
use Modules\Product\Transformers\WishListResource;

class WishListController extends Controller
{
    public function getWishList(Request $request)
    {
        $user_id = $request->input('user_id') ?? Auth::id();

        $perPage = $request->input('per_page', 10);

        $wishlist = WishList::where('user_id', $user_id)
            ->with('product')
            ->whereHas('product', function ($query) {
                $query->whereNotNull('id');
            });

        $wishlist = $wishlist->paginate($perPage);

        $wishlistCollection = WishListResource::collection($wishlist);

        return response()->json([
            'status' => true,
            'data' => $wishlistCollection,
            'message' => __('product.wishlist_list'),
        ], 200);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $product = Product::where('id', $data['product_id'])->first();

        if (! $product) {
            $message = __('product.product_not_found');

            return response()->json(['message' => $message, 'status' => true], 200);
        }

        $data['user_id'] = $request->input('user_id') ?? Auth::id();

        $wishlist = WishList::Create($data);

        $message = __('product.added_into_wishlist');

        return response()->json(['message' => $message, 'wishlist' => $wishlist, 'status' => true], 200);
    }

    public function removeWishList(Request $request)
    {
        $wishlist_id = $request->wishlist_id;

        $wishlist = WishList::where('id', $wishlist_id)->first();

        $user_id = $request->input('user_id') ?? Auth::id();

        if ($request->has('product_id') && $request->product_id != '') {
            $wishlist = WishList::where('product_id', $request->product_id)->where('user_id', $user_id)->first();
        }

        if (! $wishlist) {
            $message = __('product.wishlist_not_found');

            return response()->json(['message' => $message, 'status' => true], 200);
        }

        $wishlist->delete();

        $message = __('product.product_remove_from_wishlist');

        return response()->json(['message' => $message, 'status' => true], 200);
    }
}
