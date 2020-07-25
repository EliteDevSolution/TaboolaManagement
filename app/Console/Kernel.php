<?php

namespace DLW\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\AutoInit::class,
        Commands\AutoUpdate::class
    ];

    /**
     * Define the application's command schedule.
     * php artisan schedule:run >> /dev/null 2>&1
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    
    protected function schedule(Schedule $schedule)
    {
        //$start_date = date('Y-m-d', strtotime("-1 days"));
        //var_dump($start_date);
        //exit;
        //$schedule->command('cronjob:autoinit')->everyMinute()->between('3:05', '24:00');;
        $start_date = date('Y-m-d H:i:s');
        echo $start_date."\n";
        
        $schedule->command('cronjob:autoinit')
                           ->dailyAt('0:05');
        
        $schedule->command('cronjob:autoupdate')
          ->everyFiveMinutes()
          ->between('0:15', '9:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
