<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFieldTypeToKepesertaan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kepesertaan', function (Blueprint $table) {
            $table->decimal('nilai_manfaat_asuransi_reas',19,2)->change();
            $table->decimal('reas_manfaat_asuransi_ajri',19,2)->change();
            $table->decimal('total_kontribusi_reas',19,2)->change();
            $table->decimal('ujroh_reas',19,2)->change();
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
