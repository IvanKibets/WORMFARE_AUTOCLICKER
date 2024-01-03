<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableReas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reas', function (Blueprint $table) {
            $table->id();
            $table->string('no_pengajuan',255)->nullable();
            $table->boolean('status')->default(0)->nullable();
            $table->string('cn_number')->nullable();
            $table->integer('reasuradur_id')->nullable();
            $table->integer('reasuradur_rate_id')->nullable();
            $table->decimal('or')->nullable();
            $table->decimal('reas')->nullable();
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
        Schema::dropIfExists('reas');
    }
}