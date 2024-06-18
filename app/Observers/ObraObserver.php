<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Obra;

use App\Notifications\ObraCreated;
use App\Notifications\ObraFinalized;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class ObraObserver
{
    /**
     * Handle the Obra "created" event.
     */
    public function created(Obra $obra): void
    {
        try {
            $users = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['OWNER', 'ARCHITECT']);
            })->whereNotNull('email')->get();

            $notification = new ObraCreated($obra);

            foreach ($users as $user) {
                $user->notify($notification);
            }
        } catch (\Exception $e) {
            Log::error('Error al enviar la notificación de obra creada: ' . $e->getMessage());
            // Log::error($e);
        }
    }

    /**
     * Handle the Obra "updated" event.
     */
    public function updated(Obra $obra): void
    {
        if ($obra->status === 'FINALIZED') {
            try {
                $users = User::whereHas('roles', function ($query) {
                    $query->whereIn('name', ['OWNER', 'ARCHITECT']);
                })->whereNotNull('email')->get();
                $notification = new ObraFinalized($obra);

                foreach ($users as $user) {
                    $user->notify($notification);
                }
            } catch (\Exception $e) {
                Log::error('Error al enviar la notificación de obra actualizada: ' . $e->getMessage());
                Log::error($e);
            }
        }
    }

    /**
     * Handle the Obra "deleted" event.
     */
    public function deleted(Obra $obra): void
    {
        //
    }

    /**
     * Handle the Obra "restored" event.
     */
    public function restored(Obra $obra): void
    {
        //
    }

    /**
     * Handle the Obra "force deleted" event.
     */
    public function forceDeleted(Obra $obra): void
    {
        //
    }
}
