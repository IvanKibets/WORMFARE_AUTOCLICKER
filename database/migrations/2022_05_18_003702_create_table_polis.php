<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePolis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polis', function (Blueprint $table) {
            $table->id();
            $table->string('no_polis',100)->nullable();
            $table->string('no_polis_sistem',100)->nullable();
            $table->string('nama',100)->nullable();
            $table->text('alamat')->nullable();
            $table->integer('provinsi_id')->nullable();
            $table->date('terbit_polis')->nullable();
            $table->string('tahun_terbit_polis',5)->nullable();
            $table->integer('produk_id')->nullable();
            $table->date('awal')->nullable();
            $table->date('akhir')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status',20)->nullable();
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
        Schema::dropIfExists('polis');
    }
}
