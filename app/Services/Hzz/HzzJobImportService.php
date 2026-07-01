<?php

namespace App\Services\Hzz;

use App\Models\Job;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class HzzJobImportService
{
    private const MAX_FEED_BYTES = 5242880;

    /**
     * Common acronyms that should remain uppercase after sentence-case normalization.
     *
     * @var array<int, string>
     */
    private const PRESERVED_ACRONYMS = [
        'HZZ',
        'INA',
        'EU',
        'RH',
        'NKD',
        'HZMO',
        'HZZO',
        'OIB',
        'PDV',
        'HR',
        'CV',
    ];

    public function __construct(
        private readonly HzzApplicationContactParser $contactParser,
    ) {
    }

    public function importFromUrl(string $url, bool $dryRun = false, bool $allowUpdates = false, bool $deactivateMissing = false): array
    {
        $response = Http::accept('application/xml, text/xml;q=0.9, application/rss+xml;q=0.9, */*;q=0.1')
            ->connectTimeout(10)
            ->timeout(30)
            ->retry(3, 1000, throw: false)
            ->get($url);

        if (! $response->ok()) {
            throw new RuntimeException('HZZ import failed with HTTP status ' . $response->status());
        }

        $contentLength = (int) ($response->header('Content-Length') ?? 0);
        $maxFeedBytes = (int) config('services.hzz.max_feed_bytes', self::MAX_FEED_BYTES);
        if ($contentLength > 0 && $contentLength > $maxFeedBytes) {
            throw new RuntimeException('HZZ import failed because the XML feed is too large.');
        }

        $body = $response->body();
        if (strlen($body) > $maxFeedBytes) {
            throw new RuntimeException('HZZ import failed because the XML feed exceeds the maximum allowed size.');
        }

        $payload = $this->decodePayload($body);
        $items = $this->normalizeItems($payload);

        $created = 0;
        $updated = 0;
        $skippedExisting = 0;
        $skippedInvalid = 0;
        $deactivated = 0;
        $preservedManualRecords = 0;
        $seenReferences = [];

        foreach ($items as $item) {
            $mapped = $this->mapItem($item, $url);
            if (! $mapped) {
                $skippedInvalid++;
                continue;
            }

            $seenReferences[] = (string) $mapped['source_reference'];

            if ($dryRun) {
                continue;
            }

            $existing = Job::query()
                ->where('source_system', 'hzz')
                ->where('source_reference', $mapped['source_reference'])
                ->first();

            if ($existing) {
                if (! $allowUpdates) {
                    $skippedExisting++;
                    continue;
                }

                [$payload, $preservedManualEdit] = $this->mergeExistingRecord($existing, $mapped);
                $existing->fill($payload)->save();
                if ($preservedManualEdit) {
                    $preservedManualRecords++;
                }
                $updated++;
                continue;
            }

            Job::query()->create($mapped);
            $created++;
        }

        if (! $dryRun && $deactivateMissing && $seenReferences !== []) {
            $deactivated = Job::query()
                ->where('source_system', 'hzz')
                ->whereNotNull('source_reference')
                ->whereNotIn('source_reference', array_values(array_unique($seenReferences)))
                ->whereIn('status', ['published', 'pending'])
                ->update([
                    'status' => 'delisted',
                    'source_imported_at' => now(),
                ]);
        }

        Log::info('HZZ import summary', [
            'url' => $url,
            'total_items' => count($items),
            'created' => $created,
            'updated' => $updated,
            'skipped_existing' => $skippedExisting,
            'skipped_invalid' => $skippedInvalid,
            'deactivated' => $deactivated,
            'preserved_manual_records' => $preservedManualRecords,
            'dry_run' => $dryRun,
            'allow_updates' => $allowUpdates,
            'deactivate_missing' => $deactivateMissing,
        ]);

        return [
            'total_items' => count($items),
            'created' => $created,
            'updated' => $updated,
            'skipped_existing' => $skippedExisting,
            'skipped_invalid' => $skippedInvalid,
            'deactivated' => $deactivated,
            'preserved_manual_records' => $preservedManualRecords,
            'dry_run' => $dryRun,
            'allow_updates' => $allowUpdates,
        ];
    }

    /**
     * @return array{0: array<string, mixed>, 1: bool}
     */
    private function mergeExistingRecord(Job $existing, array $mapped): array
    {
        $preserveManualEdits = $existing->source_imported_at !== null
            && $existing->updated_at !== null
            && $existing->updated_at->gt($existing->source_imported_at->copy()->addMinutes(5));

        if (! $preserveManualEdits) {
            return [$mapped, false];
        }

        foreach ([
            'external_company_name',
            'title',
            'description',
            'responsibilities',
            'requirements',
            'benefits',
            'location_city',
            'category',
            'contract_type',
            'experience_level',
            'education_required',
            'working_hours',
            'shift_details',
            'positions_available',
            'accommodation_provided',
            'accommodation_details',
            'application_instructions',
            'hzz_legal_notice',
        ] as $field) {
            if (filled($existing->{$field})) {
                $mapped[$field] = $existing->{$field};
            }
        }

        return [$mapped, true];
    }

    private function normalizeItems(mixed $payload): array
    {
        if (is_array($payload) && array_is_list($payload)) {
            return $payload;
        }

        if (is_array($payload)) {
            $candidates = [
                Arr::get($payload, 'items', []),
                Arr::get($payload, 'jobs', []),
                Arr::get($payload, 'data', []),
                Arr::get($payload, 'results', []),
                Arr::get($payload, 'radnaMjesta.radnoMjesto', []),
                Arr::get($payload, 'radnoMjesto', []),
            ];

            foreach ($candidates as $candidate) {
                if (is_array($candidate) && array_is_list($candidate) && count($candidate) > 0) {
                    return $candidate;
                }

                if (is_array($candidate) && $this->looksLikeJobItem($candidate)) {
                    return [$candidate];
                }
            }
        }

        return [];
    }

    private function looksLikeJobItem(array $candidate): bool
    {
        $id = Arr::get($candidate, 'id')
            ?? Arr::get($candidate, 'job_id')
            ?? Arr::get($candidate, 'reference')
            ?? Arr::get($candidate, 'webSifra');

        $title = Arr::get($candidate, 'title')
            ?? Arr::get($candidate, 'job_title')
            ?? Arr::get($candidate, 'nazivRadnogMjesta')
            ?? Arr::get($candidate, 'naziv');

        return filled((string) $id) && filled(trim((string) $title));
    }

    private function mapItem(array $item, string $fallbackUrl): ?array
    {
        $externalId = (string) (
            Arr::get($item, 'id')
            ?? Arr::get($item, 'job_id')
            ?? Arr::get($item, 'reference')
            ?? Arr::get($item, 'webSifra')
            ?? ''
        );

        $title = $this->normalizeSentenceCase($this->cleanRichText($this->flattenToText(
            Arr::get($item, 'title')
            ?? Arr::get($item, 'job_title')
            ?? Arr::get($item, 'nazivRadnogMjesta')
            ?? Arr::get($item, 'naziv')
            ?? ''
        )));

        if ($externalId === '' || $title === '') {
            return null;
        }

        $descriptionRaw = $this->cleanRichText($this->flattenToText(
            Arr::get($item, 'description')
            ?? Arr::get($item, 'opis')
            ?? ''
        ));

        $requirementsRaw = $this->cleanRichText($this->flattenToText(
            Arr::get($item, 'requirements')
            ?? Arr::get($item, 'posebniZahtjevi')
            ?? Arr::get($item, 'uvjeti')
            ?? ''
        ));

        $benefitsRaw = $this->cleanRichText($this->flattenToText(
            Arr::get($item, 'benefits')
            ?? Arr::get($item, 'pogodnosti')
            ?? ''
        ));

        $nacinPrijaveRaw = $this->cleanRichText($this->flattenToText(Arr::get($item, 'nacinPrijave', '')));
        $nacinPrijaveNormalized = $this->normalizeApplicationChannelText($nacinPrijaveRaw);

        $instructionsRaw = $this->cleanRichText($this->flattenToText(
            Arr::get($item, 'application_instructions')
            ?? Arr::get($item, 'upute_prijave')
            ?? Arr::get($item, 'apply')
            ?? ''
        ));

        $instructionsCombined = trim(implode("\n", array_filter([
            $instructionsRaw,
            $nacinPrijaveNormalized,
        ], fn ($value) => trim((string) $value) !== '')));

        [$description, $requirementsText, $benefitsText, $instructionsText] = $this->deduplicateSections([
            $this->normalizeSentenceCase($descriptionRaw),
            $this->normalizeSentenceCase($requirementsRaw),
            $this->normalizeSentenceCase($benefitsRaw),
            $this->normalizeSentenceCase($instructionsCombined),
        ]);

        if ($requirementsText !== '' && $this->isMostlyDuplicate($description, $requirementsText)) {
            // For HZZ imports the responsibilities/requirements section is more useful than a duplicated generic description.
            $description = '';
        }

        if ($requirementsText === '' && preg_match('/\bod\s+vas\s+se\s+očekuje\b/iu', $description) === 1) {
            $requirementsText = $description;
            $description = '';
        }

        $sourceUrl = (string) (
            Arr::get($item, 'url')
            ?? Arr::get($item, 'apply_url')
            ?? Arr::get($item, 'link')
            ?? $fallbackUrl
        );

        $sourceLogoUrl = (string) (
            Arr::get($item, 'trackback')
            ?? Arr::get($item, 'logo')
            ?? Arr::get($item, 'logo_url')
            ?? config('services.hzz.logo_url')
            ?? ''
        );

        $externalCompanyName = $this->normalizeSentenceCase(trim($this->flattenToText(
            Arr::get($item, 'poslodavac')
            ?? Arr::get($item, 'company_name')
            ?? Arr::get($item, 'company')
            ?? ''
        )));

        $contactInput = implode("\n\n", array_filter([
            $instructionsText,
            $nacinPrijaveNormalized,
            $nacinPrijaveRaw,
            $descriptionRaw,
            $requirementsRaw,
            $benefitsRaw,
            trim((string) Arr::get($item, 'contact')),
            trim((string) Arr::get($item, 'email')),
            trim((string) Arr::get($item, 'kontakt')),
            trim((string) Arr::get($item, 'kontaktOsoba')),
            trim((string) Arr::get($item, 'telefon')),
        ], fn ($value) => trim((string) $value) !== ''));

        $parsedContact = $this->contactParser->parse($contactInput, $sourceUrl);

        if (! empty($parsedContact['email']) && $this->looksLikeSingleEmailInstruction($instructionsText)) {
            $instructionsText = (string) $parsedContact['email'];
        }

        $publishedAt = Arr::get($item, 'published_at')
            ?? Arr::get($item, 'datum_objave')
            ?? Arr::get($item, 'pocetakPrijave');

        $expiresAt = Arr::get($item, 'expires_at')
            ?? Arr::get($item, 'rok_prijave')
            ?? Arr::get($item, 'rokZaPrijavu');

        $city = $this->normalizeSentenceCase(trim($this->flattenToText(
            Arr::get($item, 'city')
            ?? Arr::get($item, 'grad')
            ?? Arr::get($item, 'mjestoRada')
            ?? 'Hrvatska'
        )));

        $category = $this->normalizeSentenceCase(trim($this->flattenToText(
            Arr::get($item, 'category')
            ?? Arr::get($item, 'kategorija')
            ?? Arr::get($item, 'kategorijaZanimanja')
            ?? 'HZZ'
        )));

        $contractType = $this->normalizeSentenceCase(trim($this->flattenToText(
            Arr::get($item, 'contract_type')
            ?? Arr::get($item, 'nacinZaposlenja')
            ?? ''
        )));

        $experienceLevel = $this->normalizeSentenceCase(trim($this->flattenToText(
            Arr::get($item, 'experience_level')
            ?? Arr::get($item, 'radnoIskustvo')
            ?? ''
        )));

        $educationRequired = $this->normalizeSentenceCase(trim($this->flattenToText(
            Arr::get($item, 'education_required')
            ?? Arr::get($item, 'razinaObrazovanja')
            ?? ''
        )));

        $workingHours = $this->normalizeSentenceCase(trim($this->flattenToText(
            Arr::get($item, 'working_hours')
            ?? Arr::get($item, 'radnoVrijeme')
            ?? ''
        )));

        $shiftDetails = $this->normalizeSentenceCase(trim($this->flattenToText(
            Arr::get($item, 'nacinRada')
            ?? Arr::get($item, 'vrstaZaposlenja')
            ?? ''
        )));

        $accommodationRaw = $this->normalizeSentenceCase(trim($this->flattenToText(
            Arr::get($item, 'smjestaj')
            ?? Arr::get($item, 'accommodation')
            ?? ''
        )));

        $transportRaw = $this->normalizeSentenceCase(trim($this->flattenToText(
            Arr::get($item, 'prijevoz')
            ?? Arr::get($item, 'transport')
            ?? ''
        )));

        $additionalConditions = $this->normalizeSentenceCase(trim($this->flattenToText(
            Arr::get($item, 'uvjeti')
            ?? ''
        )));

        if ($additionalConditions !== '' && ! $this->isMostlyDuplicate($requirementsText, $additionalConditions)) {
            $requirementsText = trim(implode("\n", array_filter([
                $requirementsText,
                $additionalConditions,
            ], fn ($value) => trim((string) $value) !== '')));
        }

        $mobilityDetails = array_values(array_filter([
            $accommodationRaw !== '' ? 'Smještaj: ' . $accommodationRaw : null,
            $transportRaw !== '' ? 'Prijevoz: ' . $transportRaw : null,
        ]));

        $accommodationDetails = implode("\n", $mobilityDetails);
        $accommodationProvided = $accommodationRaw !== '' && preg_match('/\bnema\b/iu', $accommodationRaw) !== 1;

        $positionsAvailable = (int) (
            Arr::get($item, 'positions_available')
            ?? Arr::get($item, 'trazenoRadnika')
            ?? 0
        );

        if ($positionsAvailable < 1) {
            $positionsAvailable = null;
        }

        $hzzLegalNotice = $this->normalizeSentenceCase($this->cleanRichText($this->flattenToText(
            Arr::get($item, 'legal_notice')
            ?? Arr::get($item, 'napomena')
            ?? ''
        )));

        if ($this->isMostlyDuplicate($hzzLegalNotice, $instructionsText)) {
            $hzzLegalNotice = '';
        }

        return [
            'source_system' => 'hzz',
            'hzz_is_official' => true,
            'source_reference' => $externalId,
            'source_url' => $sourceUrl,
            'source_logo_url' => $sourceLogoUrl !== '' ? $sourceLogoUrl : null,
            'source_payload' => $item,
            'source_imported_at' => now(),
            'external_company_name' => $externalCompanyName !== '' ? $externalCompanyName : null,
            'title' => $title,
            'description' => $description !== '' ? $description : 'HZZ imported listing.',
            'responsibilities' => $requirementsText !== '' ? $requirementsText : null,
            'requirements' => null,
            'benefits' => $benefitsText !== '' ? $benefitsText : null,
            'location_city' => $city,
            'category' => $category,
            'contract_type' => $contractType !== '' ? $contractType : null,
            'experience_level' => $experienceLevel !== '' ? $experienceLevel : null,
            'education_required' => $educationRequired !== '' ? $educationRequired : null,
            'working_hours' => $workingHours !== '' ? $workingHours : null,
            'shift_details' => $shiftDetails !== '' ? $shiftDetails : null,
            'positions_available' => $positionsAvailable,
            'accommodation_provided' => $accommodationProvided,
            'accommodation_details' => $accommodationDetails !== '' ? $accommodationDetails : null,
            'application_instructions' => $instructionsText !== '' ? $instructionsText : null,
            'hzz_apply_email' => $parsedContact['email'],
            'hzz_apply_contact_type' => $parsedContact['contact_type'] ?? 'unknown',
            'hzz_apply_contact_raw' => $parsedContact['contact_raw'],
            'hzz_apply_url' => $parsedContact['apply_url'],
            'hzz_apply_method_available' => (bool) ($parsedContact['has_automated_apply'] ?? false),
            'hzz_legal_notice' => $hzzLegalNotice !== '' ? $hzzLegalNotice : null,
            'status' => 'published',
            'published_at' => $this->safeDateTime($publishedAt) ?? now(),
            'expires_at' => $this->safeDateTime($expiresAt),
        ];
    }

    private function decodePayload(string $body): mixed
    {
        $trimmed = trim($body);
        if ($trimmed === '') {
            return [];
        }

        $json = json_decode($trimmed, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $json;
        }

        if (! str_starts_with($trimmed, '<')) {
            return [];
        }

        if (preg_match('/<!DOCTYPE|<!ENTITY/i', $trimmed) === 1) {
            throw new RuntimeException('HZZ XML contains unsupported DOCTYPE/ENTITY declarations.');
        }

        try {
            $previous = libxml_use_internal_errors(true);
            $xml = simplexml_load_string($trimmed, 'SimpleXMLElement', LIBXML_NONET | LIBXML_NOCDATA | LIBXML_NOWARNING | LIBXML_NOERROR);
            libxml_clear_errors();
            libxml_use_internal_errors($previous);

            if ($xml === false) {
                throw new RuntimeException('Unable to parse HZZ XML feed.');
            }

            $encoded = json_encode($xml, JSON_UNESCAPED_UNICODE);

            return is_string($encoded) ? (json_decode($encoded, true) ?? []) : [];
        } catch (\Throwable $exception) {
            throw new RuntimeException('Unable to parse HZZ XML feed.', previous: $exception);
        }
    }

    private function cleanRichText(string $value): string
    {
        $normalized = trim($value);
        if ($normalized === '') {
            return '';
        }

        $decoded = html_entity_decode($normalized, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $decoded = preg_replace('/<\s*\/?\s*p\b[^>]*>/i', "\n", $decoded) ?? $decoded;
        $decoded = preg_replace('/<\s*li\b[^>]*>/i', "\n- ", $decoded) ?? $decoded;
        $decoded = preg_replace('/<\s*\/\s*li\s*>/i', "\n", $decoded) ?? $decoded;
        $stripped = preg_replace('/<\s*br\s*\/?\s*>/i', "\n", $decoded) ?? $decoded;
        $stripped = strip_tags($stripped);
        $stripped = str_replace(["\r\n", "\r", "\t"], ["\n", "\n", ' '], $stripped);
        $stripped = preg_replace('/[ ]{2,}/', ' ', $stripped) ?? $stripped;
        $stripped = preg_replace('/\n{3,}/', "\n\n", $stripped) ?? $stripped;

        return trim((string) $stripped);
    }

    /**
     * @param array<int, string> $sections
     * @return array<int, string>
     */
    private function deduplicateSections(array $sections): array
    {
        $result = [];
        $seenItems = [];

        foreach ($sections as $candidate) {
            $candidate = trim((string) $candidate);
            if ($candidate === '') {
                $result[] = '';
                continue;
            }

            $candidate = $this->deduplicateSectionItems($candidate, $seenItems);
            if ($candidate === '') {
                $result[] = '';
                continue;
            }

            $duplicateFound = false;
            foreach ($result as $kept) {
                if ($kept === '') {
                    continue;
                }

                if ($this->isMostlyDuplicate($candidate, $kept)) {
                    $duplicateFound = true;
                    break;
                }
            }

            $result[] = $duplicateFound ? '' : $candidate;
        }

        return $result;
    }

    /**
     * @param array<int, string> $seenItems
     */
    private function deduplicateSectionItems(string $section, array &$seenItems): string
    {
        $lines = preg_split('/\R+/u', $section) ?: [];
        $kept = [];

        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }

            $isDuplicate = false;
            foreach ($seenItems as $seen) {
                if ($this->isMostlyDuplicate($line, $seen)) {
                    $isDuplicate = true;
                    break;
                }
            }

            if ($isDuplicate) {
                continue;
            }

            $seenItems[] = $line;
            $kept[] = $line;
        }

        return trim(implode("\n", $kept));
    }

    private function isMostlyDuplicate(string $left, string $right, float $threshold = 0.67): bool
    {
        $a = $this->normalizeForComparison($left);
        $b = $this->normalizeForComparison($right);

        if ($a === '' || $b === '') {
            return false;
        }

        $lenA = strlen($a);
        $lenB = strlen($b);
        $short = min($lenA, $lenB);
        $long = max($lenA, $lenB);

        if ($long === 0) {
            return false;
        }

        if (str_contains($a, $b) || str_contains($b, $a)) {
            return ($short / $long) >= $threshold;
        }

        similar_text($a, $b, $percent);

        return ($percent / 100) >= $threshold;
    }

    private function normalizeForComparison(string $value): string
    {
        $value = Str::lower($value);
        $value = preg_replace('/[^\pL\pN\s]/u', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

        return trim($value);
    }

    private function normalizeSentenceCase(string $value): string
    {
        $text = trim($value);
        if ($text === '') {
            return '';
        }

        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $lines = preg_split('/\n+/u', $text) ?: [];

        if (count($lines) > 1) {
            $normalizedLines = [];

            foreach ($lines as $line) {
                $line = trim((string) $line);
                if ($line === '') {
                    continue;
                }

                $bulletPrefix = '';
                if (str_starts_with($line, '- ')) {
                    $bulletPrefix = '- ';
                    $line = trim(substr($line, 2));
                }

                $normalizedLine = $this->normalizeSentenceCaseLine($line);
                if ($normalizedLine !== '') {
                    $normalizedLines[] = $bulletPrefix . $normalizedLine;
                }
            }

            return trim(implode("\n", $normalizedLines));
        }

        return $this->normalizeSentenceCaseLine($text);
    }

    private function normalizeSentenceCaseLine(string $value): string
    {
        $text = trim($value);
        if ($text === '') {
            return '';
        }

        [$text, $acronymTokens] = $this->protectAcronyms($text);
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        $lower = mb_strtolower($text, 'UTF-8');

        $segments = preg_split('/([.!?]+\s*)/u', $lower, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (! is_array($segments) || $segments === []) {
            return $this->ucfirstUtf8($lower);
        }

        $result = '';
        $capitalize = true;

        foreach ($segments as $segment) {
            if ($segment === '') {
                continue;
            }

            if (preg_match('/^[.!?]+\s*$/u', $segment) === 1) {
                $result .= $segment;
                $capitalize = true;
                continue;
            }

            $result .= $capitalize ? $this->ucfirstUtf8(ltrim($segment)) : $segment;
            $capitalize = false;
        }

        return $this->restoreAcronyms(trim($result), $acronymTokens);
    }

    /**
     * @return array{0: string, 1: array<string, string>}
     */
    private function protectAcronyms(string $value): array
    {
        $tokens = [];
        $protected = $value;

        foreach (self::PRESERVED_ACRONYMS as $index => $acronym) {
            $token = "__cw_acr_{$index}__";
            $pattern = '/\b' . preg_quote($acronym, '/') . '\b/u';
            $protected = preg_replace($pattern, $token, $protected) ?? $protected;
            $tokens[$token] = $acronym;
        }

        return [$protected, $tokens];
    }

    /**
     * @param array<string, string> $tokens
     */
    private function restoreAcronyms(string $value, array $tokens): string
    {
        if ($value === '' || $tokens === []) {
            return $value;
        }

        return str_ireplace(array_keys($tokens), array_values($tokens), $value);
    }

    private function normalizeApplicationChannelText(string $value): string
    {
        $normalized = trim($value);
        if ($normalized === '') {
            return '';
        }

        // HZZ usually sends "Email: user@example.com" in nacinPrijave.
        // Keep only the pure address so downstream logic and UI remain clean.
        if (preg_match('/^email\s*:\s*([A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,})$/iu', $normalized, $matches) === 1) {
            return strtolower(trim((string) $matches[1]));
        }

        $normalized = preg_replace('/\bemail\s*:\s*/iu', '', $normalized) ?? $normalized;

        return trim($normalized);
    }

    private function looksLikeSingleEmailInstruction(string $value): bool
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return false;
        }

        return preg_match('/^[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}$/iu', $trimmed) === 1;
    }

    private function flattenToText(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_string($value) || is_int($value) || is_float($value) || is_bool($value)) {
            return (string) $value;
        }

        if (! is_array($value)) {
            return '';
        }

        $parts = [];
        foreach ($value as $nested) {
            $chunk = trim($this->flattenToText($nested));
            if ($chunk !== '') {
                $parts[] = $chunk;
            }
        }

        return implode(' ', $parts);
    }

    private function ucfirstUtf8(string $value): string
    {
        if ($value === '') {
            return '';
        }

        $first = mb_substr($value, 0, 1, 'UTF-8');
        $rest = mb_substr($value, 1, null, 'UTF-8');

        return mb_strtoupper($first, 'UTF-8') . $rest;
    }

    private function safeDateTime(mixed $value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
