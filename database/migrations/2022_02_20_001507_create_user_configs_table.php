<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_configs', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->string('email');
            $table->string('account_name');
            $table->string('authorization_token');
            $table->boolean('is_complete')->default(false);
            $table->string('step_name');
            $table->boolean('is_config_active')->default(false);
            $table->string('api_key')->nullable();
            $table->string('api_secret_key')->nullable();
            $table->boolean('is_oauth')->default(false);
            $table->boolean('is_price_inc_tax')->default(false);
            $table->boolean('download_virtual_items')->default(false);
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
        Schema::dropIfExists('user_configs');
    }
}
