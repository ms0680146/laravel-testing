<?php

namespace Tests\Unit;

use App\GoogleReview;
use App\GoogleReviewJob;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GoogleReviewJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_review_job_has_many_google_reviews()
    {
        // Arrange(create 1 google_review_job & 3 google_reviews)
        factory(GoogleReviewJob::class)->create();

        // Act
        $googleReviewJob = GoogleReviewJob::first();
        $googleReviews = GoogleReview::all();

        // Assert
        // Method 1: Count that a googleReviewJob googleReviews collection exists.
        $this->assertEquals(3, $googleReviewJob->reviews->count());
        // Method 2: googleReviews are related to googleReviewJob and is a collection instance.
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $googleReviewJob->reviews);
    }
}
