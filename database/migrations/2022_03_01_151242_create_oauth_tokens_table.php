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
        //Schema::enableForeignKeyConstraints();//Added
        Schema::create('oauth_tokens', function (Blueprint $table) {
            $table->id();
            //$table->unsignedBigInteger('UserId');
            $table->unsignedBigInteger('app_user_id')->unique();//Seller User ID
            $table->unsignedBigInteger('app_owner_id')->unique();//Admin User ID
            $table->string('oauth_token')->nullable();
            $table->string('oauth_secret')->nullable();
            $table->string('oauth_verifier')->nullable();
            $table->timestamps();

            $table->foreign('app_user_id')->references('id')->on('app_users')->onDelete('cascade');
            $table->foreign('app_owner_id')->references('id')->on('users')->onDelete('cascade');
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
