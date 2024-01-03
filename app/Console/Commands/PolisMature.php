<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kepesertaan;

class PolisMature extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'polis:mature';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Data Polis yang sudah Mature atau tanggal berakhir sudah melewati hari ini';

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
        // $polis =  Kepesertaan::where('status_polis','Mature')->whereRaw('tanggal_akhir > curdate()')->get();
        $polis =  Kepesertaan::where('status_polis','Inforce')->get();
        foreach($polis as $num=> $item){
            if($item->tanggal_akhir < date('Y-m-d')){
                $find = Kepesertaan::find($item->id);
                $find->status_polis = 'Mature';
                $find->save();

                echo "{$num}. Peserta : {$item->no_peserta} \ {$item->nama} \n";
            }
        }

        return 0;
    }
}
