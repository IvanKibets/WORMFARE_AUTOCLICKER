<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldKeteranganToKepesertaan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kepesertaan', function (Blueprint $table) {
            $table->text('keterangan')->nullable();
            $table->string('pph',15)->nullable();
            $table->string('ppn',15)->nullable();
            $table->string('potongan_langsung',15)->nullable();
            $table->integer('jumlah_potongan_langsung')->nullable();
            $table->integer('kartu_peserta')->nullable();
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
