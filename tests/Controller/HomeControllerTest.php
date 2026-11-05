<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomeControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h2', 'Livros');
        self::assertSelectorExists('form.catalog-filter');
    }

    public function testCatalogAcceptsCombinedFiltersAndPagination(): void
    {
        $client = static::createClient();
        $client->request('GET', '/?subjects%5B%5D=1&subjects%5B%5D=2&authors%5B%5D=3&q=historia&page=2');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form.catalog-filter');
    }
}
