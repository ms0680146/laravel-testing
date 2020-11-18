<?php

namespace App\Services;

use App\Services\DataShakeService;
use App\Repositories\GoogleReviewJobRepo;

class GoogleReviewService
{
    private $dataShakeService;
    private $googleReviewJobRepo;

    public function __construct(DataShakeService $dataShakeService, GoogleReviewJobRepo $googleReviewJobRepo) 
    {
        $this->dataShakeService = $dataShakeService;
        $this->googleReviewJobRepo = $googleReviewJobRepo;
    }

    public function addReviewProfile(String $googlePalceId)
    {
        $queryParams = [
            'place_id' => $googlePalceId,
        ];

        $response = $this->dataShakeService->callAddReviewProfile($queryParams);
        if ($response['success'] !== true) {
            return false;
        }

        $googleReviewJob = $this->googleReviewJobRepo->findByGooglePlaceId($googlePalceId);
        if (isset($googleReviewJob)) {
            $job = [
                'job_id' => $response['job_id'],
                'crawl_status' => 'pending'
            ];

            $this->googleReviewJobRepo->update($googleReviewJob->id, $job);
        } else {
            $job = [
                'job_id' => $response['job_id'],
                'google_place_id' => $googlePalceId,
                'crawl_status' => 'pending'
            ];

            $this->googleReviewJobRepo->create($job);
        }

        return true;
    }
}