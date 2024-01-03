<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kepesertaan;

class CheckReas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:reas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $peserta = Kepesertaan::whereNull('reas_id')->whereNull('pengajuan_id')->get();
        foreach($peserta as $k => $item){
            if($item->nilai_manfaat_asuransi_reas > 0){
                $item->reas_manfaat_asuransi_ajri = $item->basic - $item->nilai_manfaat_asuransi_reas;
                $item->save(); 
            }
            echo "{$k}. No Peserta : ". $item->no_peserta ."\n";
        }

        return 0;
    }
}
