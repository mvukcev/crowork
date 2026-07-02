<?php

namespace App\Services\Hzz;

use App\Models\Job;
use DOMComment;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class HzzJobImportService
{
    private const MAX_FEED_BYTES = 12000000;

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
        'IT',
    ];

    /**
     * Common legal suffixes that should keep their original form.
     *
     * @var array<int, string>
     */
    private const PRESERVED_COMPANY_SUFFIXES = [
        'd.o.o.',
        'j.d.o.o.',
        'd.d.',
    ];

    public function __construct(
        private readonly HzzApplicationContactParser $contactParser,
    ) {
    }

    public function importFromUrl(string $url, bool $dryRun = false, bool $allowUpdates = false, bool $deactivateMissing = false, bool $forceOverwrite = false): array
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

        $items = $this->extractXmlJobItems($body);

        if (count($items) === 0) {
            $payload = $this->decodePayload($body);
            $items = $this->normalizeItems($payload);
        }

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

                [$payload, $preservedManualEdit] = $this->mergeExistingRecord($existing, $mapped, $forceOverwrite);
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
            'force_overwrite' => $forceOverwrite,
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
            'force_overwrite' => $forceOverwrite,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function extractXmlJobItems(string $body): array
    {
        try {
            $previous = libxml_use_internal_errors(true);
            $xml = simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NONET | LIBXML_NOCDATA | LIBXML_NOWARNING | LIBXML_NOERROR);
            libxml_clear_errors();
            libxml_use_internal_errors($previous);

            if ($xml === false) {
                return [];
            }

            $nodes = [];
            foreach ($xml->radnoMjesto ?? [] as $node) {
                $nodes[] = $node;
            }

            if (count($nodes) === 0) {
                $nodes = $xml->xpath('//*[local-name()="radnoMjesto"]') ?: [];
            }

            $items = [];
            foreach ($nodes as $node) {
                $encoded = json_encode($node, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
                $decoded = is_string($encoded) ? json_decode($encoded, true) : null;

                if (is_array($decoded) && $this->looksLikeJobItem($decoded)) {
                    $items[] = $decoded;
                }
            }

            return $items;
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @return array{0: array<string, mixed>, 1: bool}
     */
    private function mergeExistingRecord(Job $existing, array $mapped, bool $forceOverwrite = false): array
    {
        if ($forceOverwrite) {
            return [$mapped, false];
        }

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
                Arr::get($payload, 'rss.channel.item', []),
                Arr::get($payload, 'channel.item', []),
            ];

            foreach ($candidates as $candidate) {
                if (is_array($candidate) && array_is_list($candidate) && count($candidate) > 0) {
                    $normalized = [];

                    foreach ($candidate as $entry) {
                        if (! is_array($entry)) {
                            continue;
                        }

                        $jobEntry = Arr::get($entry, 'radnoMjesto')
                            ?? Arr::get($entry, 'job')
                            ?? Arr::get($entry, 'item')
                            ?? $entry;

                        if (is_array($jobEntry) && $this->looksLikeJobItem($jobEntry)) {
                            $normalized[] = $jobEntry;
                        }
                    }

                    if (count($normalized) > 0) {
                        return $normalized;
                    }

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

        $title = $this->normalizeJobTitle($this->cleanRichText($this->flattenToText(
            Arr::get($item, 'title')
            ?? Arr::get($item, 'job_title')
            ?? Arr::get($item, 'nazivRadnogMjesta')
            ?? Arr::get($item, 'naziv')
            ?? ''
        )));

        if ($externalId === '' || $title === '') {
            return null;
        }

        $descriptionSource = Arr::get($item, 'description') ?? Arr::get($item, 'opis') ?? '';
        $requirementsSource = Arr::get($item, 'requirements') ?? Arr::get($item, 'posebniZahtjevi') ?? '';
        $conditionsSource = Arr::get($item, 'uvjeti') ?? '';
        $benefitsSource = Arr::get($item, 'benefits') ?? Arr::get($item, 'pogodnosti') ?? '';

        $descriptionHtml = $this->sanitizeImportedHtml(is_string($descriptionSource) ? $descriptionSource : '');
        $descriptionRaw = $this->cleanRichText($this->flattenToText($descriptionSource));
        $requirementsRaw = $this->cleanRichText($this->flattenToText($requirementsSource));
        $conditionsRaw = $this->cleanRichText($this->flattenToText($conditionsSource));
        $benefitsRaw = $this->cleanRichText($this->flattenToText($benefitsSource));

        $nacinPrijaveRaw = $this->cleanRichText($this->flattenToText(
            Arr::get($item, 'nacinPrijave')
            ?? Arr::get($item, 'nacin_prijave')
            ?? Arr::get($item, 'nacinPrijaveOpis')
            ?? Arr::get($item, 'tekstPrijave')
            ?? Arr::get($item, 'napomenaPrijava')
            ?? ''
        ));
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

        $description = $this->deduplicateLinesInBlock($description);
        $requirementsText = $this->deduplicateLinesInBlock($requirementsText);
        $benefitsText = $this->deduplicateLinesInBlock($benefitsText);
        $instructionsText = $this->deduplicateLinesInBlock($instructionsText);

        if ($conditionsRaw !== '') {
            $normalizedConditions = $this->normalizeSentenceCase($conditionsRaw);
            if (! $this->isMostlyDuplicate($requirementsText, $normalizedConditions) && ! $this->isMostlyDuplicate($description, $normalizedConditions)) {
                $requirementsText = trim(implode("\n", array_filter([
                    $requirementsText,
                    $normalizedConditions,
                ], fn ($value) => trim((string) $value) !== '')));
            }
        }

        if ($requirementsText !== '' && $this->isMostlyDuplicate($description, $requirementsText)) {
            $requirementsText = '';
        }

        $responsibilitiesText = $this->extractResponsibilities($description, $requirementsText);
        if ($responsibilitiesText !== '') {
            $description = $this->removeLinesFromBlock($description, $responsibilitiesText);
            $requirementsText = $this->removeLinesFromBlock($requirementsText, $responsibilitiesText);
            $descriptionHtml = '';
        }

        if ($description === '' && $requirementsText !== '' && $this->looksLikeDescriptiveBlock($requirementsText)) {
            $description = $requirementsText;
            $requirementsText = '';
        }

        if ($description === '' && $descriptionHtml !== '') {
            $description = $this->cleanRichText($descriptionHtml);
        }

        $sourceUrlRaw = (string) (
            Arr::get($item, 'url')
            ?? Arr::get($item, 'apply_url')
            ?? Arr::get($item, 'link')
            ?? $fallbackUrl
        );
        $sourceUrl = $this->normalizeDuplicatedUrl($sourceUrlRaw) ?? $fallbackUrl;

        $sourceLogoUrl = (string) (
            Arr::get($item, 'trackback')
            ?? Arr::get($item, 'logo')
            ?? Arr::get($item, 'logo_url')
            ?? config('services.hzz.logo_url')
            ?? ''
        );

        $externalCompanyName = $this->normalizeOrganizationName(trim($this->flattenToText(
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
            trim($this->flattenToText(Arr::get($item, 'contact'))),
            trim($this->flattenToText(Arr::get($item, 'email'))),
            trim($this->flattenToText(Arr::get($item, 'eMail'))),
            trim($this->flattenToText(Arr::get($item, 'eposta'))),
            trim($this->flattenToText(Arr::get($item, 'ePosta'))),
            trim($this->flattenToText(Arr::get($item, 'emailAdresa'))),
            trim($this->flattenToText(Arr::get($item, 'adresaEPoste'))),
            trim($this->flattenToText(Arr::get($item, 'adresa_e_poste'))),
            trim($this->flattenToText(Arr::get($item, 'kontakt'))),
            trim($this->flattenToText(Arr::get($item, 'kontaktOsoba'))),
            trim($this->flattenToText(Arr::get($item, 'kontakt_osoba'))),
            trim($this->flattenToText(Arr::get($item, 'kontaktEmail'))),
            trim($this->flattenToText(Arr::get($item, 'kontakt_email'))),
            trim($this->flattenToText(Arr::get($item, 'emailPoslodavca'))),
            trim($this->flattenToText(Arr::get($item, 'email_poslodavca'))),
            trim($this->flattenToText(Arr::get($item, 'mailPoslodavca'))),
            trim($this->flattenToText(Arr::get($item, 'mail_poslodavca'))),
            trim($this->flattenToText(Arr::get($item, 'nacin_prijave'))),
            trim($this->flattenToText(Arr::get($item, 'nacinPrijaveOpis'))),
            trim($this->flattenToText(Arr::get($item, 'tekstPrijave'))),
            trim($this->flattenToText(Arr::get($item, 'napomenaPrijava'))),
            trim($this->flattenToText(Arr::get($item, 'telefon'))),
            $this->flattenToText($item),
        ], fn ($value) => trim((string) $value) !== ''));

        $parsedContact = $this->contactParser->parse($contactInput, $sourceUrl);
        $parsedApplyUrl = $this->normalizeDuplicatedUrl((string) ($parsedContact['apply_url'] ?? ''));

        if ($instructionsText !== '' && $this->looksLikeSingleEmailInstruction($instructionsText)) {
            $instructionsText = '';
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

        $sourceReference = $this->limitString($externalId, 190);
        $externalCompanyName = $this->limitString($externalCompanyName !== '' ? $externalCompanyName : null, 255);
        $title = $this->limitString($title, 255) ?? $title;
        $city = $this->limitString($city, 255) ?? $city;
        $category = $this->limitString($category, 255) ?? $category;
        $contractType = $this->limitString($contractType !== '' ? $contractType : null, 255);
        $experienceLevel = $this->limitString($experienceLevel !== '' ? $experienceLevel : null, 80);
        $educationRequired = $this->limitString($educationRequired !== '' ? $educationRequired : null, 120);
        $workingHours = $this->limitString($workingHours !== '' ? $workingHours : null, 120);

        $applyEmail = $this->limitString($parsedContact['email'] ?? null, 190);
        $applyContactType = $this->limitString((string) ($parsedContact['contact_type'] ?? 'unknown'), 40) ?? 'unknown';
        $applyContactRaw = $this->limitString($parsedContact['contact_raw'] ?? null, 64000);

        if ($instructionsText !== '') {
            $instructionsText = $this->limitString($instructionsText, 64000) ?? '';
        }

        return [
            'source_system' => 'hzz',
            'hzz_is_official' => true,
            'source_reference' => $sourceReference,
            'source_url' => $sourceUrl,
            'source_logo_url' => $sourceLogoUrl !== '' ? $sourceLogoUrl : null,
            'source_payload' => $item,
            'source_imported_at' => now(),
            'external_company_name' => $externalCompanyName,
            'title' => $title,
            'description' => $descriptionHtml !== '' && $description !== '' ? $descriptionHtml : ($description !== '' ? $description : null),
            'responsibilities' => $responsibilitiesText !== '' ? $responsibilitiesText : null,
            'requirements' => $requirementsText !== '' ? $requirementsText : null,
            'benefits' => $benefitsText !== '' ? $benefitsText : null,
            'location_city' => $city,
            'category' => $category,
            'contract_type' => $contractType,
            'experience_level' => $experienceLevel,
            'education_required' => $educationRequired,
            'working_hours' => $workingHours,
            'shift_details' => $shiftDetails !== '' ? $shiftDetails : null,
            'positions_available' => $positionsAvailable,
            'accommodation_provided' => $accommodationProvided,
            'accommodation_details' => $accommodationDetails !== '' ? $accommodationDetails : null,
            'application_instructions' => $instructionsText !== '' ? $instructionsText : null,
            'hzz_apply_email' => $applyEmail,
            'hzz_apply_contact_type' => $applyContactType,
            'hzz_apply_contact_raw' => $applyContactRaw,
            'hzz_apply_url' => $parsedApplyUrl,
            'hzz_apply_method_available' => (bool) ($parsedContact['has_automated_apply'] ?? false) || ! empty($applyEmail),
            'hzz_legal_notice' => $hzzLegalNotice !== '' ? $hzzLegalNotice : null,
            'status' => 'published',
            'published_at' => $this->safeDateTime($publishedAt) ?? now(),
            'expires_at' => $this->safeDateTime($expiresAt),
        ];
    }

    private function limitString(?string $value, int $maxLength): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (mb_strlen($value, 'UTF-8') <= $maxLength) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, $maxLength, 'UTF-8'));
    }

    private function normalizeDuplicatedUrl(?string $value): ?string
    {
        $url = trim((string) $value);
        if ($url === '') {
            return null;
        }

        // Some feeds contain a duplicated URL concatenated directly into one string.
        $lower = strtolower($url);
        $nextHttp = strpos($lower, 'http://', 7);
        $nextHttps = strpos($lower, 'https://', 8);

        $splitAt = false;
        if ($nextHttp !== false && $nextHttps !== false) {
            $splitAt = min($nextHttp, $nextHttps);
        } elseif ($nextHttp !== false) {
            $splitAt = $nextHttp;
        } elseif ($nextHttps !== false) {
            $splitAt = $nextHttps;
        }

        if ($splitAt !== false) {
            $url = substr($url, 0, $splitAt);
        }

        $url = trim((string) preg_split('/[\s;,]+/', $url)[0]);

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        return $url;
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

            $fallbackItems = [];

            $nodes = [];
            foreach ($xml->radnoMjesto ?? [] as $node) {
                $nodes[] = $node;
            }

            if (count($nodes) === 0) {
                $nodes = $xml->xpath('//*[local-name()="radnoMjesto"]') ?: [];
            }

            foreach ($nodes as $node) {
                $encodedNode = json_encode($node, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
                $decodedNode = is_string($encodedNode) ? json_decode($encodedNode, true) : null;
                if (is_array($decodedNode) && $this->looksLikeJobItem($decodedNode)) {
                    $fallbackItems[] = $decodedNode;
                }
            }

            return ['radnoMjesto' => $fallbackItems];
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

        $decoded = $normalized;
        // Some HZZ feeds contain double-encoded entities (e.g. &amp;NBSP;).
        // Decode a few rounds so frontend never shows entity literals.
        for ($i = 0; $i < 3; $i++) {
            $next = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($next === $decoded) {
                break;
            }

            $decoded = $next;
        }

        $decoded = preg_replace('/&(nbsp|NBSP|#160|#xA0);?/u', ' ', $decoded) ?? $decoded;
        $decoded = preg_replace('/&(amp|AMP);?/u', '&', $decoded) ?? $decoded;
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

    private function deduplicateLinesInBlock(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        $lines = preg_split('/\R+/u', $value) ?: [];
        $kept = [];

        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }

            $duplicate = false;
            foreach ($kept as $existingLine) {
                if ($this->isMostlyDuplicate($line, $existingLine, 0.86)) {
                    $duplicate = true;
                    break;
                }
            }

            if (! $duplicate) {
                $kept[] = $line;
            }
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

    private function normalizeJobTitle(string $value): string
    {
        $title = $this->normalizeSentenceCase($value);
        $title = preg_replace('/\s*-\s*/u', ' – ', $title) ?? $title;

        return trim((string) $title);
    }

    private function normalizeOrganizationName(string $value): string
    {
        $text = trim($value);
        if ($text === '') {
            return '';
        }

        [$text, $tokens] = $this->protectAcronyms($text);
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        $text = mb_convert_case(mb_strtolower($text, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');

        return trim($this->restoreAcronyms($text, $tokens));
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

        preg_match_all('/\b(?:https?:\/\/|www\.)\S+\b/iu', $protected, $urlMatches);
        foreach ($urlMatches[0] ?? [] as $index => $match) {
            $token = "__cw_url_{$index}__";
            $protected = str_replace($match, $token, $protected);
            $tokens[$token] = $match;
        }

        preg_match_all('/\b[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}\b/iu', $protected, $emailMatches);
        foreach ($emailMatches[0] ?? [] as $index => $match) {
            $token = "__cw_mail_{$index}__";
            $protected = str_replace($match, $token, $protected);
            $tokens[$token] = $match;
        }

        foreach (self::PRESERVED_COMPANY_SUFFIXES as $index => $suffix) {
            $token = "__cw_suffix_{$index}__";
            $protected = preg_replace('/\b' . preg_quote($suffix, '/') . '\b/iu', $token, $protected) ?? $protected;
            $tokens[$token] = $suffix;
        }

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

        if (preg_match('/\bemail\s*:\s*([A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,})/iu', $normalized, $matches) === 1) {
            return strtolower(trim((string) $matches[1]));
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

    private function looksLikeDescriptiveBlock(string $value): bool
    {
        $normalized = trim($this->normalizeForComparison($value));
        if ($normalized === '') {
            return false;
        }

        $wordCount = count(array_filter(explode(' ', $normalized)));

        return $wordCount >= 8;
    }

    private function extractResponsibilities(string ...$sources): string
    {
        $items = [];

        foreach ($sources as $source) {
            $lines = preg_split('/\R+/u', trim($source)) ?: [];

            foreach ($lines as $line) {
                $line = trim((string) $line);
                if ($line === '') {
                    continue;
                }

                if (preg_match('/^[-*]\s*(.+)$/u', $line, $matches) === 1) {
                    $candidate = $this->normalizeSentenceCase(trim((string) $matches[1]));
                    if ($candidate !== '') {
                        $items[] = $candidate;
                    }
                    continue;
                }

                if (preg_match('/^(opis\s+posla|odgovornosti|zadaci|radni\s+zadaci)\s*:\s*(.+)$/iu', $line, $matches) === 1) {
                    $chunks = preg_split('/\s*[;•]\s*/u', trim((string) $matches[2])) ?: [];
                    foreach ($chunks as $chunk) {
                        $candidate = $this->normalizeSentenceCase(trim((string) $chunk));
                        if ($candidate !== '') {
                            $items[] = $candidate;
                        }
                    }
                }
            }
        }

        $unique = [];
        foreach ($items as $item) {
            if (mb_strlen($item, 'UTF-8') < 4) {
                continue;
            }

            $duplicate = false;
            foreach ($unique as $existing) {
                if ($this->isMostlyDuplicate($item, $existing, 0.86)) {
                    $duplicate = true;
                    break;
                }
            }

            if (! $duplicate) {
                $unique[] = $item;
            }
        }

        $unique = array_slice($unique, 0, 8);

        if (count($unique) < 2) {
            return '';
        }

        return implode("\n", array_map(static fn (string $item): string => '- ' . $item, $unique));
    }

    private function removeLinesFromBlock(string $source, string $linesToRemove): string
    {
        $sourceLines = preg_split('/\R+/u', trim($source)) ?: [];
        $removals = preg_split('/\R+/u', trim($linesToRemove)) ?: [];
        $normalizedRemovals = array_values(array_filter(array_map(function (string $line): string {
            return $this->normalizeForComparison(preg_replace('/^[-*]\s*/u', '', trim($line)) ?? trim($line));
        }, $removals)));

        $kept = [];
        foreach ($sourceLines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }

            $normalizedLine = $this->normalizeForComparison(preg_replace('/^[-*]\s*/u', '', $line) ?? $line);
            if ($normalizedLine === '') {
                continue;
            }

            $shouldRemove = false;
            foreach ($normalizedRemovals as $removal) {
                if ($removal !== '' && $this->isMostlyDuplicate($normalizedLine, $removal, 0.86)) {
                    $shouldRemove = true;
                    break;
                }
            }

            if (! $shouldRemove) {
                $kept[] = $line;
            }
        }

        return trim(implode("\n", $kept));
    }

    private function sanitizeImportedHtml(string $value): string
    {
        $html = trim($value);
        if ($html === '' || ! preg_match('/<[^>]+>/', $html)) {
            return '';
        }

        $previous = libxml_use_internal_errors(true);

        try {
            $document = new DOMDocument('1.0', 'UTF-8');
            $wrappedHtml = '<div>' . $html . '</div>';
            $loaded = $document->loadHTML('<?xml encoding="utf-8" ?>' . $wrappedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR | LIBXML_NOWARNING);

            if (! $loaded) {
                return '';
            }

            $root = $document->getElementsByTagName('div')->item(0);
            if (! $root instanceof DOMElement) {
                return '';
            }

            $previousHeading = null;
            $this->sanitizeHtmlNode($root, $previousHeading);

            $output = '';
            foreach (iterator_to_array($root->childNodes) as $child) {
                $output .= $document->saveHTML($child);
            }

            $output = preg_replace('/<(p|h3|h4|li)>\s*<\/\1>/i', '', $output) ?? $output;

            return trim((string) $output);
        } catch (\Throwable) {
            return '';
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    private function sanitizeHtmlNode(DOMNode $node, ?string &$previousHeading): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMComment) {
                $node->removeChild($child);
                continue;
            }

            if ($child instanceof DOMText) {
                $child->nodeValue = $this->normalizeHtmlTextNode($child->wholeText);
                continue;
            }

            if (! $child instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);
            if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'form'], true)) {
                $node->removeChild($child);
                continue;
            }

            if (! in_array($tag, ['p', 'br', 'strong', 'b', 'em', 'i', 'ul', 'ol', 'li', 'h3', 'h4'], true)) {
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }

            while ($child->attributes->length > 0) {
                $child->removeAttributeNode($child->attributes->item(0));
            }

            $this->sanitizeHtmlNode($child, $previousHeading);

            $textContent = trim((string) $child->textContent);
            if (in_array($tag, ['h3', 'h4'], true)) {
                $heading = $this->normalizeSentenceCase($textContent);
                if ($heading === '' || ($previousHeading !== null && $this->isMostlyDuplicate($heading, $previousHeading, 0.9))) {
                    $node->removeChild($child);
                    continue;
                }

                $child->nodeValue = $heading;
                $previousHeading = $heading;
            }

            if ($tag !== 'br' && $textContent === '' && ! $child->hasChildNodes()) {
                $node->removeChild($child);
            }
        }
    }

    private function normalizeHtmlTextNode(string $value): string
    {
        $text = preg_replace('/\s+/u', ' ', $value) ?? $value;
        if (trim($text) === '') {
            return $text;
        }

        $hasLower = preg_match('/\p{Ll}/u', $text) === 1;
        $letterCount = preg_match_all('/\p{L}/u', $text, $letters) ?: 0;
        $upperCount = preg_match_all('/\p{Lu}/u', $text, $uppers) ?: 0;

        if (! $hasLower && $letterCount > 0 && ($upperCount / $letterCount) > 0.45) {
            return $this->normalizeSentenceCase($text);
        }

        return $text;
    }
}
