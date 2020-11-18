<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\GoogleReviewJob;
use App\Services\DataShakeService;
use App\Services\GoogleReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GoogleReviewServiceTest extends TestCase
{
    use RefreshDatabase;

    private $dataShakeServiceMock = null;
    private $googleReviewService = null;

    public function setUp() : void
    {
        parent::setUp();
        $this->dataShakeServiceMock = $this->initMock(DataShakeService::class);
        $this->googleReviewService = $this->app->make(GoogleReviewService::class);
    }

    public function test_add_review_profile()
    {
        // Arrange (25sprout google place id: ChIJg8wV-cmrQjQR7o27A1TgiBs)
        $googlePalceId = 'ChIJg8wV-cmrQjQR7o27A1TgiBs';
        $params = [
            'place_id' => $googlePalceId,
        ];

        $dataShakeResponse = [
            'success' => true,
            'job_id' => 10000,
            'status' => 200,
            'message' => 'add review into queue...'
        ];

        // Act
        $this->dataShakeServiceMock
            ->shouldReceive('callAddReviewProfile')
            ->once()
            ->with($params)
            ->andReturn($dataShakeResponse);

        $result = $this->googleReviewService->addReviewProfile($googlePalceId);

        // Assert
        $this->assertTrue($result);
        $googleReviewJob = GoogleReviewJob::where('job_id', $dataShakeResponse['job_id'])->first();
        $this->assertNotNull($googleReviewJob);
    }
}
