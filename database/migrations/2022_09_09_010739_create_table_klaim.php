<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableKlaim extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('klaim', function (Blueprint $table) {
            $table->id();
            $table->string('no_pengajuan',200)->nullable();
            $table->integer('polis_id')->nullable();
            $table->integer('kepesertaan_id')->nullable();
            $table->string('no_surat',100)->nullable();
            $table->date('tanggal_meninggal')->nullable();
            $table->bigInteger('nilai_klaim')->nullable();
            $table->string('jenis_klaim',200)->nullable();
            $table->text('tempat_dan_sebab')->nullable();
            $table->date('tanggal_pengajuan')->nullable();
            $table->date('tanggal_dok_lengkap')->nullable();
            $table->date('tanggal_proses')->nullable();
            $table->text('sumber_informasi')->nullable();
            $table->text('sebab_meninggal')->nullable();
            $table->text('riwayat_penyakit')->nullable();
            $table->text('tempat_meninggal')->nullable();
            $table->text('verifikasi_via_telpon')->nullable();
            $table->text('analisa_medis')->nullable();
            $table->text('kesimpulan')->nullable();
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
        Schema::dropIfExists('klaim');
    }
}
