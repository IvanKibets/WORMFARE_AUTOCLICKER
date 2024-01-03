<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldJumlahPesertaToReas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reas', function (Blueprint $table) {
            $table->integer('jumlah_peserta')->nullable();
            $table->decimal('manfaat_asuransi_reas',19,2)->nullable();
            $table->decimal('manfaat_asuransi_ajri',19,2)->nullable();
            $table->decimal('kontribusi',19,2)->nullable();
            $table->decimal('ujroh',19,2)->nullable();
            $table->decimal('kontribusi_netto',19,2)->nullable();
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
