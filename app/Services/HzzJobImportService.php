<?php

namespace App\Services;

use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HzzJobImportService
{
    public function import(bool $deactivateMissing = false): array
    {
        $feedUrl = (string) config('services.hzz.feed_url');

        if ($feedUrl === '') {
            throw new \RuntimeException('HZZ feed URL is not configured.');
        }

        $response = Http::timeout(45)
            ->accept('application/xml, text/xml;q=0.9, */*;q=0.8')
            ->get($feedUrl);

        if (! $response->successful()) {
            throw new \RuntimeException('HZZ feed request failed with HTTP '.$response->status().'.');
        }

        $xmlRaw = $response->body();

        $xml = @simplexml_load_string($xmlRaw, 'SimpleXMLElement', LIBXML_NOCDATA);

        if (! $xml) {
            throw new \RuntimeException('Unable to parse HZZ XML feed.');
        }

        $items = $xml->radnoMjesto ?? [];

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $seenExternalIds = [];
        $sourceType = 'hzz';
        $importedAt = now();

        foreach ($items as $item) {
            $externalId = trim((string) ($item->id ?? ''));

            if ($externalId === '') {
                $skipped++;
                continue;
            }

            $seenExternalIds[] = $externalId;

            $title = $this->plainText((string) ($item->nazivRadnogMjesta ?? ''));
            $occupation = $this->plainText((string) ($item->zanimanje ?? ''));
            $description = $this->safeHtml($this->mergeLines([
                (string) ($item->opis ?? ''),
                (string) ($item->posebniZahtjevi ?? ''),
            ]));
            $category = $this->plainText((string) ($item->kategorija ?? ''));
            $city = $this->plainText((string) ($item->mjestoRada ?? ''));
            $county = $this->plainText((string) ($item->zupanija ?? ''));
            $applicationDeadline = $this->parseCroatianDate((string) ($item->rokZaPrijavu ?? ''));
            $startDate = $this->parseCroatianDate((string) ($item->pocetakPrijave ?? ''));
            $workersNeeded = (int) preg_replace('/\D+/', '', (string) ($item->trazenoRadnika ?? ''));
            $education = $this->plainText((string) ($item->razinaObrazovanja ?? ''));
            $experience = $this->plainText((string) ($item->radnoIskustvo ?? ''));
            $employmentMode = $this->plainText((string) ($item->nacinZaposlenja ?? ''));
            $workingHours = $this->plainText((string) ($item->radnoVrijeme ?? ''));
            $employer = $this->plainText((string) ($item->poslodavac ?? ''));
            $applyMethod = $this->plainText((string) ($item->nacinPrijave ?? ''));
            $contactPerson = $this->plainText((string) ($item->kontaktOsoba ?? ''));
            $phone = $this->plainText((string) ($item->telefon ?? ''));
            $accommodation = $this->plainText((string) ($item->smjestaj ?? ''));
            $transport = $this->plainText((string) ($item->prijevoz ?? ''));
            $food = $this->plainText((string) ($item->prehrana ?? ''));
            $conditions = $this->plainText((string) ($item->uvjeti ?? ''));
            $sourceUrl = trim((string) ($item->url ?? ''));

            $record = Job::query()->firstOrNew([
                'source_type' => $sourceType,
                'source_external_id' => $externalId,
            ]);

            $wasExisting = $record->exists;

            $record->fill([
                'employer_id' => null,
                'created_by_user_id' => null,
                'source_type' => $sourceType,
                'source_external_id' => $externalId,
                'source_url' => $sourceUrl !== '' ? $sourceUrl : null,
                'source_logo_url' => (string) config('services.hzz.logo_url'),
                'source_imported_at' => $importedAt,
                'external_company_name' => $this->limit($employer, 255),
                'title' => $this->limit($title !== '' ? $title : ($occupation !== '' ? $occupation : 'HZZ oglas #'.$externalId), 255),
                'slug' => 'hzz-'.$externalId,
                'description' => $description !== '' ? $description : $this->safeHtml('Detalji oglasa dostupni su na izvornom HZZ oglasu.'),
                'responsibilities' => $this->mergeLines([$occupation]),
                'requirements' => $this->limit($this->mergeLines([
                    $education !== '' ? 'Razina obrazovanja: '.$education : null,
                    $experience !== '' ? 'Radno iskustvo: '.$experience : null,
                    $conditions !== '' ? 'Uvjeti: '.$conditions : null,
                ]), 65000),
                'benefits' => $this->limit($this->mergeLines([
                    $accommodation !== '' ? 'Smještaj: '.$accommodation : null,
                    $transport !== '' ? 'Prijevoz: '.$transport : null,
                    $food !== '' ? 'Prehrana: '.$food : null,
                ]), 65000),
                'location_city' => $this->limit($city !== '' ? $city : ($county !== '' ? $county : 'Hrvatska'), 255),
                'category' => $this->limit($category !== '' ? $category : 'Ostalo', 255),
                'languages' => null,
                'accommodation_provided' => Str::contains(Str::lower($accommodation), ['osiguran', 'zajednički', 'samac', 'da'])
                    && ! Str::contains(Str::lower($accommodation), ['nema']),
                'accommodation_details' => $this->limit($accommodation, 65000),
                'visa_support' => false,
                'visa_support_details' => null,
                'contract_type' => $this->mapContractType($employmentMode, $workingHours),
                'experience_level' => $this->mapExperienceLevel($experience),
                'education_required' => $this->limit($education, 120),
                'contract_duration' => $this->limit($employmentMode, 120),
                'start_date' => $startDate,
                'start_flexibility' => null,
                'positions_available' => $workersNeeded > 0 ? $workersNeeded : 1,
                'working_hours' => $this->limit($workingHours, 120),
                'shift_details' => null,
                'application_instructions' => $this->limit($this->mergeLines([
                    $applyMethod !== '' ? 'Način prijave: '.$applyMethod : null,
                    $contactPerson !== '' ? 'Kontakt osoba: '.$contactPerson : null,
                    $phone !== '' ? 'Telefon: '.$phone : null,
                    $sourceUrl !== '' ? 'Izvorni oglas: '.$sourceUrl : null,
                ]), 65000),
                'is_featured' => false,
                'is_urgent' => false,
                'expires_at' => $applicationDeadline,
                'status' => 'published',
                'published_at' => $record->published_at ?? $importedAt,
            ]);

            $record->save();

            if ($wasExisting) {
                $updated++;
            } else {
                $created++;
            }
        }

        $deactivated = 0;

        if ($deactivateMissing && $seenExternalIds !== []) {
            $deactivated = Job::query()
                ->where('source_type', $sourceType)
                ->whereNotIn('source_external_id', $seenExternalIds)
                ->whereIn('status', ['published', 'pending'])
                ->update([
                    'status' => 'delisted',
                    'source_imported_at' => $importedAt,
                ]);
        }

        Log::info('HZZ import completed.', [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'deactivated' => $deactivated,
            'total_items' => is_countable($items) ? count($items) : null,
        ]);

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'deactivated' => $deactivated,
        ];
    }

    protected function parseCroatianDate(string $value): ?Carbon
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            return null;
        }

        try {
            return Carbon::createFromFormat('d.m.Y', $trimmed)->endOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    protected function mapContractType(string $employmentMode, string $workingHours): ?string
    {
        $mode = Str::lower($employmentMode);
        $hours = Str::lower($workingHours);

        if (Str::contains($mode, ['sezonski'])) {
            return 'seasonal';
        }

        if (Str::contains($mode, ['određeno', 'odredeno'])) {
            return 'temporary';
        }

        if (Str::contains($mode, ['ugovor'])) {
            return 'contract';
        }

        if (Str::contains($mode, ['praksa', 'pripravnik', 'naukovanje'])) {
            return 'internship';
        }

        if (Str::contains($hours, ['nepuno', 'skraćeno', 'skraceno'])) {
            return 'part-time';
        }

        if (Str::contains($hours, ['puno'])) {
            return 'full-time';
        }

        return 'full-time';
    }

    protected function mapExperienceLevel(string $experience): string
    {
        $value = Str::lower($experience);

        if ($value === '' || Str::contains($value, ['nije potrebno', 'bez iskustva'])) {
            return 'entry';
        }

        if (preg_match('/(\d+)/', $value, $matches) === 1) {
            $months = (int) $matches[1];

            if ($months >= 36) {
                return 'senior';
            }

            if ($months >= 12) {
                return 'mid';
            }

            return 'junior';
        }

        return 'junior';
    }

    protected function safeHtml(string $input): string
    {
        $plain = $this->plainText($input);
        $plain = preg_replace("/\R{3,}/", "\n\n", $plain ?? '') ?? '';

        if (trim($plain) === '') {
            return '';
        }

        return nl2br(e($plain));
    }

    protected function plainText(string $value): string
    {
        $decoded = trim($value);

        for ($i = 0; $i < 2; $i++) {
            $decoded = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        $decoded = strip_tags($decoded);
        $decoded = preg_replace('/\x{00A0}/u', ' ', $decoded) ?? $decoded;
        $decoded = preg_replace('/[ \t]+/', ' ', $decoded) ?? $decoded;
        $decoded = preg_replace('/\s*\n\s*/', "\n", $decoded) ?? $decoded;

        return trim($decoded);
    }

    protected function mergeLines(array $parts): string
    {
        return trim(collect($parts)
            ->filter(fn ($part) => is_string($part) && trim($part) !== '')
            ->map(fn ($part) => trim((string) $part))
            ->implode("\n\n"));
    }

    protected function limit(string $value, int $max): ?string
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            return null;
        }

        return Str::limit($trimmed, $max, '');
    }
}
