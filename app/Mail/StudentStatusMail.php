<?php

namespace App\Mail;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function envelope(): Envelope
    {
        // Capitalize the status for the subject line
        $status = ucfirst($this->student->academic_status);
        
        return new Envelope(
            subject: "Update: Your Academic Status is now {$status}",
        );
    }

    public function content(): Content
    {
        // CRITICAL: This MUST be 'view:', not 'markdown:' for custom HTML
        return new Content(
            view: 'emails.students.status', 
        );
    }
}