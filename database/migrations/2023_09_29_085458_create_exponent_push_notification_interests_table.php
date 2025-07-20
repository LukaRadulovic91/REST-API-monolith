<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('exponent_push_notification_interests', static function (Blueprint $table) {
            $table->id();

            $table->string('key')->index();
            $table->string('value');

            $table->unique(['key','value']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('exponent_push_notification_interests');
    }
};
