<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleReview extends Model
{
    protected $fillable = [
        'google_review_job_id', 'review_id', 'review_name', 
        'review_date', 'rating_value', 'review_text',
        'review_url', 'profile_picture', 'location', 
        'review_title', 'verified_order', 'language_code',
        'reviewer_title', 'meta_data'
    ];

    public function googleReviewJob()
    {
        return $this->belongsTo(GoogleReviewJob::class, 'google_review_job_id', 'id');
    }
}
