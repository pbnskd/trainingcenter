<?php

namespace App\Mail;

use App\Models\Batch;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentAddedToBatch extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Batch $batch, public Student $student) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Enrollment Confirmed: ' . $this->batch->course->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.batches.student-added',
        );
    }
}