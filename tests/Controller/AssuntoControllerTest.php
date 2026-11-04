<?php

namespace App\Tests\Controller;

use App\Entity\Assunto;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AssuntoControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    /** @var EntityRepository<Assunto> $assuntoRepository */
    private EntityRepository $assuntoRepository;
    private string $path = '/assunto/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->assuntoRepository = $this->manager->getRepository(Assunto::class);

        foreach ($this->assuntoRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Assunto index');
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'assunto[descricao]' => 'Testing',
        ]);

        self::assertResponseRedirects('/assunto');

        self::assertSame(1, $this->assuntoRepository->count([]));
    }

    public function testShow(): void
    {
        $fixture = new Assunto();
        $fixture->setDescricao('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Assunto');
    }

    public function testEdit(): void
    {
        $fixture = new Assunto();
        $fixture->setDescricao('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'assunto[descricao]' => 'Something New',
        ]);

        self::assertResponseRedirects('/assunto');

        $fixture = $this->assuntoRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getDescricao());
    }

    public function testRemove(): void
    {
        $fixture = new Assunto();
        $fixture->setDescricao('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/assunto');
        self::assertSame(0, $this->assuntoRepository->count([]));
    }
}
