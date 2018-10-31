<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CloseByUsersCron implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //run cron job
        // $admin = \App\Admin::find(1);
        // $admin->test = "mk";
        // $admin->save();
        // for($i=0; $i<=1000; $i++){
        //     echo $i;
        // }

        $cron = file_get_contents(url('/api/cron/capture-users'));
        // $cron = file_get_contents('http://whaabaam.com/backend/api/cron/capture-users');
        // $admin->test = "mk1";
        // $admin->save();

    }
}
