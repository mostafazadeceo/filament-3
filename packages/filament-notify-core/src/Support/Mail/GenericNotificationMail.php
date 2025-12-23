<?php

namespace Haida\FilamentNotify\Core\Support\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericNotificationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public ?string $subjectLine,
        public string $htmlBody,
        public ?string $fromAddress = null,
        public ?string $fromName = null,
    ) {}

    public function build(): self
    {
        if ($this->fromAddress) {
            $this->from($this->fromAddress, $this->fromName);
        }

        if ($this->subjectLine) {
            $this->subject($this->subjectLine);
        }

        return $this->html($this->htmlBody);
    }
}
