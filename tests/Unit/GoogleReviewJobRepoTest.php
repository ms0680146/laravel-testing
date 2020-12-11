<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\GoogleReviewJob;
use App\Repositories\GoogleReviewJobRepo;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GoogleReviewJobRepoTest extends TestCase
{
    use RefreshDatabase;

    protected $googleReviewJobRepo = null;

    public function setUp() : void
    {
        parent::setUp();
        // 建立要測試用的 repository
        $this->googleReviewJobRepo = $this->app->make(GoogleReviewJobRepo::class);
    }

    public function test_find_google_review_job_by_google_place_id()
    {
        // Arrange(create 1 google_review_job & 3 google_reviews)
        $googlePlaceId = 'test123';
        factory(GoogleReviewJob::class)->create([
            'google_place_id' => $googlePlaceId
        ]);

        // Act(find googleReviewJob by googlePlaceId)
        $googleReviewJob = $this->googleReviewJobRepo->findByGooglePlaceId($googlePlaceId);
       
        // Assert(check googleReviewJob is instance of GoogleReviewJob)
        $this->assertInstanceOf(GoogleReviewJob::class, $googleReviewJob);
    }

    public function test_create_google_review_job()
    {
        // Arrange
        $data = [
            'job_id' => 'test_job_id',
            'google_place_id' => 'test_google_place_id', 
            'review_count' => 10, 
            'average_rating' => 3.5, 
            'crawl_status' => 'complete', 
            'credits_used' => 10
        ];

        // Act(find googleReviewJob by id)
        $googleReviewJob = $this->googleReviewJobRepo->create($data);
        
        // Assert
        $this->assertInstanceOf(GoogleReviewJob::class, $googleReviewJob);
        $this->assertDatabaseHas('google_review_jobs', ['job_id' => 'test_job_id']);
    }

    public function test_find_google_review_job_by_id()
    {
        // Arrange(create 1 google_review_job & 3 google_reviews)
        factory(GoogleReviewJob::class)->create();

        // Act(find googleReviewJob by id)
        $googleReviewJob = $this->googleReviewJobRepo->find(1);

        // Assert(check googleReviewJob is instance of GoogleReviewJob)
        $this->assertInstanceOf(GoogleReviewJob::class, $googleReviewJob);
        $this->assertEquals(1, $googleReviewJob->id);
    }

    public function test_update_google_review_job()
    {
        // Arrange
        factory(GoogleReviewJob::class)->create([
            'crawl_status' => 'pending',
        ]);

        // Act(update googleReviewJob crawl_status as complete)
        $status = $this->googleReviewJobRepo->update(1, ['crawl_status' => 'complete']);
        
        // Assert
        $this->assertTrue($status);
        $this->assertDatabaseHas('google_review_jobs', ['crawl_status' => 'complete']);
        $this->assertDatabaseMissing('google_review_jobs', ['crawl_status' => 'pending']);
    }

    public function test_delete_google_review_job()
    {
        // Arrange
        factory(GoogleReviewJob::class)->create([
            'job_id' => 'should_delete'
        ]);

        // Act(delete googleReviewJob)
        $status = $this->googleReviewJobRepo->delete(1);
        
        // Assert
        $this->assertTrue($status);
        $this->assertDatabaseMissing('google_review_jobs', ['job_id' => 'should_delete']);
    }
}
