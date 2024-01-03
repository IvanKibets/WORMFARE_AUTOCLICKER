<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldModelReasToReasuradurRate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reasuradur_rate', function (Blueprint $table) {
            $table->string('model_reas',100)->nullable();
            $table->string('max_or',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reasuradur_rate', function (Blueprint $table) {
            //
        });
    }
}
