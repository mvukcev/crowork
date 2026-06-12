<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CiLocalizationGuardrailsTest extends TestCase
{
    public function test_bug_report_translation_keys_exist_for_en_and_hr(): void
    {
        $requiredKeys = [
            'ui.bug_report.trigger',
            'ui.bug_report.title',
            'ui.bug_report.close',
            'ui.bug_report.description',
            'ui.bug_report.problem_label',
            'ui.bug_report.problem_placeholder',
            'ui.bug_report.screenshot_label',
            'ui.bug_report.screenshot_help',
            'ui.bug_report.submit',
        ];

        foreach ($requiredKeys as $key) {
            $this->assertNotSame($key, trans($key, [], 'en'), "Missing EN translation key: {$key}");
            $this->assertNotSame($key, trans($key, [], 'hr'), "Missing HR translation key: {$key}");
        }
    }

    public function test_bug_report_banner_has_no_hardcoded_english_ui_copy(): void
    {
        $content = File::get(resource_path('views/components/bug-report-banner.blade.php'));

        $this->assertStringNotContainsString('Report a bug', $content);
        $this->assertStringNotContainsString('Screenshot (optional)', $content);
        $this->assertStringContainsString("__('ui.bug_report.trigger')", $content);
        $this->assertStringContainsString("__('ui.bug_report.submit')", $content);
    }

    public function test_admin_module_options_include_bugs_and_error_logs(): void
    {
        $options = User::adminModuleOptions();

        $this->assertArrayHasKey('bugs', $options);
        $this->assertArrayHasKey('error-logs', $options);
    }
}
