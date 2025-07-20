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
        Schema::create('clients', static function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('software_id')->nullable();
            $table->string('title');
            $table->string('company_name')->nullable();
            $table->string('dentist_name')->nullable();
            $table->string('website')->nullable();
            $table->string('office_address')->nullable();
            $table->string('office_number')->nullable();

            $table->integer('recall_time')->nullable();
            $table->integer('type_of_procedure')->nullable();
            $table->integer('vaccination_info')->nullable();
            $table->integer('payment_for_candidates')->nullable();
            $table->integer('provide_masks')->nullable();

            $table->boolean('provide_gowns')->nullable();
            $table->boolean('provide_shields')->nullable();
            $table->boolean('digital_x_ray')->nullable();
            $table->boolean('ultrasonic_cavitron')->nullable();
            $table->boolean('free_parking')->nullable();
            $table->boolean('tax_deducation')->nullable();
            $table->boolean('sin_info')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('software_id')->references('id')->on('softwares')
                ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
