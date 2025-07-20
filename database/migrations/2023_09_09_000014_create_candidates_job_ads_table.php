<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('candidates_job_ads', static function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('candidate_id');
            $table->unsignedBigInteger('job_ad_id');
            $table->unsignedInteger('job_ad_status');
            $table->text('candidates_feedback')->nullable();
            $table->integer('candidate_feedback_stars')->nullable();
            $table->text('reason_of_cancellation')->nullable();

            $table->timestamps();

            $table->foreign('candidate_id')->references('id')->on('candidates')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('job_ad_id')->references('id')->on('job_ads')
                ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates_job_ads');
    }
};
