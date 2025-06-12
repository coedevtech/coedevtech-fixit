<?php

namespace Fixit\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ErrorOccurredNotification extends Mailable
{
    use Queueable, SerializesModels;

    public string $messageContent;

    public function __construct($messageContent)
    {
        $this->messageContent = $messageContent;
    }

    public function build(): self
    {
        return $this->subject('🚨 FixIt Error Logged')
            ->view('fixit::emails.error_occurred')
            ->with([
                'errorMessage' => $this->messageContent,
            ]);
    }
}
