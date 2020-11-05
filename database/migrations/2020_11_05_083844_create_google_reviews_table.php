<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('google_review_job_id')->comment('google_review_jobs id');
            $table->foreign('google_review_job_id')->references('id')->on('google_review_jobs')->onDelete('cascade');
            $table->unsignedInteger('review_id')->comment('DataShake Review Id');
            $table->string('review_name', 255)->nullable()->comment('Google 評論者');
            $table->date('review_date')->comment('Google 評論日期');
            $table->float('rating_value')->comment('Google 評論分數');
            $table->text('review_text')->nullable()->comment('Google 評論內容');
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
        Schema::dropIfExists('google_reviews');
    }
}
