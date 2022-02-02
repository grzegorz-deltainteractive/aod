<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSuppliersContacts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers_contacts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('supplier_id')->unsigned()->default(0);
            $table->integer('department_id')->unsigned()->default(0);
            $table->string('email',200)->nullable();
            $table->string('phone', 200)->nullable();
            $table->string('name', 200)->nullable();
            $table->string('stanowisko', 200)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suppliers_contacts');
    }
}
