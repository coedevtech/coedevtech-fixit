<?php

namespace Fixit\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ErrorOccurredNotification extends Mailable
{
    use Queueable, SerializesModels;

    public string $messageContent;
    public ?string $suggestion;

    public function __construct(string $messageContent, ?string $suggestion = null)
    {
        $this->messageContent = $messageContent;
        $this->suggestion = $suggestion;
    }

    public function build(): self
    {
        return $this->subject('🚨 Fixit Error Logged')
            ->view('fixit::emails.error_occurred')
            ->with([
                'errorMessage' => $this->messageContent,
                'suggestion' => $this->suggestion,
            ]);
    }
}

