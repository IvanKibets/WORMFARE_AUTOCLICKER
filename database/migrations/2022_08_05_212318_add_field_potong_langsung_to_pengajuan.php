<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldPotongLangsungToPengajuan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->integer('potong_langsung')->nullable();
            $table->string('potong_langsung_persen',12)->nullable();
            $table->integer('pph')->nullable();
            $table->string('pph_persen',12)->nullable();
            $table->integer('ppn')->nullable();
            $table->string('ppn_persen',12)->nullable();
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
