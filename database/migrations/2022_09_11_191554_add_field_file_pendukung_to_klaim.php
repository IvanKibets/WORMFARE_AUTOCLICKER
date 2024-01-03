<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldFilePendukungToKlaim extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('klaim', function (Blueprint $table) {
            $table->text('formulir_pengajuan_klaim')->nullable();
            $table->text('surat_keterangan_meninggal_kelurahan')->nullable();
            $table->text('surat_keterangan_meninggal_rs')->nullable();
            $table->text('copy_ktp')->nullable();
            $table->text('copy_ktp_ahli_waris')->nullable();
            $table->text('resume_medis')->nullable();
            $table->text('daftar_angsuran')->nullable();
            $table->text('copy_akad_pembiayaan')->nullable();
            $table->text('surat_kuasa')->nullable();
            $table->text('surat_keterangan_ahli_waris')->nullable();
            $table->text('surat_dari_pemegang_polis')->nullable();
            $table->text('dokumen_lain')->nullable();
            $table->text('dokumen_lain_keterangan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('klaim', function (Blueprint $table) {
            //
        });
    }
}
