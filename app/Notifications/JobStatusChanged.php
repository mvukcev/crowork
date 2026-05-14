<?php

namespace App\Notifications;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public Job $job, public string $status)
    {
        $this->job->loadMissing('employer');
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isPublished = $this->status === 'published';

        $message = (new MailMessage)
            ->subject($isPublished ? 'CroWork: job approved' : 'CroWork: job rejected/delisted')
            ->greeting('Hi '.$notifiable->name.',')
            ->line($isPublished
                ? 'Your job listing "'.$this->job->title.'" has been approved and is now visible to workers.'
                : 'Your job listing "'.$this->job->title.'" is no longer publicly visible.'
            );

        if ($isPublished) {
            $message->action('View job listing', route('jobs.show', $this->job));
        } else {
            $message->action('Go to employer dashboard', url('/employer'));
        }

        return $message;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'job_id' => $this->job->id,
            'status' => $this->status,
        ];
    }
}
