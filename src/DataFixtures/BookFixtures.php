<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Subject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BookFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            SubjectFixtures::class,
            AuthorFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('pt_BR');

        $authors = $manager->getRepository(Author::class)->findAll();
        $subjects = $manager->getRepository(Subject::class)->findAll();

        if (empty($authors) || empty($subjects)) {
            dd($subjects);
            throw new \RuntimeException('Autores ou Assuntos não encontrados. Verifique a ordem das fixtures.');
        }

        for ($i = 0; $i < 300; $i++) {
            $book = new Book();

            $book->setTitle(mb_substr($faker->sentence(3), 0, 40));
            $book->setPublisher(mb_substr($faker->company(), 0, 40));
            $book->setEdition($faker->numberBetween(1, 200));
            $book->setPublicationYear($faker->numberBetween(1450, (int)date('Y') + 1));
            $book->setPrice($faker->randomFloat(2, 20, 250));

            $numAuthors = min(count($authors), $faker->numberBetween(1, 3));
            $randomAuthorKeys = (array) array_rand($authors, $numAuthors);
            foreach ($randomAuthorKeys as $key) {
                $book->addAuthor($authors[$key]);
            }

            $numSubjects = min(count($subjects), $faker->numberBetween(1, 3));
            $randomSubjectKeys = (array) array_rand($subjects, $numSubjects);
            foreach ($randomSubjectKeys as $key) {
                $book->addSubject($subjects[$key]);
            }

            $manager->persist($book);
        }

        $manager->flush();
    }
}
