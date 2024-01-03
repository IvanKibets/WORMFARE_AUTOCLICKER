<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKadaluarsaToReas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reas', function (Blueprint $table) {
            $table->integer('kadaluarsa_reas_hari')->nullable();
            $table->integer('kadaluarsa_reas_tanggal')->nullable();
        });

        Schema::table('klaim', function (Blueprint $table) {
            $table->integer('kadaluarsa_klaim_hari')->nullable();
            $table->integer('kadaluarsa_klaim_tanggal')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reas', function (Blueprint $table) {
            //
        });
    }
}
