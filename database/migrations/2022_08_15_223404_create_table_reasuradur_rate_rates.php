<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableReasuradurRateRates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reasuradur_rate_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('rate')->nullable();
            $table->smallInteger('tahun')->nullable();
            $table->smallInteger('bulan')->nullable();
            $table->integer('reasuradur_rate_id')->nullable();
            $table->timestamps();
        });

        Schema::create('reasuradur_rate_uw', function (Blueprint $table) {
            $table->id();
            $table->integer('reasuradur_rate_id')->nullable();
            $table->bigInteger('min_amount')->nullable();
            $table->bigInteger('max_amount')->nullable();
            $table->smallInteger('usia')->nullable();
            $table->text('keterangan')->nullable();
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
        Schema::dropIfExists('reasuradur_rate_rates');
    }
}
