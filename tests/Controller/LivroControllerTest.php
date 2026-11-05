<?php

namespace App\Tests\Controller;

use App\Entity\Livro;
use App\Repository\LivroRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LivroControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    /** @var EntityRepository<Livro> $livroRepository */
    private EntityRepository $livroRepository;
    private string $path = '/book/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->livroRepository = $this->manager->getRepository(Livro::class);

        foreach ($this->livroRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Livro index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'livro[titulo]' => 'Testing',
            'livro[editora]' => 'Testing',
            'livro[edicao]' => 1,
            'livro[anoPublicacao]' => 2026,
            'livro[valor]' => '9.99',
        ]);

        self::assertResponseRedirects('/livro');

        self::assertSame(1, $this->livroRepository->count([]));
    }

    public function testShow(): void
    {
        $fixture = new Livro();
        $fixture->setTitulo('My Title');
        $fixture->setEditora('My Title');
        $fixture->setEdicao(1);
        $fixture->setAnoPublicacao(2026);
        $fixture->setValor('9.99');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Livro');
    }

    public function testEdit(): void
    {
        $fixture = new Livro();
        $fixture->setTitulo('Value');
        $fixture->setEditora('Value');
        $fixture->setEdicao(1);
        $fixture->setAnoPublicacao(2026);
        $fixture->setValor('9.99');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'livro[titulo]' => 'Something New',
            'livro[editora]' => 'Something New',
            'livro[edicao]' => 2,
            'livro[anoPublicacao]' => 2027,
            'livro[valor]' => '10.99',
        ]);

        self::assertResponseRedirects('/livro');

        $fixture = $this->livroRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitulo());
        self::assertSame('Something New', $fixture[0]->getEditora());
        self::assertSame(2, $fixture[0]->getEdicao());
        self::assertSame(2027, $fixture[0]->getAnoPublicacao());
        self::assertSame('10.99', $fixture[0]->getValor());
    }

    public function testRemove(): void
    {
        $fixture = new Livro();
        $fixture->setTitulo('Value');
        $fixture->setEditora('Value');
        $fixture->setEdicao(1);
        $fixture->setAnoPublicacao(2026);
        $fixture->setValor('9.99');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/livro');
        self::assertSame(0, $this->livroRepository->count([]));

    }
}
