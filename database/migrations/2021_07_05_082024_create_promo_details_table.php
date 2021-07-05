<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_details', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('promo_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedInteger('type');
            $table->unsignedBigInteger('quantity');
            $table->boolean('free_of_charge')->default(false);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promo_details');
    }
}
