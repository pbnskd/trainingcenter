<?php

namespace App\Mail;

use App\Models\Batch;
use App\Models\User; // Assuming Faculty is a User
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BatchAssignedToFaculty extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Batch $batch, public User $faculty) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Batch Assignment: ' . $this->batch->batch_code,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.batches.faculty-assigned',
        );
    }
}