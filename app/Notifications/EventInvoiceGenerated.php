<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventInvoiceGenerated implements ShouldQueue
{
    use Queueable;

    private $invoice;

    /**
     * @param null $invoice
     */
    public function __construct(null $invoice)
    {
        $this->invoice = $invoice;
    }


    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $mail = (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Neue Rechnung erstellt - ' . $this->invoice->invoice_number)
            ->greeting('Hallo ' . $notifiable->name . ',')
            ->line('Eine neue Rechnung wurde für die Veranstaltung "' . $this->invoice->event->title . '" erstellt.')
            ->line('Rechnungsnummer: ' . $this->invoice->invoice_number)
            ->line('Betrag: ' . number_format($this->invoice->total_amount, 2, ',', '.') . ' ' . $this->invoice->currency)
            ->action('Rechnung anzeigen', route('invoices.show', $this->invoice->id))
            ->line('Vielen Dank, dass Sie unsere Plattform nutzen!');

        return $mail;
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Neue Rechnung erstellt für ' . $this->invoice->event->title,
            'message' => 'Eine neue Rechnung (' . $this->invoice->invoice_number . ') wurde für die Veranstaltung "' . $this->invoice->event->title . '" erstellt.',
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'event_title' => $this->invoice->event->title,
            'amount' => $this->invoice->total_amount,
            'currency' => $this->invoice->currency,
        ];
    }
}
