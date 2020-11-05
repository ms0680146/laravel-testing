<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\GoogleReview;
use App\GoogleReviewJob;
use Faker\Generator as Faker;

$factory->define(GoogleReview::class, function (Faker $faker) {
    return [
        'google_review_job_id' => factory(GoogleReviewJob::class),
        'review_id' => $faker->randomDigit,
        'review_name' => $faker->name, 
        'review_date' => $faker->date('Y-m-d'),
        'rating_value' => $faker->randomElement(array(1,2,3,4,5)),
        'review_text' => $faker->text,
    ];
});
