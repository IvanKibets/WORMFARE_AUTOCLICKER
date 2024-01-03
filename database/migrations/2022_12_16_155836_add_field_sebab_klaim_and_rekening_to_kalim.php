<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldSebabKlaimAndRekeningToKalim extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('klaim', function (Blueprint $table) {
            $table->string('sebab',200)->nullable();
            $table->string('bank_no_rekening',100)->nullable();
            $table->string('bank_cabang',200)->nullable();
            $table->string('bank_atas_nama',200)->nullable();
            $table->string('bank_mata_uang',25)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kalim', function (Blueprint $table) {
            //
        });
    }
}
