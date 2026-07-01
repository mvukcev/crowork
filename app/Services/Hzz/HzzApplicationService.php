<?php

namespace App\Services\Hzz;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Throwable;

class HzzApplicationService
{
    public function sendToEmployer(JobApplication $application, Job $job, User $worker, WorkerProfile $profile): array
    {
        $targetEmail = strtolower(trim((string) $job->hzz_apply_email));

        if ($targetEmail === '' || ! filter_var($targetEmail, FILTER_VALIDATE_EMAIL)) {
            Log::warning('HZZ application send skipped due to missing/invalid employer email.', [
                'application_id' => $application->id,
                'job_id' => $job->id,
                'target_email' => $targetEmail,
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'log' => 'HZZ apply email is missing or invalid.',
            ];
        }

        $subject = 'CroWork prijava: ' . $job->title;

        $bodyLines = [
            'Nova prijava poslana putem CroWork platforme.',
            '',
            'Oglas: ' . $job->title,
            'ID oglasa: ' . $job->id,
            'Kandidat: ' . trim(($profile->first_name ?? '') . ' ' . ($profile->last_name ?? '')),
            'Korisnik (CroWork ID): ' . $worker->id,
            'Kontakt e-mail kandidata: ' . ($worker->email ?? '-'),
            'Trenutni grad: ' . ($profile->current_city ?? '-'),
            'Traženi grad: ' . ($profile->desired_city ?? '-'),
            'Dostupnost: ' . ($profile->availability_date?->toDateString() ?? '-'),
            '',
            'Motivacijsko pismo:',
            trim((string) $application->cover_letter_text) !== '' ? trim((string) $application->cover_letter_text) : '(nije uneseno)',
            '',
            'Profil snapshot je spremljen unutar CroWork sustava uz ID prijave: ' . $application->id,
        ];

        try {
            Mail::raw(implode("\n", $bodyLines), function ($message) use ($application, $targetEmail, $subject, $worker): void {
                $message->to($targetEmail)
                    ->subject($subject);

                if (filled($worker->email)) {
                    $message->replyTo((string) $worker->email);
                }

                if (filled($application->cv_file_path) && Storage::disk('local')->exists((string) $application->cv_file_path)) {
                    $message->attach(Storage::disk('local')->path((string) $application->cv_file_path));
                }
            });

            return [
                'success' => true,
                'status' => 'sent',
                'log' => 'Application forwarded to employer email successfully.',
            ];
        } catch (Throwable $exception) {
            Log::warning('HZZ application email send failed.', [
                'application_id' => $application->id,
                'job_id' => $job->id,
                'target_email' => $targetEmail,
                'error' => $exception->getMessage(),
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'log' => 'Email send failed: ' . $exception->getMessage(),
            ];
        }
    }
}
