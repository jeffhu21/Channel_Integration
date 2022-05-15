<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifyFailedDespatchedOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('notify_failed_despatched_orders');

        Schema::enableForeignKeyConstraints();
        Schema::create('notify_failed_despatched_orders', function (Blueprint $table) {
            
            
            $table->unsignedBigInteger('app_user_id');
            $table->string('ReferenceNumber')->unique()->primary();
            $table->string('ShippingVendor')->nullable();
            $table->string('ShippingMethod')->nullable();
            $table->string('TrackingNumber')->nullable();
            $table->string('SecondaryTrackingNumbers')->nullable();
            $table->string('ProcessedOn')->nullable();
            
            $table->timestamps();

            $table->foreign('app_user_id')->references('id')->on('app_users')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notify_failed_despatched_orders');
    }
}
