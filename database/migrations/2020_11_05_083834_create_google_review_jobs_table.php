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
            $table->unsignedInteger('job_id')->comment('DataShake Job Id'); // 0 ~ 2^32
            $table->string('google_place_id', 255)->comment('Google 地點 Id');
            $table->unsignedInteger('review_count')->nullable()->comment('Google 評論總數量');
            $table->float('average_rating')->nullable()->comment('Google 評論總評分');
            $table->string('crawl_status', 20)->comment('DataShake 爬取狀態(complete, maintenance, pending)');
            $table->integer('credits_used')->nullable()->comment('DataShake 此次爬取消耗的點數');
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
