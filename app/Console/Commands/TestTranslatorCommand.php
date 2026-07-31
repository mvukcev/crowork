<?php

namespace App\Console\Commands;

use App\Services\Translation\AzureTranslator;
use Illuminate\Console\Command;
use Throwable;

class TestTranslatorCommand extends Command
{
    protected $signature = 'crowork:translator-test';

    protected $description = 'Verify the configured Azure Translator connection without exposing credentials.';

    public function handle(AzureTranslator $translator): int
    {
        try {
            $result = $translator->translate(['message' => 'Ovo je test prijevoda.']);
        } catch (Throwable $exception) {
            $this->error('Translator test failed: ' . $exception->getMessage());
            return self::FAILURE;
        }

        $this->info('Azure Translator connection is working.');
        $this->line('Result: ' . ($result['message'] ?? ''));

        return self::SUCCESS;
    }
}
