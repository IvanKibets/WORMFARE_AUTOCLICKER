<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldRunningNoSuratToPolis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('polis', function (Blueprint $table) {
            $table->integer('running_no_surat')->default(0)->nullable();
            $table->integer('biaya_polis_materai')->default(0)->nullable();
            $table->integer('biaya_sertifikat')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('polis', function (Blueprint $table) {
            //
        });
    }
}
