<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleReviewJob extends Model
{
    protected $fillable = [
        'job_id', 'google_place_id', 'http_status_code',
        'review_count', 'average_rating', 'last_crawl', 'crawl_status',
        'percentage_complete', 'result_count', 'credits_used'
    ];

    public function reviews()
    {
        return $this->hasMany(GoogleReview::class, 'google_review_job_id', 'id');
    }
}
