<?php

namespace App\Notifications;

use App\Models\Obra;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ObraCreated extends Notification
{
    use Queueable;

    public $obra;

    /**
     * Create a new notification instance.
     */
    public function __construct(Obra $obra)
    {
        $this->obra = $obra;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Novedad: Presupuesto de obra ' . $this->obra->name .  ' aprobado')
            ->greeting('El presupuesto de la obra ' . $this->obra->name . ' pasó a estado aprobado. Se creó la obra ' . $this->obra->name)
            ->salutation(' ');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
