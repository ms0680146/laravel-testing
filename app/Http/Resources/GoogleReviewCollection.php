<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class GoogleReviewCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return [
                'id' => $item['id'],
                'review_name' => $item['review_name'],
                'review_date' => $item['review_date'],
                'rating_value' => $item['rating_value'],
                'review_text' => $item['review_text'],
            ];
        });
    }
}
