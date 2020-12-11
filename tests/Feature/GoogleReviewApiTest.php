<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\GoogleReview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class GoogleReviewApiTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    public function test_google_reviews_api()
    {
        // Arrange(create 1 google_review_job & 1 google_reviews)
        factory(GoogleReview::class)->create();
        // Act
        $response = $this->json('GET', 'api/google_reviews');
        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'review_name',
                    'review_date',
                    'rating_value',
                    'review_text'
                ]
            ]
        ]);
    }

    public function test_google_review_api()
    {
        // Arrange(create 1 google_review_job & 1 google_reviews)
        factory(GoogleReview::class)->create();
        // Act
        $response = $this->json('GET', 'api/google_reviews/1');
        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'review_name',
                'review_date',
                'rating_value',
                'review_text'
            ]
        ]);
    }
}
