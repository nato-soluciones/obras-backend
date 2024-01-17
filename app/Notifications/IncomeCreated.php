<?php

namespace App\Notifications;

use App\Models\Income;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IncomeCreated extends Notification
{
    use Queueable;

    public $income;

    /**
     * Create a new notification instance.
     */
    public function __construct(Income $income)
    {
        $this->income = $income;
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
            ->subject('Nuevo recibo de cobro | ' . $this->income->obra->name)
            ->line('Se ha creado un nuevo recibo de cobro para la obra ' . $this->income->obra->name . '.')
            ->line('Número de Recibo: ' . $this->income->receipt_number)
            ->line('Lugar: ' . $this->income->location)
            ->line('Recibí de: ' . $this->income->exchange_rate)
            ->line('Valor Total (USD): ' . $this->income->amount_usd)
            ->line('Valor Total (ARS): ' . $this->income->amount_ars)
            ->line('Cantidad: ' . $this->income->amount_ars_text)
            ->line('En concepto de: ' . $this->income->payment_concept)
            ->line('Observaciones: ' . $this->income->comments);
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
