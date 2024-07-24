<?php

namespace App\Observers;

use App\Models\Income;
use App\Notifications\IncomeCreated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class IncomeObserver
{
    /**
     * Handle the Income "created" event.
     */
    public function created(Income $income): void
    {
        try {
            if (!empty($income->email)) {
                if (filter_var($income->email, FILTER_VALIDATE_EMAIL)) {
                    Notification::route('mail', $income->email)
                        ->notify(new IncomeCreated($income));
                }
            }
        } catch (\Exception $e) {
            Log::error('Error al enviar la notificación de ingreso creada: ' . $e->getMessage());
            Log::error($e);
        }
    }

    /**
     * Handle the Income "updated" event.
     */
    public function updated(Income $income): void
    {
        //
    }

    /**
     * Handle the Income "deleted" event.
     */
    public function deleted(Income $income): void
    {
        //
    }

    /**
     * Handle the Income "restored" event.
     */
    public function restored(Income $income): void
    {
        //
    }

    /**
     * Handle the Income "force deleted" event.
     */
    public function forceDeleted(Income $income): void
    {
        //
    }
}
