<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

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
        Log::info('Cronjob ejecutado exitosamente.');
        $notificationService = app(NotificationService::class);

        $notificationService->create(1, 'Cronjob', 'Cronjob ejecutado exitosamente.');
    }
}
