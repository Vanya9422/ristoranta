<?php

namespace App\Console\Commands;

use App\Telegram\TelegramBot;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use Vinkla\Hashids\Facades\Hashids;

class CheckHashid extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'hash:check {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check id to encrypt';

    /**
     * Execute the console command.
     * @throws BindingResolutionException
     */
    public function handle()
    {
        $this->info(Hashids::encode($this->argument('id')));
    }
}
