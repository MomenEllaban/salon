<?php

namespace Modules\LikeModule\Http\Controllers\Backend\API;

use Auth;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\LikeModule\Models\Likes;
use Modules\Product\Models\Review;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function ReviewLike(Request $request)
    {
        $review_id = $request->review_id;
        $user_id = $request->input('user_id') ?? Auth::id();

        $review = Review::where('id', $review_id)->with('likes')->first();

        if (! $review) {
            $message = __('product.review_not_found');

            return response()->json(['message' => $message, 'status' => true], 200);
        }

        $userLike = $review->likes->where('user_id', $user_id)->first();

        if ($userLike) {
            $userLike->update([
                'is_like' => $request->input('is_like', 0),
                'dislike_like' => $request->input('dislike_like', 0),
            ]);
        } else {
            $like = new Likes();
            $like->user_id = $user_id;
            $like->is_like = $request->input('is_like', 0);
            $like->dislike_like = $request->input('dislike_like', 0);

            $review->likes()->save($like);
        }

        $likeCount = $review->likes->where('is_like', 1)->count();
        $dislikeCount = $review->likes->where('dislike_like', 1)->count();

        $message = __('product.review_like_update');

        return response()->json([
            'message' => $message,
            'status' => true,
            'like_count' => $likeCount,
            'dislike_count' => $dislikeCount,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create()
    {
        return view('likemodule::create');
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
        return view('likemodule::show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('likemodule::edit');
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
        //
    }
}
