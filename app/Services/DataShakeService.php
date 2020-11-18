<?php

namespace App\Services;

use GuzzleHttp\Client;

class DataShakeService
{
    private const GET = 'GET';
    private const POST = 'POST';
    private $client;
    private $headers;

    public function __construct()
    {
        $this->client = new Client();
        $this->headers = ['spiderman-token' => config('googleReview.token')];
    }

    public function callAddReviewProfile($params)
    {
        $params['headers'] = $this->headers;
        /**
         * This avoids you having to poll our system, and you get the result as soon as it's complete.
         * Ref: https://help.datashake.com/article/207-what-is-the-callback-functionality
         */
        $params['callback'] = config('googleReview.callback');

        $response = $this->client
            ->request(self::GET, config('googleReview.add_review_profile_url'), $params)
            ->getBody();
        
        return json_decode($response, true);
    }
}