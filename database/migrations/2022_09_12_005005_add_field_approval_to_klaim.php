<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldApprovalToKlaim extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('klaim', function (Blueprint $table) {
            $table->boolean('head_klaim_status')->nullable();
            $table->text('head_klaim_note')->nullable();
            $table->date('head_klaim_date')->nullable();

            $table->boolean('head_teknik_status')->nullable();
            $table->text('head_teknik_note')->nullable();
            $table->date('head_teknik_date')->nullable();

            $table->boolean('head_devisi_status')->nullable();
            $table->text('head_devisi_note')->nullable();
            $table->date('head_devisi_date')->nullable();
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
