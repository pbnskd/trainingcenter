<?php

namespace App\Mail;

use App\Models\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BatchStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Batch $batch, public string $recipientRole) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Batch Update: ' . $this->batch->batch_code . ' is now ' . ucfirst($this->batch->status),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.batches.status-changed',
        );
    }
}