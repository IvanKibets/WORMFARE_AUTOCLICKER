<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKadaluarsaReasToKepesertaan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kepesertaan', function (Blueprint $table) {
            $table->integer('kadaluarsa_reas_hari')->nullable();
            $table->integer('kadaluarsa_reas_tanggal')->nullable();
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
        Schema::table('kepesertaan', function (Blueprint $table) {
            //
        });
    }
}
