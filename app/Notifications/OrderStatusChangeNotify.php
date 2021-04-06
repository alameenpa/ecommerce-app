<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChangeNotify extends Notification
{
    use Queueable;

    public $status, $transaction;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($transaction, $status)
    {
        $this->transaction = $transaction;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Order Status Update (ORD-' . $this->transaction->order->id . ')')
            ->greeting('Hello', $this->transaction->order->user->name)
            ->line('Your order moved one step ahead, Current status:' . $this->status)
            ->line('Thank you for being with us!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
