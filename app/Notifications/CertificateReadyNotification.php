<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Certificate $certificate) {}

    public function via($notifiable): array { return ['mail', 'database']; }

    public function toMail($notifiable): MailMessage
    {
        $courseName = $this->certificate->batchStudent->batch->course->title;
        $filePath = storage_path('app/public/' . $this->certificate->file_path);
        
        return (new MailMessage)
            ->subject("Your Certificate for {$courseName} is Ready!")
            ->line("Congratulations on completing **{$courseName}** with {$this->certificate->attendance_percentage}% attendance.")
            ->line("Please find your official certificate attached.")
            ->attach($filePath, [
                'as' => "Certificate_{$this->certificate->certificate_number}.pdf",
                'mime' => 'application/pdf',
            ]);
    }

    public function toArray($notifiable): array
    {
        $courseName = $this->certificate->batchStudent->batch->course->title;

        return [
            'message' => "Your certificate for {$courseName} is ready.",
            'link' => route('student.certificates.download', $this->certificate->id)
        ];
    }
}