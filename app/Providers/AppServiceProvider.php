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
use Illuminate\Support\Facades\Vite;

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
        $viteHotFile = storage_path('framework/vite.hot');
        $viteManifestPath = public_path('build/manifest.json');

        Vite::useHotFile($viteHotFile)->useBuildDirectory('build');

        if (app()->isProduction()) {
            $legacyPublicHotFile = public_path('hot');

            if (is_file($legacyPublicHotFile)) {
                Log::warning('Legacy Vite hot file detected in production. This file must not be deployed.', [
                    'path' => $legacyPublicHotFile,
                ]);
            }

            if (is_file($viteHotFile)) {
                @unlink($viteHotFile);

                Log::warning('Stale Vite hot file removed in production to force manifest-based assets.', [
                    'path' => $viteHotFile,
                ]);
            }

            if (! is_file($viteManifestPath)) {
                Log::critical('Vite manifest missing in production. Frontend assets may be unavailable.', [
                    'path' => $viteManifestPath,
                ]);
            }
        }

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

            $subject = null;
            $messageId = null;
            $bodyPreview = null;

            $response = $event->response;

            if (is_object($response) && method_exists($response, 'getMessageId')) {
                $rawMessageId = $response->getMessageId();
                $messageId = is_string($rawMessageId) ? $rawMessageId : null;
            }

            if (is_object($response) && method_exists($response, 'getOriginalMessage')) {
                $originalMessage = $response->getOriginalMessage();

                if (is_object($originalMessage) && method_exists($originalMessage, 'getSubject')) {
                    $rawSubject = $originalMessage->getSubject();
                    $subject = is_string($rawSubject) ? $rawSubject : null;
                }

                $textBody = is_object($originalMessage) && method_exists($originalMessage, 'getTextBody')
                    ? (string) $originalMessage->getTextBody()
                    : '';
                $htmlBody = is_object($originalMessage) && method_exists($originalMessage, 'getHtmlBody')
                    ? (string) $originalMessage->getHtmlBody()
                    : '';

                $source = $textBody !== '' ? $textBody : strip_tags($htmlBody);
                $bodyPreview = $source !== '' ? str($source)->squish()->limit(500)->toString() : null;

                if ($messageId === null && is_object($originalMessage) && method_exists($originalMessage, 'getHeaders')) {
                    $headers = $originalMessage->getHeaders();
                    $header = $headers?->get('Message-ID');
                    $rawHeaderMessageId = $header?->getBodyAsString();
                    $messageId = is_string($rawHeaderMessageId) ? $rawHeaderMessageId : null;
                }
            }

            EmailSendLog::query()->create([
                'to_address' => $email,
                'template' => class_basename($event->notification),
                'subject' => $subject,
                'body_preview' => $bodyPreview,
                'context_hash' => null,
                'message_id' => $messageId,
                'sent_at' => now(),
            ]);
        });
    }
}
