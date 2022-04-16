<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
        Schema::create('app_users', function (Blueprint $table) {
            $table->id();
            $table->uuid('UserId');
            $table->string('Email');
            $table->string('AccountName');
            $table->string('AuthorizationToken');
            $table->boolean('IsComplete')->default(false);
            $table->string('StepName');
            $table->boolean('IsConfigActive')->default(false);
            $table->string('ApiKey')->nullable();
            $table->string('ApiSecretKey')->nullable();
            $table->boolean('IsOauth')->default(false);
            $table->boolean('IsPriceIncTax')->default(false);
            $table->boolean('DownloadVirtualItems')->default(false);
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
        Schema::dropIfExists('app_users');
    }
}
