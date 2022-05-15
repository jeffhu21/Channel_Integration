<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifyFailedDespatchedItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('notify_failed_despatched_items');
        
        Schema::enableForeignKeyConstraints();
        Schema::create('notify_failed_despatched_items', function (Blueprint $table) {
            

            $table->string('ReferenceNumber');
            $table->string('SKU')->nullable();
            $table->string('OrderLineNumber')->nullable();
            $table->integer('DespatchedQuantity')->nullable();

            $table->timestamps();

            $table->foreign('ReferenceNumber')->references('ReferenceNumber')->on('notify_failed_despatched_orders')->onDelete('cascade');
        });
        /*
        Schema::table('notify_failed_despatched_items', function (Blueprint $table)
        {
            $table->dropForeign('notify_failed_despatched_items.notify_failed_despatched_order_id_foreign');
            
            if(Schema::hasColumn('notify_failed_despatched_items','order_id'))
            {
                Schema::table('notify_failed_despatched_items', function (Blueprint $table)
                {
                    
                    $table->dropColumn('order_id');
                });
            }
            
        }
        );
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notify_failed_despatched_items');
    }
}
