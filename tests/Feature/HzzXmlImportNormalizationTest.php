<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Services\Hzz\HzzJobImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HzzXmlImportNormalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_hzz_xml_import_deduplicates_text_extracts_email_and_normalizes_case(): void
    {
        $xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<radnaMjesta>
  <radnoMjesto>
    <id>164366408</id>
    <url>http://burzarada.hzz.hr/RadnoMjesto_Ispis.aspx?WebSifra=164366408</url>
    <nazivRadnogMjesta>PRODAVAČ/PRODAVAČICA - KONOBAR/KONOBARICA NA BENZINSKOJ POSTAJI</nazivRadnogMjesta>
    <opis>OD VAS SE OČEKUJE: AKTIVNA ULOGA U PRODAJI PROIZVODA IZ NAŠEG BOGATOG ASORTIMANA.</opis>
    <kategorija>HZZ EU PROGRAMI</kategorija>
    <mjestoRada>VARAŽDINSKE TOPLICE</mjestoRada>
    <rokZaPrijavu>06.07.2026</rokZaPrijavu>
    <trazenoRadnika>1</trazenoRadnika>
    <razinaObrazovanja>SREDNJA ŠKOLA 3 GODINE;SREDNJA ŠKOLA 4 GODINE</razinaObrazovanja>
    <radnoIskustvo>NIJE POTREBNO</radnoIskustvo>
    <nacinZaposlenja>NA ODREĐENO</nacinZaposlenja>
    <radnoVrijeme>PUNO RADNO VRIJEME</radnoVrijeme>
    <nacinPrijave>Email: pridruzise@ina.hr</nacinPrijave>
    <posebniZahtjevi>Od vas se očekuje: aktivna uloga u prodaji proizvoda iz našeg bogatog asortimana.</posebniZahtjevi>
  </radnoMjesto>
</radnaMjesta>
XML;

        Http::fake([
            'https://example.test/hzz.xml' => Http::response($xml, 200, ['Content-Type' => 'application/xml']),
        ]);

        $summary = app(HzzJobImportService::class)->importFromUrl('https://example.test/hzz.xml', false, false);

        $this->assertSame(1, $summary['created']);

        $job = Job::query()->firstOrFail();

        $this->assertSame('164366408', (string) $job->source_reference);
        $this->assertSame('pridruzise@ina.hr', $job->hzz_apply_email);
        $this->assertTrue($job->canApplyViaCroWork());

        $this->assertSame(
            'Prodavač/prodavačica - konobar/konobarica na benzinskoj postaji',
            $job->title
        );

        $this->assertSame('Varaždinske toplice', $job->location_city);
        $this->assertSame('HZZ EU programi', $job->category);

        // Duplicate text from posebniZahtjevi should be removed when mostly equal to opis.
        $this->assertNull($job->responsibilities);

        // nacinPrijave should be normalized to pure email (without "Email:" prefix)
        $this->assertSame('pridruzise@ina.hr', $job->application_instructions);

        $this->assertSame('Na određeno', $job->contract_type);
        $this->assertSame('Nije potrebno', $job->experience_level);
        $this->assertSame('Puno radno vrijeme', $job->working_hours);

        // Preserve known acronyms in uppercase after sentence-case conversion.
        $this->assertStringContainsString('HZZ EU', $job->category);
    }
}
