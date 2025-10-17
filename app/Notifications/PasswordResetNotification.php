<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\UserEmail;
use Illuminate\Support\Facades\Log;

class PasswordResetNotification extends Notification
{
    use Queueable;

    public $resetUrl;
    public $nome;
    public $userId;

    /**
     * Create a new notification instance.
     */
    public function __construct($resetUrl, $nome = 'Utente', $userId = null)
    {
        $this->resetUrl = $resetUrl;
        $this->nome = $nome;
        $this->userId = $userId;
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
        $subject = 'Reset Password - Passepartout';

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting('Ciao ' . $this->nome . '!')
            ->line('Hai richiesto il reset della tua password.')
            ->line('Clicca sul pulsante qui sotto per procedere:')
            ->action('Reset Password', $this->resetUrl)
            ->line('Questo link è valido per 60 minuti.')
            ->line('Se non hai richiesto il reset della password, ignora questa email.')
            ->salutation('Grazie, Il team di Passepartout');

        // Log email nel database
        try {
            UserEmail::logEmail(
                $this->userId ?? $notifiable->id ?? null,
                $notifiable->email ?? $notifiable->routes['mail'] ?? 'unknown',
                $this->nome,
                'reset_password',
                $subject,
                'Email di reset password con link: ' . $this->resetUrl,
                'sent'
            );
        } catch (\Exception $e) {
            Log::error('Errore log email reset password:', ['error' => $e->getMessage()]);
        }

        return $message;
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
