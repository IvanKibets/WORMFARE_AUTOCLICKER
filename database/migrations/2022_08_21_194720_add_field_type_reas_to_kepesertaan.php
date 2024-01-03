<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldTypeReasToKepesertaan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kepesertaan', function (Blueprint $table) {
            $table->string('reas_manfaat',50)->nullable();
            $table->string('reas_type',50)->nullable();
            $table->decimal('nilai_manfaat_asuransi_reas')->nullable()->change();
            $table->decimal('ujroh_reas')->nullable()->change();
            $table->decimal('total_kontribusi_reas')->nullable()->change();
            $table->decimal('reas_manfaat_asuransi_ajri')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kepesertaan', function (Blueprint $table) {
            //
        });
    }
}
