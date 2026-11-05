<?php

namespace App\Tests\Controller;

use App\Entity\Autor;
use App\Repository\AutorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AutorControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    /** @var EntityRepository<Autor> $autorRepository */
    private EntityRepository $autorRepository;
    private string $path = '/autor/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->autorRepository = $this->manager->getRepository(Autor::class);

        foreach ($this->autorRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Autor index');

    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'autor[nome]' => 'Testing',
        ]);

        self::assertResponseRedirects('/autor');

        self::assertSame(1, $this->autorRepository->count([]));
    }

    public function testShow(): void
    {
        $fixture = new Autor();
        $fixture->setNome('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Autor');
    }

    public function testEdit(): void
    {
        $fixture = new Autor();
        $fixture->setNome('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'autor[nome]' => 'Something New',
        ]);

        self::assertResponseRedirects('/autor');

        $fixture = $this->autorRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getNome());
    }

    public function testRemove(): void
    {
        $fixture = new Autor();
        $fixture->setNome('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/autor');
        self::assertSame(0, $this->autorRepository->count([]));
    }
}
