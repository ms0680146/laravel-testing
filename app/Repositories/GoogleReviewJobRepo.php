<?php

namespace App\Repositories;

use App\GoogleReviewJob;
use App\Repositories\BaseRepo;

class GoogleReviewJobRepo extends BaseRepo
{
    public function __construct(GoogleReviewJob $model)
    {
        parent::__construct($model);
    }

    public function findByGooglePlaceId($googlePalceId)
    {
        return $this->model->where('google_place_id', $googlePalceId)->first();
    }
}