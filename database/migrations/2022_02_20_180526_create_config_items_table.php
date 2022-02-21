<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_items', function (Blueprint $table) {
            $table->id();
            $table->string('ConfigItemId');
            $table->string('Description');
            $table->string('GroupName');
            $table->boolean('MustBeSpecified')->default(true);
            $table->string('Name');
            $table->boolean('ReadOnly')->default(false);
            $table->string('SelectedValue');
            $table->integer('Sortorder');
            $table->enum('ValueType',['STRING','INT','DOUBLE','BOOLEAN','PASSWORD','LIST']);
            $table->timestamps();
            //$table->foreign('id')->references('id')->on('user_configs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('config_items');
    }
}
