<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleReviewJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_review_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('job_id'); // 0 ~ 2^32
            $table->string('google_place_id', 255);
            $table->unsignedSmallInteger('http_status_code');
            $table->unsignedInteger('review_count');
            $table->float('average_rating');
            $table->date('last_crawl');
            $table->string('crawl_status', 10);
            $table->float('percentage_complete');
            $table->unsignedInteger('result_count');
            $table->integer('credits_used')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('google_review_jobs');
    }
}
