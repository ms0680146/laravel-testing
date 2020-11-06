<?php

namespace Tests\Unit;

use App\GoogleReview;
use App\GoogleReviewJob;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GoogleReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_reviews_belongs_to_google_review_job()
    {
        // Arrange(create 1 google_review_job & 3 google_reviews)
        factory(GoogleReviewJob::class)->create();

        // Act
        $googleReview = GoogleReview::all()->random(1)->first();

        // Assert
        // Method 1: Test by count that a GoogleReview has a parent relationship with GoogleReviewJob
        $this->assertEquals(1, $googleReview->googleReviewJob()->count());
        // Method 2: GoogleReview has a parent GoogleReviewJob and is a GoogleReviewJob instance.
        $this->assertInstanceOf(GoogleReviewJob::class, $googleReview->googleReviewJob);
    }
}
