<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldRateToReasuradur extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reasuradur_rate', function (Blueprint $table) {
            $table->string('nama',100)->nullable();
            $table->decimal('or')->nullable();
            $table->decimal('reas')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reasuradur', function (Blueprint $table) {
            //
        });
    }
}
