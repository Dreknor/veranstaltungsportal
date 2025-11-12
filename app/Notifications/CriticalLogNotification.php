<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CriticalLogNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $logEntry;
    protected $logCount;

    /**
     * Create a new notification instance.
     */
    public function __construct($logEntry, int $logCount = 1)
    {
        $this->logEntry = $logEntry;
        $this->logCount = $logCount;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $subject = $this->logCount > 1
            ? "{$this->logCount} kritische System-Fehler erkannt"
            : "Kritischer System-Fehler erkannt: {$this->logEntry->level_name}";

        $mail = (new MailMessage)
            ->subject($subject)
            ->error()
            ->greeting('⚠️ Kritischer Systemfehler')
            ->line("Ein kritischer Fehler wurde im System erkannt und erfordert möglicherweise Ihre Aufmerksamkeit.");

        if ($this->logCount > 1) {
            $mail->line("**Anzahl der Fehler:** {$this->logCount} in den letzten 5 Minuten");
        }

        $mail->line("**Level:** {$this->logEntry->level_name}")
            ->line("**Channel:** " . ($this->logEntry->channel ?? 'Unbekannt'))
            ->line("**Zeit:** " . $this->formatDateTime($this->logEntry->datetime))
            ->line("**Nachricht:**")
            ->line($this->truncateMessage($this->logEntry->message, 200));

        if ($this->logEntry->context) {
            $context = is_string($this->logEntry->context)
                ? json_decode($this->logEntry->context, true)
                : $this->logEntry->context;

            if (is_array($context) && isset($context['exception'])) {
                $mail->line("**Exception:** " . (is_string($context['exception'])
                    ? substr($context['exception'], 0, 100) . '...'
                    : 'Siehe Log-Details'));
            }
        }

        $mail->action('Log-Details anzeigen', route('admin.system-logs.show', $this->logEntry->id))
            ->line('Bitte überprüfen Sie die Details und ergreifen Sie bei Bedarf Maßnahmen.');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'log_id' => $this->logEntry->id,
            'level' => $this->logEntry->level_name,
            'channel' => $this->logEntry->channel,
            'message' => $this->truncateMessage($this->logEntry->message, 200),
            'datetime' => $this->logEntry->datetime,
            'count' => $this->logCount,
            'url' => route('admin.system-logs.show', $this->logEntry->id),
        ];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }

    /**
     * Format datetime with error handling
     */
    protected function formatDateTime($datetime): string
    {
        if (!$datetime) {
            return 'Unbekannt';
        }

        try {
            return \Carbon\Carbon::parse($datetime)->format('d.m.Y H:i:s');
        } catch (\Exception $e) {
            // Bereinige fehlerhaftes Format
            $cleanDate = preg_replace('/:\d{4}$/', '', $datetime);
            try {
                return \Carbon\Carbon::parse($cleanDate)->format('d.m.Y H:i:s');
            } catch (\Exception $e2) {
                return $datetime;
            }
        }
    }

    /**
     * Truncate message to specified length
     */
    protected function truncateMessage(string $message, int $length = 200): string
    {
        if (strlen($message) <= $length) {
            return $message;
        }

        return substr($message, 0, $length) . '...';
    }
}

