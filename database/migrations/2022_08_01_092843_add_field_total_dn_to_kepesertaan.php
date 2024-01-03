<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldTotalDnToKepesertaan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kepesertaan', function (Blueprint $table) {
            $table->smallInteger('total_dn')->nullable();
            $table->string('no_reg',200)->nullable();
            $table->string('no_debit_note',200)->nullable();
            $table->string('tahun_produksi',4)->nullable();
            $table->string('produksi_akrual',20)->nullable();
            $table->date('issued_accepted_date')->nullable();
            $table->string('status_endorse')->nullable();
            $table->bigInteger('refund')->nullable();
            $table->text('refund_keterangan')->nullable();
            $table->date('refund_date_efektif')->nullable();
            $table->string('no_cn',200)->nullable();
            $table->date('pay_date')->nullable();
            $table->string('produksi_cash_basis')->nullable();
            $table->bigInteger('kontribusi_netto_biaya_penutupan')->nullable();
            $table->string('perkalian_biaya_penutupan',100)->nullable();
            $table->string('bp',5)->nullable();
            $table->bigInteger('total_biaya_penutupan')->nullable();
            $table->string('line_of_business',25)->nullable();
            $table->string('ket_polis',100)->nullable();
            $table->string('channel',100)->nullable();
            $table->string('distribution_channel_to_aaji',20)->nullable();
            $table->string('tipe_reas',20)->nullable();
            $table->string('model_reas',50)->nullable();
            $table->string('reasuradur',30)->nullable();
            $table->string('rate_reas',20)->nullable();
            $table->string('ri_com',20)->nullable();
            $table->bigInteger('nilai_manfaat_asuransi_reas')->nullable();
            $table->bigInteger('total_kontribusi_reas')->nullable();
            $table->bigInteger('ujroh_reas')->nullable();
            $table->bigInteger('net_kontribusi_reas')->nullable();
            $table->bigInteger('prod_reas')->nullable();
            $table->bigInteger('prod_akrual_reas')->nullable();
            $table->string('no_cn_reas',150)->nullable();
            $table->string('no_kode',100)->nullable();
            $table->date('pay_date_reas')->nullable();
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
