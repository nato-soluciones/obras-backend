<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Notifications\Notifiable;

use App\Models\User;
use App\Notifications\ExampleNotification;

class ExampleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:example-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is a simple test command to show how to create a custom cron job in Laravel.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::where('email', 'paconicolasmiranda@gmail.com')->first();

        $user->notify(new ExampleNotification());
        \Log::info('Cronjob ejecutado exitosamente.');
    }
}
