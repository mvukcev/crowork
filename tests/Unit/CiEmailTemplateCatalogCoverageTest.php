<?php

namespace Tests\Unit;

use App\Services\EmailTemplateService;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CiEmailTemplateCatalogCoverageTest extends TestCase
{
    public function test_every_notification_class_is_mapped_in_email_template_catalog_source(): void
    {
        $catalog = EmailTemplateService::catalog();

        $mappedNotifications = collect($catalog)
            ->pluck('source')
            ->filter(fn ($source) => is_string($source) && $source !== '')
            ->flatMap(function (string $source): array {
                preg_match_all('/App\\\\Notifications\\\\([A-Za-z0-9_]+)/', $source, $matches);

                return $matches[1] ?? [];
            })
            ->unique()
            ->values()
            ->all();

        $notificationFiles = File::files(app_path('Notifications'));
        $notificationClasses = collect($notificationFiles)
            ->map(fn ($file) => pathinfo($file->getFilename(), PATHINFO_FILENAME))
            ->filter(fn ($class) => is_string($class) && $class !== '')
            ->values()
            ->all();

        $missing = array_values(array_diff($notificationClasses, $mappedNotifications));

        $this->assertSame(
            [],
            $missing,
            'Notification classes missing in EmailTemplateService::catalog source mapping: ' . implode(', ', $missing)
        );
    }

    public function test_admin_new_bug_report_template_key_exists_in_catalog(): void
    {
        $catalog = EmailTemplateService::catalog();

        $this->assertArrayHasKey('admin_new_bug_report', $catalog);
        $this->assertSame('App\\Notifications\\AdminNewBugReport', $catalog['admin_new_bug_report']['source']);
    }
}
