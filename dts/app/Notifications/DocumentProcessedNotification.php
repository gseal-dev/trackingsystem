<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Document;

class DocumentProcessedNotification extends Notification
{
    use Queueable;

    protected $document;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $status = $this->document->status->statusName ?? 'Processed';
        return (new MailMessage)
            ->subject('Your Document Has Been ' . $status)
            ->greeting('Hello ' . $notifiable->firstName . '!')
            ->line("Your document '{$this->document->title}'")
            ->line("STATUS: {$status}.")
            ->line("Current Office: {$office}")
            ->action('View Document', url('/dashboard'))
            ->line('Thank you for using the Document Tracking System!');
    }
}