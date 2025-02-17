<?php

namespace Modules\Product\Http\Controllers\Backend\API;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Product\Models\Product;
use Modules\Product\Models\Review;
use Modules\Product\Models\ReviewGallery;
use Modules\Product\Transformers\ReviewResource;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();

        $data['user_id'] = $request->input('user_id') ?? Auth::id();

        $product = Product::where('id', $data['product_id'])->first();

        if (! $product) {
            $message = __('product.product_not_found');

            return response()->json(['message' => $message, 'status' => true], 200);
        }

        $review = Review::Create($data);

        $gallery = collect($request->gallery);

        foreach ($gallery as $key => $file) {
            if ($file->isValid()) {
                $reviewGallery = ReviewGallery::create([
                    'review_id' => $review->id,
                ]);

                $media = $reviewGallery->addMedia($file)->toMediaCollection('gallery_images');

                $reviewGallery->full_url = $media->getUrl();
                $reviewGallery->save();
            }
        }

        $message = __('product.review_added');

        return response()->json(['message' => $message, 'review' => $review, 'status' => true], 200);
    }

    public function getReviewList(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Get the number of items per page from the request (default: 10)

        $review = Review::with('gallery');

        if ($request->has('product_id') && $request->product_id != '') {
            $review = $review->where('product_id', $request->product_id);
        }

        $review = $review->paginate($perPage);

        $reviewCollection = ReviewResource::collection($review);

        return response()->json([
            'status' => true,
            'data' => $reviewCollection,
            'message' => __('product.review_list'),
        ], 200);
    }

    public function UpdateReview(Request $request)
    {
        $review_id = $request->review_id;

        $data['user_id'] = $request->input('user_id') ?? Auth::id();

        $review = Review::where('id', $review_id)->first();

        $data = $request->except('gallery');

        if (! $review) {
            $message = __('product.review_not_found');

            return response()->json(['message' => $message, 'status' => true], 200);
        }

        $gallery = collect($request->gallery, true);

        $images = ReviewGallery::where('review_id', $review_id)->with('media')->get();

        foreach ($images as $key => $value) {
            $value->clearMediaCollection('gallery_images');
            $value->delete();
        }

        foreach ($gallery as $key => $file) {
            if ($file->isValid()) {
                $reviewGallery = ReviewGallery::create([
                    'review_id' => $review->id,
                ]);

                $media = $reviewGallery->addMedia($file)->toMediaCollection('gallery_images');

                $reviewGallery->full_url = $media->getUrl();
                $reviewGallery->save();
            }
        }

        $review->update($data);

        $message = __('product.review_update');

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function removeReview(Request $request)
    {
        $review_id = $request->review_id;

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
}
