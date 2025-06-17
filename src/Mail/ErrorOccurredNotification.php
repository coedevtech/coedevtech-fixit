<?php

namespace Fixit\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ErrorOccurredNotification extends Mailable
{
    use Queueable, SerializesModels;

    public string $messageContent;
    public ?string $exception;
    public ?string $suggestion;
    public ?int $occurrences;
    public string $date;
    public string $environment;

    public function __construct(
        string $messageContent,
        ?string $exception = null,
        ?string $suggestion = null,
        ?int $occurrences = null,
        string $date = '',
        string $environment = ''
    ) {
        $this->messageContent = $messageContent;
        $this->suggestion = $suggestion;
        $this->exception = $exception;
        $this->occurrences = $occurrences;
        $this->date = $date;
        $this->environment = $environment;
    }

    public function build(): self
    {
        return $this->subject('🚨 Fixit Error Logged')
            ->view('fixit::emails.error_occurred')
            ->with([
                'errorMessage' => $this->messageContent,
                'suggestion' => $this->suggestion,
                'exception' => $this->exception,
                'occurrences' => $this->occurrences,
                'date' => $this->date,
                'environment' => $this->environment,
            ]);
    }
}

