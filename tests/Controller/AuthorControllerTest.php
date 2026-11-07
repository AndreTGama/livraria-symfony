<?php

namespace App\Tests\Controller;

use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AuthorControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    /** @var EntityRepository<Author> $authorRepository */
    private EntityRepository $authorRepository;
    private string $path = '/autores/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->authorRepository = $this->manager->getRepository(Author::class);

        foreach ($this->authorRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Autores | Lista');
    }

    public function testNew(): void
    {
        $request = $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $form = $request->filter('form')->form([
            'author[name]' => 'Testing',
        ]);
        $this->client->submit($form);

        self::assertResponseRedirects('/autores');

        self::assertSame(1, $this->authorRepository->count([]));
    }

    public function testShow(): void
    {
        $fixture = new Author();
        $fixture->setName('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
    }

    public function testEdit(): void
    {
        $fixture = new Author();
        $fixture->setName('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $request = $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));
        $form = $request->filter('form')->first()->form([
            'author[name]' => 'Something New',
        ]);
        $this->client->submit($form);

        self::assertResponseRedirects('/autores');

        $fixture = $this->authorRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
    }

    public function testRemove(): void
    {
        $fixture = new Author();
        $fixture->setName('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $request = $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $form = $request->filter('form')->form();
        $this->client->submit($form);

        self::assertResponseRedirects('/autores');
        self::assertSame(0, $this->authorRepository->count([]));
    }
}
