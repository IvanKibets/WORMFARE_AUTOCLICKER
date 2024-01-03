<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableReasuransi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reasuransi', function (Blueprint $table) {
            $table->id();
            $table->integer('pengajuan_id')->nullable();
            $table->integer('peserta')->nullable();
            $table->integer('polis_id')->nullable();
            $table->bigInteger('total_nilai_manfaat')->nullable();
            $table->bigInteger('nilai_manfaat_or')->nullable();
            $table->bigInteger('nilai_manfaat_reas')->nullable();
            $table->bigInteger('kontribusi')->nullable();
            $table->bigInteger('kontribusi_gross')->nullable();
            $table->bigInteger('ujroh')->nullable();
            $table->bigInteger('em')->nullable();
            $table->bigInteger('kontribusi_netto')->nullable();
            $table->string('kirim_reas',100)->nullable();
            $table->string('broker',100)->nullable();
            $table->string('line_business')->nullable();
            $table->string('cn_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reasuransi');
    }
}
