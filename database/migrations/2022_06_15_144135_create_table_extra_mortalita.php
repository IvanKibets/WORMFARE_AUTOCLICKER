<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableExtraMortalita extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extra_mortalita_rate', function (Blueprint $table) {
            $table->id();
            $table->integer('extra_mortalita_id')->nullable();
            $table->string('bulan',10)->nullable();
            $table->string('tahun',10)->nullable();
            $table->string('rate',10)->nullable();
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
        Schema::dropIfExists('extra_mortalita');
    }
}
