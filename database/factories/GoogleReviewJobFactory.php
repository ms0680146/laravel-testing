<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\GoogleReview;
use App\GoogleReviewJob;
use Faker\Generator as Faker;

$factory->define(GoogleReviewJob::class, function (Faker $faker) {
    return [
        'job_id' => $faker->randomDigit,
        'google_place_id' => $faker->uuid,
        'review_count' => $faker->numberBetween($min = 20, $max = 100), 
        'average_rating' => $faker->randomElement(array(1,2,3,4,5)),
        'crawl_status' => $faker->randomElement(array('complete', 'maintenance', 'pending')),
        'credits_used' => $faker->randomElement(array(1,2,3,4,5)),
    ];
});

// Factory States
$factory->state(GoogleReviewJob::class, 'complete', function (Faker $faker) {
    return [
        'crawl_status' => 'complete'
    ];
});
$factory->state(GoogleReviewJob::class, 'maintenance', function (Faker $faker) {
    return [
        'crawl_status' => 'maintenance'
    ];
});
$factory->state(GoogleReviewJob::class, 'pending', function (Faker $faker) {
    return [
        'crawl_status' => 'pending'
    ];
});

// Factory Callbacks
$factory->afterCreating(GoogleReviewJob::class, function (GoogleReviewJob $googleReviewJob, Faker $faker) {
    $googleReviewJob->reviews()->saveMany(
        factory(GoogleReview::class, 3)->make([
            'google_review_job_id' => $googleReviewJob['id'],
        ])
    );
});
