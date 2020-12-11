<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GoogleReview extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'google_review_job_id' => $this->google_review_job_id,
            'review_id' => $this->review_id,
            'review_name' => $this->review_name,
            'review_date' => $this->review_date,
            'rating_value' => $this->rating_value,
            'review_text' => $this->review_text,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
