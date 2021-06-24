<?php

namespace App\Console\Commands;

use App\Telegram\TelegramBot;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;

class TelegramWebhook extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'telegram:hook {--e|event=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add or Remove telegram WebHook';

    /**
     * @var TelegramBot
     */
    protected TelegramBot $telegramBot;

    /**
     * TelegramWebhook constructor.
     */
    public function __construct(TelegramBot $telegramBot)
    {
        parent::__construct();
        $this->telegramBot = $telegramBot;
    }

    /**
     * Execute the console command.
     * @throws BindingResolutionException
     */
    public function handle()
    {
        $event = $this->option('event');

        switch ($event){
            case 'add':
                $this->telegramBot->setWebHook();
                break;
            case 'remove':
                $this->telegramBot->removeWebHook();
                break;
        }

        $json_string = json_encode($this->telegramBot->getWebHook(), JSON_PRETTY_PRINT);
        $this->info($json_string);
    }
}
