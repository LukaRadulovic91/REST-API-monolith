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
        Schema::create('job_ads', static function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('job_ad_status_id');

            $table->integer('job_ad_type');
            $table->integer('title');
            $table->text('job_description')->nullable();
            $table->integer('pay_rate');
            $table->integer('payment_time');
            $table->integer('years_experience')->nullable();

            $table->date('permament_start_date')->nullable();
//            $table->text('candidates_feedback')->nullable();
            $table->text('client_feedback')->nullable();
            $table->integer('client_feedback_stars')->nullable();
            $table->boolean('lunch_break')->default(false);
            $table->integer('lunch_break_duration')->nullable();
            $table->boolean('is_active');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients')
                ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_ads');
    }
};
