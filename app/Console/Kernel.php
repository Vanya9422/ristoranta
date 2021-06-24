<?php

namespace App\Console;

use AdamTyn\Lumen\Artisan\StorageLinkCommand;
use App\Console\Commands\CheckHashid;
use App\Console\Commands\TelegramWebhook;
use Illuminate\Console\KeyGenerateCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        StorageLinkCommand::class,
        KeyGenerateCommand::class,
        TelegramWebhook::class,
        CheckHashid::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

    }
}
