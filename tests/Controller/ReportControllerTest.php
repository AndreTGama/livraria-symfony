<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ReportControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/relatorios');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form[action="/relatorios"]');
        self::assertSelectorTextContains('custom-button', 'Exportar planilha');
    }
}
