<?php

namespace App\Filament\Admin\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class SystemHealth extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'System Health';

    protected static ?string $title = 'System Health';

    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament.admin.pages.system-health';

    /** @var array<int, array{label: string, status: string, value: string, details: string}> */
    public array $checks = [];

    public function mount(): void
    {
        $this->refreshChecks();
    }

    protected function refreshChecks(): void
    {
        $this->checks = [
            $this->checkAppEnvironment(),
            $this->checkStorageWritable(),
            $this->checkBootstrapCacheWritable(),
            $this->checkBuildManifest(),
            $this->checkLivewireAssets(),
            $this->checkFilamentAssets(),
            $this->checkQueueConnection(),
            $this->checkFailedJobsCount(),
            $this->checkSchedulerLastRun(),
            $this->checkMailConfiguration(),
            $this->checkMailTestStatus(),
            $this->checkSitemapStatus(),
            $this->checkLlmsStatus(),
            $this->checkStorageLink(),
            $this->checkDiskFreeSpace(),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()?->isAdmin();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('retryFailedJobs')
                ->label('Retry Failed Jobs')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function (): void {
                    if (! Schema::hasTable('failed_jobs')) {
                        Notification::make()
                            ->title('failed_jobs table missing')
                            ->danger()
                            ->send();

                        return;
                    }

                    $uuids = DB::table('failed_jobs')->pluck('uuid')->filter()->values()->all();

                    if ($uuids === []) {
                        Notification::make()
                            ->title('No failed jobs to retry')
                            ->success()
                            ->send();

                        $this->refreshChecks();

                        return;
                    }

                    Artisan::call('queue:retry', ['id' => $uuids]);

                    Notification::make()
                        ->title('Retry queued for failed jobs')
                        ->body(sprintf('Retried %d failed job(s).', count($uuids)))
                        ->success()
                        ->send();

                    $this->refreshChecks();
                }),
            Action::make('clearFailedJobs')
                ->label('Clear Failed Jobs')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (): void {
                    if (! Schema::hasTable('failed_jobs')) {
                        Notification::make()
                            ->title('failed_jobs table missing')
                            ->danger()
                            ->send();

                        return;
                    }

                    Artisan::call('queue:flush');

                    Notification::make()
                        ->title('Failed jobs cleared')
                        ->success()
                        ->send();

                    $this->refreshChecks();
                }),
            Action::make('sendMailTest')
                ->label('Send Mail Test')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->action(function (): void {
                    $target = (string) config('mail.from.address', '');

                    if ($target === '') {
                        Cache::put('mail:last_test_status', [
                            'status' => 'fail',
                            'tested_at' => now()->toIso8601String(),
                            'target' => null,
                            'message' => 'MAIL_FROM_ADDRESS is missing.',
                        ], now()->addDays(7));

                        Notification::make()
                            ->title('Mail test failed')
                            ->body('MAIL_FROM_ADDRESS is not configured.')
                            ->danger()
                            ->send();

                        $this->refreshChecks();

                        return;
                    }

                    try {
                        Mail::raw('CroWork System Health test email sent at '.now()->toDateTimeString(), function ($message) use ($target): void {
                            $message->to($target)->subject('CroWork Mail Health Test');
                        });

                        Cache::put('mail:last_test_status', [
                            'status' => 'ok',
                            'tested_at' => now()->toIso8601String(),
                            'target' => $target,
                            'message' => 'Mail test sent successfully.',
                        ], now()->addDays(7));

                        Notification::make()
                            ->title('Mail test sent')
                            ->body('Sent test email to '.$target)
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Cache::put('mail:last_test_status', [
                            'status' => 'fail',
                            'tested_at' => now()->toIso8601String(),
                            'target' => $target,
                            'message' => $e->getMessage(),
                        ], now()->addDays(7));

                        Notification::make()
                            ->title('Mail test failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }

                    $this->refreshChecks();
                }),
        ];
    }

    /**
     * @return array{label: string, status: string, value: string, details: string}
     */
    protected function checkAppEnvironment(): array
    {
        $env = (string) config('app.env', 'unknown');
        $debug = (bool) config('app.debug', false);

        $status = 'ok';
        if ($env === 'production' && $debug) {
            $status = 'warn';
        }

        return [
            'label' => 'APP_ENV / APP_DEBUG',
            'status' => $status,
            'value' => sprintf('%s / %s', $env, $debug ? 'true' : 'false'),
            'details' => $debug ? 'Debug mode is enabled.' : 'Debug mode is disabled.',
        ];
    }

    /**
     * @return array{label: string, status: string, value: string, details: string}
     */
    protected function checkStorageWritable(): array
    {
        $path = storage_path();
        $writable = is_dir($path) && is_writable($path);

        return [
            'label' => 'Storage Writable',
            'status' => $writable ? 'ok' : 'fail',
            'value' => $writable ? 'Yes' : 'No',
            'details' => $path,
        ];
    }

    /**
     * @return array{label: string, status: string, value: string, details: string}
     */
    protected function checkBootstrapCacheWritable(): array
    {
        $path = base_path('bootstrap/cache');
        $writable = is_dir($path) && is_writable($path);

        return [
            'label' => 'bootstrap/cache Writable',
            'status' => $writable ? 'ok' : 'fail',
            'value' => $writable ? 'Yes' : 'No',
            'details' => $path,
        ];
    }

    /**
     * @return array{label: string, status: string, value: string, details: string}
     */
    protected function checkBuildManifest(): array
    {
        $path = public_path('build/manifest.json');
        $exists = file_exists($path);

        return [
            'label' => 'public/build Manifest',
            'status' => $exists ? 'ok' : 'fail',
            'value' => $exists ? 'Present' : 'Missing',
            'details' => $path,
        ];
    }

    /**
     * @return array{label: string, status: string, value: string, details: string}
     */
    protected function checkLivewireAssets(): array
    {
        $paths = [
            public_path('vendor/livewire/livewire.js'),
            base_path('vendor/livewire/livewire/dist/livewire.js'),
        ];

        $found = collect($paths)->first(fn (string $path) => file_exists($path));

        return [
            'label' => 'Livewire Assets',
            'status' => $found ? 'ok' : 'warn',
            'value' => $found ? 'Found' : 'Not found on disk',
            'details' => $found ?: 'Livewire may still be served via route.',
        ];
    }

    /**
     * @return array{label: string, status: string, value: string, details: string}
     */
    protected function checkFilamentAssets(): array
    {
        $paths = [
            public_path('vendor/filament'),
            base_path('vendor/filament'),
        ];

        $found = collect($paths)->first(fn (string $path) => file_exists($path));

        return [
            'label' => 'Filament Assets',
            'status' => $found ? 'ok' : 'fail',
            'value' => $found ? 'Found' : 'Missing',
            'details' => $found ?: 'Expected vendor assets directory not found.',
        ];
    }

    /**
     * @return array{label: string, status: string, value: string, details: string}
     */
    protected function checkQueueConnection(): array
    {
        $default = (string) config('queue.default', '');
        $configured = Config::has("queue.connections.{$default}");

        if (! $configured) {
            return [
                'label' => 'Queue Status',
                'status' => 'fail',
                'value' => $default !== '' ? $default : 'not set',
                'details' => 'Queue connection config missing.',
            ];
        }

        if ($default === 'sync') {
            return [
                'label' => 'Queue Status',
                'status' => 'warn',
                'value' => 'sync',
                'details' => 'Sync queue is active. Use a worker-backed connection in production.',
            ];
        }

        if ($default === 'database') {
            if (! Schema::hasTable('jobs')) {
                return [
                    'label' => 'Queue Status',
                    'status' => 'fail',
                    'value' => 'database',
                    'details' => 'jobs table does not exist.',
                ];
            }

            $pending = (int) DB::table('jobs')->count();
            $oldestCreatedAt = DB::table('jobs')->min('created_at');
            $oldestAgeMinutes = null;

            if ($oldestCreatedAt !== null) {
                $oldestAgeMinutes = (int) now()->diffInMinutes(Carbon::createFromTimestamp((int) $oldestCreatedAt));
            }

            $status = 'ok';
            if ($pending > 0 && $oldestAgeMinutes !== null && $oldestAgeMinutes >= 10) {
                $status = 'warn';
            }

            $details = $pending === 0
                ? 'No pending queued jobs.'
                : sprintf('Pending jobs: %d. Oldest queued %d minute(s) ago.', $pending, $oldestAgeMinutes ?? 0);

            return [
                'label' => 'Queue Status',
                'status' => $status,
                'value' => 'database',
                'details' => $details,
            ];
        }

        return [
            'label' => 'Queue Status',
            'status' => 'ok',
            'value' => $default !== '' ? $default : 'not set',
            'details' => 'Queue connection configuration found.',
        ];
    }

    /**
     * @return array{label: string, status: string, value: string, details: string}
     */
    protected function checkFailedJobsCount(): array
    {
        if (! Schema::hasTable('failed_jobs')) {
            return [
                'label' => 'Failed Jobs Count',
                'status' => 'warn',
                'value' => 'N/A',
                'details' => 'failed_jobs table does not exist.',
            ];
        }

        $count = (int) DB::table('failed_jobs')->count();

        return [
            'label' => 'Failed Jobs Count',
            'status' => $count === 0 ? 'ok' : 'warn',
            'value' => (string) $count,
            'details' => $count === 0 ? 'No failed jobs.' : 'Review and retry/clear failed jobs.',
        ];
    }

    /**
     * @return array{label: string, status: string, value: string, details: string}
     */
    protected function checkSchedulerLastRun(): array
    {
        $cacheKeys = [
            'scheduler:last_run_at',
            'schedule:last_run_at',
            'schedule_last_run_at',
        ];

        $lastRun = null;

        foreach ($cacheKeys as $key) {
            $value = Cache::get($key);
            if ($value) {
                $lastRun = $value;
                break;
            }
        }

        if (! $lastRun) {
            return [
                'label' => 'Scheduler Last Run',
                'status' => 'warn',
                'value' => 'Unknown',
                'details' => 'No scheduler heartbeat key found in cache.',
            ];
        }

        try {
            $date = $lastRun instanceof Carbon ? $lastRun : Carbon::parse((string) $lastRun);
            $minutes = $date->diffInMinutes(now());
            $status = $minutes <= 5 ? 'ok' : 'warn';

            return [
                'label' => 'Scheduler Last Run',
                'status' => $status,
                'value' => $date->toDateTimeString(),
                'details' => sprintf('%d minute(s) ago.', $minutes),
            ];
        } catch (\Throwable) {
            return [
                'label' => 'Scheduler Last Run',
                'status' => 'warn',
                'value' => 'Unknown',
                'details' => 'Heartbeat value exists but could not be parsed.',
            ];
        }
    }

    /**
     * @return array{label: string, status: string, value: string, details: string}
     */
    protected function checkMailConfiguration(): array
    {
        $mailer = (string) config('mail.default', '');
        $configured = $mailer !== '' && Config::has("mail.mailers.{$mailer}");

        if (! $configured) {
            return [
                'label' => 'Mail Config Status',
                'status' => 'fail',
                'value' => 'Not configured',
                'details' => 'mail.default or selected mailer is missing.',
            ];
        }

        $driverConfig = (array) config("mail.mailers.{$mailer}", []);
        $status = 'ok';

        if ($mailer === 'smtp') {
            $host = (string) ($driverConfig['host'] ?? '');
            $port = (string) ($driverConfig['port'] ?? '');
            if ($host === '' || $port === '') {
                $status = 'warn';
            }
        }

        return [
            'label' => 'Mail Config Status',
            'status' => $status,
            'value' => $mailer,
            'details' => $status === 'ok' ? 'Mailer configuration appears valid.' : 'SMTP selected but host/port missing.',
        ];
    }

    /**
     * @return array{label: string, status: string, value: string, details: string}
     */
    protected function checkMailTestStatus(): array
    {
        /** @var array{status?: string, tested_at?: string, target?: string|null, message?: string}|null $lastTest */
        $lastTest = Cache::get('mail:last_test_status');

        if (! is_array($lastTest)) {
            return [
                'label' => 'Mail Test Status',
                'status' => 'warn',
                'value' => 'Never tested',
                'details' => 'Use "Send Mail Test" action to verify outbound mail.',
            ];
        }

        $testedAt = isset($lastTest['tested_at']) ? (string) $lastTest['tested_at'] : 'Unknown time';
        $target = isset($lastTest['target']) && $lastTest['target'] ? ' to '.$lastTest['target'] : '';
        $message = isset($lastTest['message']) ? (string) $lastTest['message'] : '';
        $isOk = ($lastTest['status'] ?? '') === 'ok';

        return [
            'label' => 'Mail Test Status',
            'status' => $isOk ? 'ok' : 'warn',
            'value' => $isOk ? 'Pass' : 'Fail',
            'details' => trim($testedAt.$target.' - '.$message),
        ];
    }

    /**
     * @return array{label: string, status: string, value: string, details: string}
     */
    protected function checkSitemapStatus(): array
    {
        return $this->checkUrlStatus('Sitemap URL Status', '/sitemap.xml');
    }

    /**
     * @return array{label: string, status: string, value: string, details: string}
     */
    protected function checkLlmsStatus(): array
    {
        return $this->checkUrlStatus('llms.txt Status', '/llms.txt');
    }

    /**
     * @return array{label: string, status: string, value: string, details: string}
     */
    protected function checkStorageLink(): array
    {
        $link = public_path('storage');
        $exists = is_link($link) || is_dir($link);

        return [
            'label' => 'Storage Link Exists',
            'status' => $exists ? 'ok' : 'fail',
            'value' => $exists ? 'Yes' : 'No',
            'details' => $link,
        ];
    }

    /**
     * @return array{label: string, status: string, value: string, details: string}
     */
    protected function checkDiskFreeSpace(): array
    {
        $freeBytes = @disk_free_space(base_path());
        $totalBytes = @disk_total_space(base_path());

        if ($freeBytes === false || $totalBytes === false || $totalBytes <= 0) {
            return [
                'label' => 'Disk Usage',
                'status' => 'warn',
                'value' => 'Unknown',
                'details' => 'Unable to determine disk usage.',
            ];
        }

        $usedBytes = $totalBytes - $freeBytes;
        $freeGb = $freeBytes / 1024 / 1024 / 1024;
        $totalGb = $totalBytes / 1024 / 1024 / 1024;
        $usedPercent = ($usedBytes / $totalBytes) * 100;
        $status = $usedPercent < 90 ? 'ok' : 'warn';

        return [
            'label' => 'Disk Usage',
            'status' => $status,
            'value' => number_format($usedPercent, 2).'%',
            'details' => sprintf('Used %s GB / Total %s GB (Free %s GB).', number_format($usedBytes / 1024 / 1024 / 1024, 2), number_format($totalGb, 2), number_format($freeGb, 2)),
        ];
    }

    /**
     * @return array{label: string, status: string, value: string, details: string}
     */
    protected function checkUrlStatus(string $label, string $path): array
    {
        $baseUrl = (string) config('app.url', '');

        if ($baseUrl === '') {
            return [
                'label' => $label,
                'status' => 'warn',
                'value' => 'Unknown',
                'details' => 'APP_URL is not configured.',
            ];
        }

        $url = rtrim($baseUrl, '/') . $path;

        try {
            $response = Http::timeout(4)->withoutRedirecting()->get($url);
            $statusCode = $response->status();

            return [
                'label' => $label,
                'status' => $response->successful() ? 'ok' : 'warn',
                'value' => (string) $statusCode,
                'details' => $url,
            ];
        } catch (\Throwable $e) {
            return [
                'label' => $label,
                'status' => 'warn',
                'value' => 'Error',
                'details' => $e->getMessage(),
            ];
        }
    }
}
