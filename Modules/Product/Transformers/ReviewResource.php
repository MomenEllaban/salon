<?php

namespace Modules\Product\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->user->first_name.' '.$this->user->last_name,
            'product_id' => $this->product_id,
            'rating' => $this->rating,
            'review_msg' => $this->review_msg,
            'is_user_like' => $request->has('user_id') ? checkIsLike($this->id, $request->input('user_id')) : 0,
            'is_user_dislike' => $request->has('user_id') ? checkIsdisLike($this->id, $request->input('user_id')) : 0,
            'review_likes' => $this->likes->where('is_like', 1)->count(),
            'review_dislikes' => $this->likes->where('dislike_like', 1)->count(),
            'review_gallary' => $this->gallery,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,

        ];
    }
}
