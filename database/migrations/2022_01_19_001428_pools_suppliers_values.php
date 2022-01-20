<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PoolsSuppliersValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers_pools_questions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('user_id')->default(0)->unsigned();
            $table->integer('pool_id')->nullable()->unsigned()->default(0);
            $table->integer('category_id')->unsigned()->default(0);
            $table->integer('category_param_id')->unsigned()->default(0);
            $table->integer('supplier_id')->unsigned()->default(0);
            $table->integer('value')->default(0)->unsigned();
            $table->text('notices')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suppliers_pools_questions');
    }
}
