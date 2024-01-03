<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldNilaiManfaatToPengajuan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->bigInteger('nilai_manfaat')->default(0)->nullable();
            $table->bigInteger('dana_tabbaru')->default(0)->nullable();
            $table->bigInteger('dana_ujrah')->default(0)->nullable();
            $table->bigInteger('kontribusi')->default(0)->nullable();
            $table->bigInteger('extra_kontribusi')->default(0)->nullable();
            $table->bigInteger('extra_mortalita')->default(0)->nullable();
            $table->bigInteger('net_kontribusi')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            //
        });
    }
}
