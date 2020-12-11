<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\GoogleReviewRepo;
use App\Repositories\GoogleReviewJobRepo;
use App\Http\Resources\GoogleReviewCollection;
use App\Http\Resources\GoogleReview as GoogleReviewResource;

class GoogleReviewController extends Controller
{
    public function __construct(GoogleReviewJobRepo $googleReviewJobRepo, GoogleReviewRepo $googleReviewRepo)
    {
        $this->googleReviewJobRepo = $googleReviewJobRepo;
        $this->googleReviewRepo = $googleReviewRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $googleReviews = $this->googleReviewJobRepo->list();
        $data = new GoogleReviewCollection($googleReviews);
        
        return $this->customResponse(200, 'success', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $googleReview = $this->googleReviewRepo->find($id);
        $data = new GoogleReviewResource($googleReview);

        return $this->customResponse(200, 'success', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
