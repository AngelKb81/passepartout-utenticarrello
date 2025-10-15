<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
            ->subject('Benvenuto in Passepartout!')
            ->greeting('Ciao ' . $notifiable->nome . '!')
            ->line('Benvenuto nel nostro sistema di gestione utenti e carrello.')
            ->line('La tua registrazione è stata completata con successo.')
            ->line('Ora puoi accedere a tutte le funzionalità del portale:')
            ->line('• Gestione profilo personale')
            ->line('• Catalogo prodotti completo')
            ->line('• Carrello e checkout personalizzato')
            ->action('Accedi al portale', url('/login'))
            ->line('Grazie per esserti registrato con noi!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $notifiable->id,
            'message' => 'Nuovo utente registrato: ' . $notifiable->nome . ' ' . $notifiable->cognome,
        ];
    }
}
