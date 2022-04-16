<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();//Added
        Schema::create('app_keys', function (Blueprint $table) {
            //$table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('discogs_consumer_key')->nullable();
            $table->string('discogs_consumer_secret')->nullable();
            $table->string('linnworks_application_id')->nullable();
            $table->string('linnworks_application_secret')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('callback_url')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_keys');
    }
}
