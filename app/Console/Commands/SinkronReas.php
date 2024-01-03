<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reas;
use App\Models\ReasuradurRate;

class SinkronReas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sinkron:reas';

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
        foreach(Reas::get() as $item){
            echo "No Pengajuan : {$item->no_pengajuan}\n";
            // rate
            $rate = ReasuradurRate::find($item->reasuradur_rate_id);
            if($rate){
                echo "OR : {$rate->or}\n";
                echo "Reas : {$rate->reas}\n";
                $item->or = $rate->or;
                $item->reas = $rate->reas;
                $item->save();
            }
            echo "--------------------------------------\n";
        }
    }
}
