<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRecoveryClaim extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recovery_claim', function (Blueprint $table) {
            $table->id();
            $table->string('no_pengajuan',200)->nullable();
            $table->integer('klaim_id')->nullable();
            $table->decimal('or_share',19,2)->nullable();
            $table->decimal('reas_share',19,2)->nullable();
            $table->decimal('ri_com',19,2)->nullable();
            $table->bigInteger('nilai_klaim')->nullable();
            $table->timestamps();
        });

        Schema::table('kepesertaan', function (Blueprint $table) {
            $table->integer('recovery_claim_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recovery_claim');
    }
}
