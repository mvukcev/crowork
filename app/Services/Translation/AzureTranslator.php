<?php

namespace App\Services\Translation;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class AzureTranslator
{
    /**
     * @param array<string, string> $content
     * @return array<string, string>
     */
    public function translate(array $content, string $from = 'hr', string $to = 'en'): array
    {
        if (
            ! config('services.azure_translator.enabled')
            || ! setting('job_translation_enabled', true)
        ) {
            throw new RuntimeException('Azure Translator is disabled.');
        }

        $key = trim((string) config('services.azure_translator.key'));
        $region = trim((string) config('services.azure_translator.region'));
        $endpoint = rtrim((string) config('services.azure_translator.endpoint'), '/');

        if ($key === '' || $region === '' || $endpoint === '') {
            throw new RuntimeException('Azure Translator credentials are incomplete.');
        }

        $translated = [];
        $chunks = $this->chunkContent($content);

        foreach ($chunks as $chunk) {
            $keys = array_keys($chunk);
            $payload = array_map(
                static fn (string $text): array => ['Text' => $text],
                array_values($chunk)
            );

            $response = Http::asJson()
                ->acceptJson()
                ->withHeaders([
                    'Ocp-Apim-Subscription-Key' => $key,
                    'Ocp-Apim-Subscription-Region' => $region,
                ])
                ->retry(3, 750, throw: false)
                ->timeout(45)
                ->post($endpoint . '/translate?' . http_build_query([
                    'api-version' => '3.0',
                    'from' => $from,
                    'to' => $to,
                    'textType' => 'html',
                ]), $payload);

            if (! $response->successful()) {
                throw new RuntimeException('Azure Translator request failed with HTTP ' . $response->status() . '.');
            }

            $items = $response->json();
            if (! is_array($items) || count($items) !== count($keys)) {
                throw new RuntimeException('Azure Translator returned an unexpected response.');
            }

            foreach ($keys as $index => $field) {
                $value = $items[$index]['translations'][0]['text'] ?? null;
                if (! is_string($value)) {
                    throw new RuntimeException('Azure Translator omitted a translated field.');
                }
                $translated[$field] = trim($value);
            }
        }

        return $translated;
    }

    /**
     * @param array<string, string> $content
     * @return array<int, array<string, string>>
     */
    private function chunkContent(array $content): array
    {
        $chunks = [];
        $chunk = [];
        $characters = 0;

        foreach ($content as $field => $text) {
            $length = mb_strlen($text, 'UTF-8');
            if ($chunk !== [] && (count($chunk) >= 90 || $characters + $length > 45000)) {
                $chunks[] = $chunk;
                $chunk = [];
                $characters = 0;
            }

            $chunk[$field] = $text;
            $characters += $length;
        }

        if ($chunk !== []) {
            $chunks[] = $chunk;
        }

        return $chunks;
    }
}
