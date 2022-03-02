<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOauthTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_tokens', function (Blueprint $table) {
            $table->id();
            //$table->unsignedBigInteger('UserId');
            $table->string('consumer_key')->nullable();
            $table->string('consumer_secret')->nullable();
            $table->string('oauth_token')->nullable();
            $table->string('oauth_secret')->nullable();
            $table->string('oauth_verifier')->nullable();
            $table->timestamps();

            //$table->foreign('UserId')->references('id')->on('user_infos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oauth_tokens');
    }
}
