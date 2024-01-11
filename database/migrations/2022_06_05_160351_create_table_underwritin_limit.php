<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUnderwritinLimit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('underwriting_limit', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('min_amount')->nullable();
            $table->bigInteger('max_amount')->nullable();
            $table->smallInteger('usia')->nullable();
            $table->string('keterangan',10)->nullable();
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
        Schema::dropIfExists('underwriting_limit');
    }
}
