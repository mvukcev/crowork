<?php

namespace App\Providers;

use App\Models\Employer;
use App\Models\Job;
use App\Observers\EmployerObserver;
use App\Observers\JobObserver;
use App\Models\EmailSendLog;
use Illuminate\Support\ServiceProvider;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Lang::handleMissingKeysUsing(function (string $key, array $replace, ?string $locale): string {
            Log::warning('Missing translation key', [
                'key' => $key,
                'locale' => $locale ?? app()->getLocale(),
                'url' => request()?->fullUrl(),
            ]);

            return $key;
        });

        Employer::observe(EmployerObserver::class);
        Job::observe(JobObserver::class);

        Event::listen(NotificationSent::class, function (NotificationSent $event): void {
            if ($event->channel !== 'mail') {
                return;
            }

            $notifiable = $event->notifiable;
            $email = $notifiable->email ?? null;

            if (! is_string($email) || $email === '') {
                return;
            }

            EmailSendLog::query()->create([
                'to_address' => $email,
                'template' => class_basename($event->notification),
                'context_hash' => null,
                'message_id' => null,
                'sent_at' => now(),
            ]);
        });
    }
}
