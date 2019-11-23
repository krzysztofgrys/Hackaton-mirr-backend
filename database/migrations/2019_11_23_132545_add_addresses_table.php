<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('city');
            $table->string('zip_code');
            $table->string('street');
            $table->string('house_number');
            $table->point('coordinates');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('addresses_id');
            $table->index('addresses_id');
            $table->foreign('addresses_id')->references('id')->on('addresses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_addresses_id_foreign');
            $table->dropColumn('addresses_id');
        });
        Schema::drop('addresses');
    }
}
