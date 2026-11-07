<?php

namespace App\Tests\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Subject;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BookControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    /** @var EntityRepository<Book> $bookRepository */
    private EntityRepository $bookRepository;
    private Author $author;
    private Subject $subject;
    private string $path = '/livros/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->bookRepository = $this->manager->getRepository(Book::class);

        foreach ($this->bookRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();

        $this->author = new Author();
        $this->author->setName('Test Author');
        $this->subject = new Subject();
        $this->subject->setDescription('Testing');

        $this->manager->persist($this->author);
        $this->manager->persist($this->subject);
        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Livros | lista');
    }

    public function testNew(): void
    {
        $request = $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $form = $request->filter('form')->form([
            'book[title]' => 'Testing',
            'book[publisher]' => 'Testing',
            'book[edition]' => 1,
            'book[publicationYear]' => 2026,
            'book[price]' => '9.99',
            'book[authors]' => [$this->author->getId()],
            'book[subjects]' => [$this->subject->getId()],
        ]);
        $this->client->submit($form);

        self::assertResponseRedirects('/livros');

        self::assertSame(1, $this->bookRepository->count([]));
    }

    public function testShow(): void
    {
        $fixture = new Book();
        $fixture->setTitle('My Title');
        $fixture->setPublisher('My Publisher');
        $fixture->setEdition(1);
        $fixture->setPublicationYear(2026);
        $fixture->setPrice('9.99');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Livro');
    }

    public function testEdit(): void
    {
        $fixture = new Book();
        $fixture->setTitle('Value');
        $fixture->setPublisher('Value');
        $fixture->setEdition(1);
        $fixture->setPublicationYear(2026);
        $fixture->setPrice('9.99');
        $fixture->addAuthor($this->author);
        $fixture->addSubject($this->subject);

        $this->manager->persist($fixture);
        $this->manager->flush();

        $request = $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));
        $form = $request->filter('form')->first()->form([
            'book[title]' => 'Something New',
            'book[publisher]' => 'Something New',
            'book[edition]' => 2,
            'book[publicationYear]' => 2027,
            'book[price]' => '10.99',
        ]);
        $this->client->submit($form);

        self::assertResponseRedirects('/livros');

        $fixture = $this->bookRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getPublisher());
        self::assertSame(2, $fixture[0]->getEdition());
        self::assertSame(2027, $fixture[0]->getPublicationYear());
        self::assertSame('10.99', $fixture[0]->getPrice());
    }

    public function testRemove(): void
    {
        $fixture = new Book();
        $fixture->setTitle('Value');
        $fixture->setPublisher('Value');
        $fixture->setEdition(1);
        $fixture->setPublicationYear(2026);
        $fixture->setPrice('9.99');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $request = $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $form = $request->filter('form')->form();
        $this->client->submit($form);

        self::assertResponseRedirects('/livros');
        self::assertSame(0, $this->bookRepository->count([]));
    }
}
