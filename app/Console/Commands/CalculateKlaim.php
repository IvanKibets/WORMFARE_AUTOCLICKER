<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Klaim;
use App\Models\Kepesertaan;

class CalculateKlaim extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:klaim';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'kalkulasi data klaim';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $data = Klaim::get();
        foreach($data as $k => $item){
            echo "{$k}.{$item->no_pengajuan}\n";
            $item->kadaluarsa_klaim_tanggal = $item->kepesertaan->polis->kadaluarsa_klaim ? date('Y-m-d',strtotime($item->tanggal_meninggal ." +{$item->kepesertaan->polis->kadaluarsa_klaim} days")) : '';
            $kadaluarsa_reas_tanggal = $item->kepesertaan->polis->kadaluarsa_reas ? date('Y-m-d',strtotime($item->tanggal_meninggal ." +{$item->kepesertaan->polis->kadaluarsa_reas} days")) : '';
            $item->save();

            Kepesertaan::find($item->kepesertaan_id)->update(['kadaluarsa_reas_tanggal'=>$kadaluarsa_reas_tanggal]);
        }
    }
}
