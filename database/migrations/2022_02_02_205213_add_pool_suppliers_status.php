<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPoolSuppliersStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers_pools_status', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('supplier_id')->unsigned()->default(0);
            $table->integer('pool_id')->unsigned()->default(0);
            $table->integer('user_id')->unsigned()->default(0);
            $table->dateTime('filled_date')->nullable()->default(null);;
            $table->dateTime('accepted_date')->nullable()->default(null);
            $table->integer('accepted_user_id')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suppliers_pools_status');
    }
}
