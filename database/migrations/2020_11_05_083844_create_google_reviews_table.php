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
            $table->unsignedInteger('google_review_job_id');
            $table->foreign('google_review_job_id')->references('id')->on('google_review_jobs')->onDelete('cascade');
            $table->unsignedInteger('review_id');
            $table->string('review_name', 255)->nullable();
            $table->date('review_date');
            $table->float('rating_value');
            $table->text('review_text')->nullable();
            $table->string('review_url', 512);
            $table->string('profile_picture', 512)->nullable();
            $table->string('location', 255)->nullable();
            $table->string('review_title', 255)->nullable();
            $table->boolean('verified_order');
            $table->string('language_code', 20)->nullable();
            $table->string('reviewer_title', 255)->nullable();
            $table->text('meta_data')->nullable();
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
