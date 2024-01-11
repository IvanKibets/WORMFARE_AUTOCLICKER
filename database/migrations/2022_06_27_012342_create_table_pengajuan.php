<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePengajuan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id();
            $table->integer('polis_id')->nullable();
            $table->string('no_pengajuan',200)->nullable();
            $table->boolean('status')->default(0)->nullable();
            $table->tinyInteger('total_akseptasi')->nullable();
            $table->tinyInteger('total_approve')->nullable();
            $table->tinyInteger('total_reject')->nullable();
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
        Schema::dropIfExists('pengajuan');
    }
}
