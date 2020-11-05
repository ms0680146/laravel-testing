<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleReview extends Model
{
    protected $fillable = [
        'google_review_job_id', 'review_id', 'review_name', 
        'review_date', 'rating_value', 'review_text'
    ];

    public function googleReviewJob()
    {
        return $this->belongsTo(GoogleReviewJob::class, 'google_review_job_id', 'id');
    }
}
