<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Obra;

use App\Notifications\ObraCreated;
use App\Notifications\ObraFinalized;

class ObraObserver
{
    /**
     * Handle the Obra "created" event.
     */
    public function created(Obra $obra): void
    {
        $users = User::whereIn('role', ['OWNER', 'ARCHITECT'])->get();
        $notification = new ObraCreated($obra);

        foreach ($users as $user) {
            $user->notify($notification);
        }
    }

    /**
     * Handle the Obra "updated" event.
     */
    public function updated(Obra $obra): void
    {
        if ($obra->status === 'FINALIZED') {
            $users = User::whereIn('role', ['OWNER', 'ARCHITECT'])->get();
            $notification = new ObraFinalized($obra);

            foreach ($users as $user) {
                $user->notify($notification);
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
