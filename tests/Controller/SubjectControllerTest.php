<?php

namespace App\Tests\Controller;

use App\Entity\Subject;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SubjectControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    /** @var EntityRepository<Subject> $subjectRepository */
    private EntityRepository $subjectRepository;
    private string $path = '/assuntos/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->subjectRepository = $this->manager->getRepository(Subject::class);

        foreach ($this->subjectRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $request = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Assuntos | lista');
    }

    public function testNew(): void
    {
        $request = $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $form = $request->filter('form')->form([
            'subject[description]' => 'Testing',
        ]);
        $this->client->submit($form);

        self::assertResponseRedirects('/assuntos');

        self::assertSame(1, $this->subjectRepository->count([]));
    }

    public function testShow(): void
    {
        $fixture = new Subject();
        $fixture->setDescription('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Assuntos');
    }

    public function testEdit(): void
    {
        $fixture = new Subject();
        $fixture->setDescription('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $request = $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));
        $form = $request->filter('form')->first()->form([
            'subject[description]' => 'Something New',
        ]);
        $this->client->submit($form);

        self::assertResponseRedirects('/assuntos');

        $fixture = $this->subjectRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getDescription());
    }

    public function testRemove(): void
    {
        $fixture = new Subject();
        $fixture->setDescription('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $request = $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $form = $request->filter('form')->form();
        $this->client->submit($form);

        self::assertResponseRedirects('/assuntos');
        self::assertSame(0, $this->subjectRepository->count([]));
    }
}
