<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleReviewJob extends Model
{
    protected $fillable = [
        'job_id', 'google_place_id', 'review_count', 
        'average_rating', 'crawl_status', 'credits_used'
    ];

    public function reviews()
    {
        return $this->hasMany(GoogleReview::class, 'google_review_job_id', 'id');
    }
}
