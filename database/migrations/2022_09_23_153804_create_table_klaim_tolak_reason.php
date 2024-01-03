<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableKlaimTolakReason extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('klaim_tolak_reason', function (Blueprint $table) {
            $table->id();
            $table->string('title',200)->nullable();
            $table->timestamps();
        });

        Schema::create('klaim_tolak_reason_item', function (Blueprint $table) {
            $table->id();
            $table->integer('table_klaim_tolak_reason_id')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('table_klaim_tolak_reason');
    }
}
