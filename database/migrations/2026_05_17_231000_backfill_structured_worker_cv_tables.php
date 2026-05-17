<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('worker_profiles')) {
            return;
        }

        if (! Schema::hasTable('worker_experiences') || ! Schema::hasTable('worker_educations') || ! Schema::hasTable('worker_certifications') || ! Schema::hasTable('worker_references') || ! Schema::hasTable('worker_skills') || ! Schema::hasTable('worker_languages')) {
            return;
        }

        DB::table('worker_profiles')
            ->orderBy('id')
            ->chunkById(200, function ($profiles): void {
                foreach ($profiles as $profile) {
                    $this->backfillProfile((array) $profile);
                }
            });
    }

    public function down(): void
    {
        // No-op. Data backfill should not be removed automatically.
    }

    private function backfillProfile(array $profile): void
    {
        $profileId = (int) ($profile['id'] ?? 0);
        if ($profileId <= 0) {
            return;
        }

        $now = now();

        if ($this->tableHasRows('worker_skills', $profileId) === false) {
            $skills = $this->decodeJsonArray($profile['skills'] ?? null);
            if ($skills !== []) {
                $rows = [];
                foreach (array_values($skills) as $index => $skill) {
                    $value = trim((string) $skill);
                    if ($value === '') {
                        continue;
                    }

                    $rows[] = [
                        'worker_profile_id' => $profileId,
                        'name' => $value,
                        'level' => null,
                        'sort_order' => $index,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if ($rows !== []) {
                    DB::table('worker_skills')->insert($rows);
                }
            }
        }

        if ($this->tableHasRows('worker_languages', $profileId) === false) {
            $languages = $this->decodeJsonArray($profile['languages'] ?? null);
            if ($languages !== []) {
                $rows = [];
                foreach (array_values($languages) as $index => $language) {
                    if (is_array($language)) {
                        $name = trim((string) ($language['language'] ?? ''));
                        $level = trim((string) ($language['level'] ?? ''));
                    } else {
                        $name = trim((string) $language);
                        $level = '';
                    }

                    if ($name === '') {
                        continue;
                    }

                    $rows[] = [
                        'worker_profile_id' => $profileId,
                        'language' => $name,
                        'level' => $level !== '' ? $level : null,
                        'sort_order' => $index,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if ($rows !== []) {
                    DB::table('worker_languages')->insert($rows);
                }
            }
        }

        if ($this->tableHasRows('worker_experiences', $profileId) === false) {
            $workExperience = trim((string) ($profile['work_experience'] ?? ''));
            if ($workExperience !== '') {
                DB::table('worker_experiences')->insert([
                    'worker_profile_id' => $profileId,
                    'job_title' => 'Legacy Experience',
                    'company_name' => null,
                    'country' => null,
                    'city' => null,
                    'start_date' => null,
                    'end_date' => null,
                    'is_current' => false,
                    'description' => $workExperience,
                    'sort_order' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        if ($this->tableHasRows('worker_educations', $profileId) === false) {
            $educationSummary = trim((string) ($profile['education_summary'] ?? ''));
            if ($educationSummary !== '') {
                DB::table('worker_educations')->insert([
                    'worker_profile_id' => $profileId,
                    'institution' => 'Legacy Education',
                    'degree' => null,
                    'field_of_study' => null,
                    'country' => null,
                    'city' => null,
                    'start_date' => null,
                    'end_date' => null,
                    'description' => $educationSummary,
                    'sort_order' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        if ($this->tableHasRows('worker_certifications', $profileId) === false) {
            $certifications = trim((string) ($profile['certifications'] ?? ''));
            if ($certifications !== '') {
                DB::table('worker_certifications')->insert([
                    'worker_profile_id' => $profileId,
                    'name' => 'Legacy Certifications',
                    'issuer' => null,
                    'issued_on' => null,
                    'expires_on' => null,
                    'credential_id' => null,
                    'credential_url' => null,
                    'sort_order' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        if ($this->tableHasRows('worker_references', $profileId) === false) {
            $recommendations = trim((string) ($profile['recommendations'] ?? ''));
            if ($recommendations !== '') {
                DB::table('worker_references')->insert([
                    'worker_profile_id' => $profileId,
                    'full_name' => 'Legacy Reference',
                    'position' => null,
                    'company' => null,
                    'contact_email' => null,
                    'contact_phone' => null,
                    'notes' => $recommendations,
                    'sort_order' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function tableHasRows(string $table, int $profileId): bool
    {
        return DB::table($table)->where('worker_profile_id', $profileId)->exists();
    }

    private function decodeJsonArray(mixed $raw): array
    {
        if (is_array($raw)) {
            return $raw;
        }

        if (! is_string($raw) || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }
};
