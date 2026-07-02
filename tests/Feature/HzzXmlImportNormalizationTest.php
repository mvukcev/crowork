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
          'Prodavač/prodavačica – konobar/konobarica na benzinskoj postaji',
          $job->title
        );

        $this->assertSame('Varaždinske toplice', $job->location_city);
        $this->assertSame('HZZ EU programi', $job->category);

        // Duplicate text from opis/posebniZahtjevi should be kept once, without placeholder fallback.
        $this->assertSame(
            'Od vas se očekuje: aktivna uloga u prodaji proizvoda iz našeg bogatog asortimana.',
            $job->description
        );
        $this->assertNull($job->responsibilities);
        $this->assertNull($job->requirements);

        // Email-only instructions should become contact data, not a visible instruction block.
        $this->assertNull($job->application_instructions);

        $this->assertSame('Na određeno', $job->contract_type);
        $this->assertSame('Nije potrebno', $job->experience_level);
        $this->assertSame('Puno radno vrijeme', $job->working_hours);

        // Preserve known acronyms in uppercase after sentence-case conversion.
        $this->assertStringContainsString('HZZ EU', $job->category);
    }

    public function test_hzz_xml_import_keeps_bullet_list_content_in_description_when_it_comes_from_opis(): void
    {
        $xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<radnaMjesta>
  <radnoMjesto>
    <id>164366409</id>
    <url>http://burzarada.hzz.hr/RadnoMjesto_Ispis.aspx?WebSifra=164366409</url>
    <nazivRadnogMjesta>KUHAR/ICA</nazivRadnogMjesta>
    <opis><![CDATA[
      <p>RAD NA PRIPREMI I POSLUŽIVANJU HRANE.</p>
      <ul>
        <li>PRIPREMA JELA</li>
        <li>ODRŽAVANJE ČISTOĆE KUHINJE</li>
        <li>SUDJELOVANJE U NARUDŽBAMA</li>
      </ul>
    ]]></opis>
    <mjestoRada>ZAGREB</mjestoRada>
  </radnoMjesto>
</radnaMjesta>
XML;

        Http::fake([
            'https://example.test/hzz-bullets.xml' => Http::response($xml, 200, ['Content-Type' => 'application/xml']),
        ]);

        $summary = app(HzzJobImportService::class)->importFromUrl('https://example.test/hzz-bullets.xml', false, false);

        $this->assertSame(1, $summary['created']);

        $job = Job::query()->firstOrFail();

        $this->assertStringContainsString('Rad na pripremi i posluživanju hrane.', (string) strip_tags((string) $job->description));
        $this->assertStringContainsString('Priprema jela', (string) strip_tags((string) $job->description));
        $this->assertNull($job->responsibilities);
    }

    public function test_hzz_xml_import_keeps_non_null_description_when_source_is_only_bullets(): void
    {
        $xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<radnaMjesta>
  <radnoMjesto>
    <id>164251816</id>
    <url>http://burzarada.hzz.hr/RadnoMjesto_Ispis.aspx?WebSifra=164251816</url>
    <nazivRadnogMjesta>DJELATNIK/DJELATNICA U KUŠAONICI VINA</nazivRadnogMjesta>
    <opis><![CDATA[
      <P>- PRIMARNI RAD U KUŠAONICI VINARIJE, NA POSLOVIMA PREZENTACIJE, POSLUŽIVANJA I PRODAJE&amp;NBSP;VINA.</P>
      <P>- UVJET JE&amp;NBSP;DOBRO POZNAVANJE ENGLESKOG JEZIKA I&amp;NBSP;DOBRE KOMUNIKACIJSKE VJEŠTINE.</P>
      <P>- UVJET NIJE FORMALNO OBRAZOVANJE.</P>
      <P>- JEDAN DAN U TJEDNU NERADNI.</P>
      <P>- RADNO VRIJEME OD 12-20 H (5 DANA PO 6H I 1 DAN 8H, ILI SLIČNO, OVISNO O DOGOVORU.)</P>
      <P>- MOGUĆE I POVREMENI ANGAŽMAN</P>
    ]]></opis>
    <mjestoRada>SKRADIN</mjestoRada>
    <nacinPrijave>Email: info@antesladicvino.hr</nacinPrijave>
    <uvjeti>Teren:Oboje</uvjeti>
    <posebniZahtjevi><![CDATA[
      Opis poslova:<br />
      - Primarni rad u kušaonici vinarije, na poslovima prezentacije, posluživanja i prodaje&amp;nbsp;vina.<br />
      - Uvjet je&amp;nbsp;dobro poznavanje engleskog jezika i&amp;nbsp;dobre komunikacijske vještine.<br />
      - Uvjet nije formalno obrazovanje.<br />
      - Jedan dan u tjednu neradni.<br />
      - Radno vrijeme od 12-20 h (5 dana po 6h i 1 dan 8h, ili slično, ovisno o dogovoru.)<br />
      - Moguće i povremeni angažman.
    ]]></posebniZahtjevi>
  </radnoMjesto>
</radnaMjesta>
XML;

        Http::fake([
            'https://example.test/hzz-bullet-only.xml' => Http::response($xml, 200, ['Content-Type' => 'application/xml']),
        ]);

        $summary = app(HzzJobImportService::class)->importFromUrl('https://example.test/hzz-bullet-only.xml', false, false);

        $this->assertSame(1, $summary['created']);

        $job = Job::query()->firstOrFail();

        $this->assertNotSame('', (string) $job->description);
        $this->assertStringContainsString('primarni rad u kušaonici vinarije', mb_strtolower((string) strip_tags((string) $job->description), 'UTF-8'));
        $this->assertNull($job->responsibilities);
        $this->assertStringContainsString('Teren', (string) $job->requirements);
    }

      public function test_hzz_xml_import_handles_empty_xml_without_creating_records(): void
      {
        Http::fake([
          'https://example.test/hzz-empty.xml' => Http::response("<?xml version=\"1.0\" encoding=\"UTF-8\"?><radnaMjesta></radnaMjesta>", 200),
        ]);

        $summary = app(HzzJobImportService::class)->importFromUrl('https://example.test/hzz-empty.xml', false, false);

        $this->assertSame(0, $summary['total_items']);
        $this->assertSame(0, $summary['created']);
        $this->assertDatabaseCount('job_postings', 0);
      }

      public function test_hzz_xml_import_rejects_malformed_xml(): void
      {
        Http::fake([
          'https://example.test/hzz-bad.xml' => Http::response('<radnaMjesta><radnoMjesto>', 200),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to parse HZZ XML feed.');

        app(HzzJobImportService::class)->importFromUrl('https://example.test/hzz-bad.xml', false, false);
      }

      public function test_hzz_xml_import_strips_non_hzz_apply_url_and_keeps_email_empty_when_missing(): void
      {
        $xml = <<<'XML'
    <?xml version="1.0" encoding="UTF-8"?>
    <radnaMjesta>
      <radnoMjesto>
      <id>2001</id>
      <url>https://evil.example/phish</url>
      <nazivRadnogMjesta>MEDICINSKA SESTRA</nazivRadnogMjesta>
      <opis>Prijava preko vanjskog obrasca.</opis>
      <mjestoRada>ZAGREB</mjestoRada>
      </radnoMjesto>
    </radnaMjesta>
    XML;

        Http::fake([
          'https://example.test/hzz-unsafe.xml' => Http::response($xml, 200, ['Content-Type' => 'application/xml']),
        ]);

        $summary = app(HzzJobImportService::class)->importFromUrl('https://example.test/hzz-unsafe.xml', false, false);

        $this->assertSame(1, $summary['created']);

        $job = Job::query()->firstOrFail();
        $this->assertNull($job->hzz_apply_email);
        $this->assertNull($job->hzz_apply_url);
        $this->assertSame('unknown', $job->hzz_apply_contact_type);
      }

      public function test_hzz_xml_import_extracts_email_from_alternate_contact_fields_and_mailto(): void
      {
        $xml = <<<'XML'
    <?xml version="1.0" encoding="UTF-8"?>
    <radnaMjesta>
      <radnoMjesto>
        <id>2002</id>
        <url>http://burzarada.hzz.hr/RadnoMjesto_Ispis.aspx?WebSifra=2002</url>
        <nazivRadnogMjesta>KUHAR/ICA</nazivRadnogMjesta>
        <opis>Prijavite se prema uputi u nastavku.</opis>
        <mjestoRada>ZAGREB</mjestoRada>
        <nacin_prijave>mailto:jobs (at) example dot hr</nacin_prijave>
      </radnoMjesto>
    </radnaMjesta>
    XML;

        Http::fake([
          'https://example.test/hzz-alt-email.xml' => Http::response($xml, 200, ['Content-Type' => 'application/xml']),
        ]);

        $summary = app(HzzJobImportService::class)->importFromUrl('https://example.test/hzz-alt-email.xml', false, false);

        $this->assertSame(1, $summary['created']);

        $job = Job::query()->firstOrFail();
        $this->assertSame('jobs@example.hr', $job->hzz_apply_email);
        $this->assertSame('email', $job->hzz_apply_contact_type);
        $this->assertTrue($job->hzz_apply_method_available);
      }
}
