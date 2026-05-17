<?php

namespace App\Support;

class StructuredCvLegacyFormatter
{
    public static function educationSummary(array $educations): ?string
    {
        if ($educations === []) {
            return null;
        }

        $lines = array_map(function (array $education): string {
            $title = trim(implode(' - ', array_filter([
                $education['institution'] ?? null,
                $education['degree'] ?? null,
                $education['field_of_study'] ?? null,
            ])));

            $period = trim(implode(' - ', array_filter([
                $education['start_date'] ?? null,
                $education['end_date'] ?? null,
            ])));

            return trim($title . ($period !== '' ? ' (' . $period . ')' : ''));
        }, $educations);

        return implode("\n", array_filter($lines));
    }

    public static function experienceSummary(array $experiences): ?string
    {
        if ($experiences === []) {
            return null;
        }

        $lines = array_map(function (array $experience): string {
            $title = trim(implode(' @ ', array_filter([
                $experience['job_title'] ?? null,
                $experience['company_name'] ?? null,
            ])));

            $period = trim(implode(' - ', array_filter([
                $experience['start_date'] ?? null,
                $experience['end_date'] ?? ($experience['is_current'] ?? false ? 'current' : null),
            ])));

            return trim($title . ($period !== '' ? ' (' . $period . ')' : ''));
        }, $experiences);

        return implode("\n", array_filter($lines));
    }

    public static function certificationSummary(array $certifications): ?string
    {
        if ($certifications === []) {
            return null;
        }

        $lines = array_map(fn (array $certification): string => trim(implode(' - ', array_filter([
            $certification['name'] ?? null,
            $certification['issuer'] ?? null,
        ]))), $certifications);

        return implode("\n", array_filter($lines));
    }

    public static function referenceSummary(array $references): ?string
    {
        if ($references === []) {
            return null;
        }

        $lines = array_map(fn (array $reference): string => trim(implode(' - ', array_filter([
            $reference['full_name'] ?? null,
            $reference['position'] ?? null,
            $reference['company'] ?? null,
        ]))), $references);

        return implode("\n", array_filter($lines));
    }
}
