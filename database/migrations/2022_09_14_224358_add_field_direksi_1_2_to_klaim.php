<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldDireksi12ToKlaim extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('klaim', function (Blueprint $table) {
            $table->text('direksi_1_file')->nullable();
            $table->boolean('direksi_1_status')->default(0)->nullable();
            $table->text('direksi_1_note')->nullable();

            $table->text('direksi_2_file')->nullable();
            $table->boolean('direksi_2_status')->default(0)->nullable();
            $table->text('direksi_2_note')->nullable();
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
