<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CartCheckedOut extends Notification
{
    use Queueable;

    protected $cart;
    protected $totalAmount;

    /**
     * Create a new notification instance.
     */
    public function __construct($cart, float $totalAmount)
    {
        $this->cart = $cart;
        $this->totalAmount = $totalAmount;
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
            ->subject('Checkout completato - Passepartout')
            ->greeting('Ciao ' . $notifiable->nome . '!')
            ->line('Il tuo ordine è stato elaborato con successo.')
            ->line('Totale ordine: €' . number_format($this->totalAmount, 2))
            ->line('Numero prodotti: ' . $this->cart->items()->count())
            ->line('Riceverai conferma del pagamento entro 24 ore.')
            ->action('Visualizza profilo', url('/profile'))
            ->line('Grazie per aver scelto Passepartout!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'cart_id' => $this->cart->id,
            'total_amount' => $this->totalAmount,
            'items_count' => $this->cart->items()->count(),
        ];
    }
}
