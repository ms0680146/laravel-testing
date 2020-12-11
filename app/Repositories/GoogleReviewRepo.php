<?php

namespace App\Repositories;

use App\GoogleReview;
use App\Repositories\BaseRepo;

class GoogleReviewRepo extends BaseRepo
{
    public function __construct(GoogleReview $model)
    {
        parent::__construct($model);
    }
}