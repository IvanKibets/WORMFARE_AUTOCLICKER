<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableKepesertaan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kepesertaan', function (Blueprint $table) {
            $table->id();
            $table->integer('polis_id')->nullable();
            $table->string('nama',255)->nullable();
            $table->string('no_ktp',100)->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telepon',35)->nullable();
            $table->string('pekerjaan',100)->nullable();
            $table->string('bank',255)->nullable();
            $table->string('cab',255)->nullable();
            $table->string('no_closing',200)->nullable();
            $table->string('no_akad_kredit',200)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin',25)->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_akhir')->nullable();
            $table->bigInteger('basic')->nullable();
            $table->bigInteger('sub_basic1')->nullable();
            $table->bigInteger('sub_basic2')->nullable();
            $table->bigInteger('rider1')->nullable();
            $table->bigInteger('rider2')->nullable();
            $table->bigInteger('rider3')->nullable();
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
        Schema::dropIfExists('kepesertaan');
    }
}
